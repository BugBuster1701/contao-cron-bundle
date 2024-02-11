<?php

declare(strict_types=1);

namespace BugBuster\CronBundle\Cron;

use BugBuster\Cron\CronRequest;
use Contao\Config;
use Contao\CoreBundle\DependencyInjection\Attribute\AsCronJob;
// use Symfony\Component\HttpFoundation\RequestStack;
use Contao\CoreBundle\Framework\ContaoFramework;
// use Symfony\Component\Filesystem\Path;
// use Symfony\Component\Finder\Finder;
use Contao\Environment;
use Contao\StringUtil;
// use Doctrine\DBAL\Types\Types;
use Contao\System;
use Cron\CronExpression;
// use Contao\CoreBundle\Util\LocaleUtil;
// use Contao\CoreBundle\Cron\Cron;
// use Contao\CoreBundle\Exception\CronExecutionSkippedException;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ParameterType;
use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AsCronJob('minutely')]
class StartJobsCron
{
    /**
     * Job Constants.
     */
    const JOB_TYPE_FILE = 1;

    const JOB_TYPE_ROUTE = 2;

    const JOB_TYPE_URL = 3;

    /**
     * stop processung jobs in one trigger after this time in seconds
     * can be overwritten.
     *
     * @var int
     */
    private $cron_max_run = 10;

    public function __construct(
        private readonly Filesystem $filesystem,
        private readonly string $projectDir,
        private readonly LoggerInterface|null $logger,
        private readonly Connection $connection,
        private readonly TranslatorInterface $translator,
        private readonly ContaoFramework $framework,
    ) {
    }

    public function __invoke(string $scope): void
    {
        // SCOPE_WEB = 'web';
        // SCOPE_CLI = 'cli';
        // Skip this cron job in the web scope
        // if (Cron::SCOPE_WEB === $scope) {
        //      throw new CronExecutionSkippedException();
        // }

        // de, wenn BE Deutsch ist
        // $tll = $GLOBALS['TL_LANGUAGE'];
        // $this->logger?->info('TL_LANGUAGE: '.$tll);

        // $this->translator->getLocale(); // bringt 'en' zurück statt 'de', wenn BE Deutsch ist.
        // $translang = $this->translator->getLocale();
        // $this->logger?->info('Translang: '.$translang);

        // $strLanguage = LocaleUtil::formatAsLocale($GLOBALS['TL_LANGUAGE'] ?? 'en');
        // if (!$strLanguage)
        // {
        // 	$strLanguage = 'en';
        // }

        // $text = $this->translator->trans('tl_crontab.route_not_exists',[] , 'contao_tl_crontab', 'en');
        // $this->logger?->info($text);

        // \Contao\System::loadLanguageFile('tl_crontab');
        // $this->logger?->info($GLOBALS['TL_LANG']['tl_crontab']['route_not_exists'] . ' '.$strLanguage . ' GLOBAL '.$GLOBALS['TL_LANGUAGE']);

        // $this->logger?->info('Start: StartJobsCron');
        $this->runJobs();
        // $this->logger?->info('End: StartJobsCron');
    }

    public function runJobs(): void
    {
        // $this->logger?->info(__METHOD__ .' '. __LINE__);
        global $cronJob;

        $currtime = time();

        $this->framework->initialize();
        $config = $this->framework->getAdapter(Config::class);
        $cron_limit = $config->get('cron_limit');
        if (null !== $cron_limit) {
            $this->cron_max_run = (int) $cron_limit;
        }

        $stmt = $this->connection->prepare("
            SELECT * FROM `tl_crontab`
            WHERE `enabled`= '1'
            AND (
                (`nextrun`>0 and `nextrun`< :currtime)
            OR (`nextrun`=0 and `scheduled`< :scheduled)
                )
            ORDER BY `nextrun`, `scheduled`
        ");
        $stmt->bindValue('currtime', $currtime, ParameterType::INTEGER);
        $stmt->bindValue('scheduled', $currtime - 86400, ParameterType::INTEGER);
        $resultSet = $stmt->executeQuery();
        // $rowCount = $stmt->executeStatement(); // insert,update,delete
        $rows = $resultSet->fetchAllAssociative();

        $locked = false;
        $endtime = time() + $this->cron_max_run;

        /** @var array<string, mixed>|false $row[] */
        foreach ($rows as $row) {
            // $this->logger?->info(__METHOD__ .' '. __LINE__);
            $currtime = time();
            if ($currtime >= $endtime) {
                break;
            }

            if (!$locked) {
                // ensure exclusive access
                $stmt2 = $this->connection->prepare("SELECT get_lock('cronlock',0) AS lockstate");
                $lockstate = $stmt2->executeQuery()->fetchOne();

                if (!$lockstate || !(int) $lockstate) {
                    return; // Abbruch
                }
                $locked = true;
            } // if
            if ((int) $row['nextrun'] > 0) {
                $cronJob = [
                    'id' => $row['id'],
                    'title' => $row['title'],
                    'lastrun' => $row['lastrun'],
                    'endtime' => $endtime,
                    'runonce' => (int) $row['runonce'] > 0,
                    'logging' => (int) $row['logging'] > 0,
                    'completed' => true,
                ];

                $output = $this->runJob($row);

                if ($cronJob['completed']) {
                    // $this->logger?->info(__METHOD__ .' '. __LINE__.' complete');
                    if ($cronJob['runonce']) {
                        // $this->logger?->info(__METHOD__ .' '. __LINE__.' complete and runonce');
                        $dataset = [
                            $currtime,
                            0,
                            0,
                            '0',
                        ];
                        $types = [
                            'lastrun' => ParameterType::INTEGER,
                            'nextrun' => ParameterType::INTEGER,
                            'scheduled' => ParameterType::INTEGER,
                            'enabled' => ParameterType::STRING,
                        ];
                    } else {
                        // $this->logger?->info(__METHOD__ .' '. __LINE__.' complete and not runonce');
                        $dataset = [
                            $currtime,
                            $this->getNextRun($row),
                            $currtime,
                            '1',
                        ];
                        $types = [
                            'lastrun' => ParameterType::INTEGER,
                            'nextrun' => ParameterType::INTEGER,
                            'scheduled' => ParameterType::INTEGER,
                            'enabled' => ParameterType::STRING,
                        ];
                    }
                    // $this->logger?->info(__METHOD__ .' '. __LINE__.' dataset '. print_r($dataset,true));
                    $id = $row['id'];
                    $sqlUpdate = 'UPDATE `tl_crontab` SET `lastrun`=?, `nextrun`=?, `scheduled`=?, `enabled`=? WHERE id='.$row['id'];
                    // $this->logger?->info(__METHOD__ .' '. __LINE__.' SQL '. $sqlUpdate);
                    $this->connection->executeStatement($sqlUpdate, $dataset, $types);

                    if ($cronJob['logging']) {
                        if ('' !== $output) {
                            $this->logger?->error('Cron job '.$row['title'].': failed: '.strip_tags($output));
                        } else {
                            $this->logger?->info('Cron job '.$row['title'].': '.($cronJob['completed'] ? 'completed.' : 'processed partially.'));
                        }
                    } // if
                } // if
            } else {
                $dataset = [
                    'nextrun' => $this->getNextRun($row),
                    'scheduled' => $currtime,
                ];
                $types = [
                    'nextrun' => ParameterType::INTEGER,
                    'scheduled' => ParameterType::INTEGER,
                ];
                $id = $row['id'];
                $sqlUpdate = "UPDATE `tl_crontab` SET `nextrun`=?, `scheduled`=? WHERE id=$id";
                $this->connection->executeStatement($sqlUpdate, $dataset, $types);
            }
        } // foreach

        // release lock
        if ($locked) {
            $stmtlocked = $this->connection->prepare("SELECT release_lock('cronlock')");
            $stmtlocked->executeQuery();
        }
    }

    /**
     * Run job and return the captured output.
     */
    private function runJob(&$qjob)
    {
        // $this->logger?->info(__METHOD__ .' '. __LINE__);
        $jobtype = $this->getJobType($qjob['job']);

        switch ($jobtype) {
            case self::JOB_TYPE_FILE:
                return $this->runFileJob($qjob);
                break;
            case self::JOB_TYPE_ROUTE:
                return $this->runRouteJob($qjob);
                break;
            case self::JOB_TYPE_URL:
                return $this->runUrlJob($qjob);
                break;

            default:
                return;
                break;
        }
    }

    /**
     * Get the Job Type.
     *
     * @param string $strJob
     *
     * @return int 1: File, 2: Route 3: URL
     */
    private function getJobType($strJob)
    {
        // $this->logger?->info(__METHOD__ .' '. __LINE__);
        if ('http:' === substr($strJob, 0, 5) || 'https:' === substr($strJob, 0, 6)) {
            return self::JOB_TYPE_URL;
        }

        if ('.php' === substr($strJob, -4)) {
            return self::JOB_TYPE_FILE;
        }

        return self::JOB_TYPE_ROUTE; // I hope :-)
    }

    private function getNextRun($qjob): int
    {
        // $this->logger?->info(__METHOD__ .' '. __LINE__);
        $dowNum =
        str_ireplace(
            ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
            [0, 1, 2, 3, 4, 5, 6],
            $qjob['t_dow'],
        );
        $monthNum =
        str_ireplace(
            ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12],
            $qjob['t_month'],
        );
        $crontab = sprintf('%s %s %s %s %s', $qjob['t_minute'], $qjob['t_hour'], $qjob['t_dom'], $monthNum, $dowNum);
        $cron = CronExpression::factory($crontab);

        return (int) $cron->getNextRunDate()->format('U');
    }

    /**
     * Run job and return the captured output.
     */
    private function runFileJob(&$qjob)
    {
        // $this->logger?->info(__METHOD__ .' '. __LINE__);
        global $cronJob;
        // File exists and readable?
        if (!is_readable($this->projectDir.'/'.$qjob['job'])) {
            $cronJob['completed'] = false;

            return $this->translator->trans('tl_crontab.file_not_readable', [], 'contao_tl_crontab', 'en');
        }

        ob_start();
        $e = error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED & ~E_USER_DEPRECATED);
        include $this->projectDir.'/'.$qjob['job'];
        error_reporting($e);

        $cronJob = [
            'id' => $qjob['id'],
            'title' => $qjob['title'],
            'lastrun' => $qjob['lastrun'],
            'endtime' => time(),
            'runonce' => (int) $qjob['runonce'] > 0,
            'logging' => (int) $qjob['logging'] > 0,
            'completed' => true,
        ];

        return str_replace("\n", '<br>', trim(preg_replace('#<\s*br\s*//*?\s*>#i', "\n", ob_get_flush())));
    } // runJob

    /**
     * Run route job and return the captured output.
     */
    private function runRouteJob($qjob)
    {
        // $this->logger?->info(__METHOD__ .' '. __LINE__);
        global $cronJob;

        // @var Router $router
        $router = System::getContainer()->get('router');

        // Trennung Parameter im alten Stil: ?abcde.. (BackupDB Spam Schutz)
        $arrFragments = StringUtil::trimsplit('?', $qjob['job']);
        $arrRoute = $router->match($arrFragments[0]);

        if ('contao_catch_all' === $arrRoute['_route']) {
            return $this->translator->trans('tl_crontab.route_not_exists', [], 'contao_tl_crontab', 'en').' ('.$qjob['job'].')';
        }

        $url = Environment::get('base').ltrim($qjob['job'], '/');

        try {
            $request = new CronRequest($url, (int) $qjob['expert_timeout']);
        } catch (\Exception $e) {
            $cronJob['completed'] = false;

            return;
        }

        $StatusCode = $request->get();

        if (200 === $StatusCode) {
            $cronJob['completed'] = true;

            return;
        }
        $cronJob['completed'] = false;

        return $StatusCode.'::'.$request->getResponseBody();
    }

    /**
     * Run URL job and return the captured output.
     */
    private function runUrlJob($qjob)
    {
        // $this->logger?->info(__METHOD__ .' '. __LINE__ .' '.$qjob['job']);
        global $cronJob;

        try {
            $request = new CronRequest($qjob['job'], (int) $qjob['expert_timeout']);
        } catch (\Exception $e) {
            $cronJob['completed'] = false;

            return;
        }

        $StatusCode = $request->get();

        if (200 === $StatusCode) {
            $cronJob['completed'] = true;
            // $this->logger?->info(__METHOD__ .' '. __LINE__ .' StatusCode '.$StatusCode);

            return;
        }
        $cronJob['completed'] = false;
        // $this->logger?->info(__METHOD__ .' '. __LINE__ .' StatusCode '.$StatusCode);

        return $StatusCode.'::'.$request->getResponseBody();
    }
}

// vendor/bin/contao-console debug:container --tag contao.cronjob

<?php

/**
 * @copyright  Glen Langer 2018 <http://contao.ninja>
 * @author     Glen Langer (BugBuster)
 * @license    LGPL-3.0+
 */

namespace BugBuster\Cron;

use BugBuster\CronBundle\Services\JobTypesInterface;
use Contao\System;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Contao\Input;
use Contao\Database;
use Contao\Environment;
use Psr\Log\LogLevel;
use Contao\CoreBundle\Monolog\ContaoContext;
use BugBuster\Cron\CronRequest;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

/**
 * Back end
 *
 * @author     Glen Langer (BugBuster)
 */
class ContaoBackendController extends \Backend implements JobTypesInterface
{

    /**
     * Initialize the controller
     *
     * 1. Import the user
     * 2. Call the parent constructor
     * 3. Authenticate the user
     * 4. Load the language files
     * DO NOT CHANGE THIS ORDER!
     */
    public function __construct()
    {
        $this->import('BackendUser', 'User');
        parent::__construct();

        $this->User->authenticate();

        \System::loadLanguageFile('default');
        \System::loadLanguageFile('tl_crontab');

    }


    /**
     * Run the controller and parse the template
     *
     * @return Response
     */
    public function runJobNow()
    {
        /** @var BackendTemplate|object $objTemplate */
        $objTemplate = new \BackendTemplate('mod_cron_start_now');

        $output = '';
        $strEncypt = Input::get('crcst');
        $jobId = substr($strEncypt, 6); //TODO

        $GLOBALS['TL_CONFIG']['debugMode'] = false;

        $q = Database::getInstance()->prepare("SELECT * FROM `tl_crontab` WHERE id=?")->execute($jobId);

        if ($q->numRows > 0) {
            $objTemplate->cronjob = $q->job;
            $objTemplate->cronlogtitle = $q->title;
            $objTemplate->start_time = time();

            \System::getContainer()
                ->get('monolog.logger.contao')
                ->log(LogLevel::INFO,
                    'Running scheduler job manually',
                    ['contao' => new ContaoContext('ContaoBackendController run()', TL_CRON)]);

            $output .= sprintf("[%s] %s<br>", date('d-M-Y H:i:s'), 'Running scheduler job manually');
            $output .= '::' . $this->runJob($q) . '<br>';

            \System::getContainer()
                ->get('monolog.logger.contao')
                ->log(LogLevel::INFO,
                    'Manually scheduler job complete',
                    ['contao' => new ContaoContext('ContaoBackendController run()', TL_CRON)]);

            $output .= sprintf("[%s] %s<br>", date('d-M-Y H:i:s'), 'Manually scheduler job complete');
        } else {
            $output .= '<br>Job not found!';
        }

        $objTemplate->cronlog = $output;
        $objTemplate->theme = $this->getTheme();
        $objTemplate->base = Environment::get('base');
        $objTemplate->language = $GLOBALS['TL_LANGUAGE'];
        $objTemplate->title = 'CronRunJobNow';
        $objTemplate->charset = $GLOBALS['TL_CONFIG']['characterSet'];

        return $objTemplate->getResponse(); // compile and new Response()...
    }

    /**
     * Run job and return the captured output
     */
    private function runJob(&$qjob)
    {
        if (!$qjob->job_type) {
            $jobtype = $this->getJobType($qjob->job);
        } else {
            $jobtype = $qjob->job_type;
        }

        switch ($jobtype) {
            case self::JOB_TYPE_FILE :
                return $this->runFileJob($qjob);
                break;
            case self::JOB_TYPE_ROUTE :
                return $this->runRouteJob($qjob);
                break;
            case self::JOB_TYPE_URL :
                return $this->runUrlJob($qjob);
                break;
            case self::JOB_TYPE_COMMAND:
                return $this->runCommandJob($qjob);
                break;

            default:
                return;
                break;
        }
    }

    /**
     * Get the Job Type
     * @param string $strJob
     * @return  int     1: File, 2: Route 3: URL
     */
    private function getJobType($strJob)
    {
        if ('http:' == substr($strJob, 0, 5) || 'https:' == substr($strJob, 0, 6)) {
            return self::JOB_TYPE_URL;
        }

        if ('.php' == substr($strJob, -4)) {
            return self::JOB_TYPE_FILE;
        }
        return self::JOB_TYPE_ROUTE; // I hope :-)
    }

    /**
     * Run route job and return the captured output
     */
    private function runRouteJob($strJob)
    {
        /* @var Router $router */
        $router = \System::getContainer()->get('router');

        //Trennung Parameter im alten Stil: ?abcde.. (BackupDB Spam Schutz)
        $arrFragments = \StringUtil::trimsplit('?', $strJob->job);
        $arrRoute = $router->match($arrFragments[0]);

        if ('contao_catch_all' == $arrRoute['_route']) {
            return '<span style="color:red;">' . $GLOBALS['TL_LANG']['tl_crontab']['route_not_exists'] . "</span> ($strJob->job)";
        }

        $url = Environment::get('base') . ltrim($strJob->job, '/');

        try {
            $request = new CronRequest($url);
        } catch (\Exception $e) {
            return '<span style="color:red;">500::' . $e->getMessage() . '</span>';
        }

        return $request->get() . '::' . $request->getResponseBody();
    }

    /**
     * Run URL job and return the captured output
     */
    private function runUrlJob($strJob)
    {
        try {
            $request = new CronRequest($strJob->job);
        } catch (\Exception $e) {
            return '<span style="color:red;">500::' . $e->getMessage() . '</span>';
        }

        return $request->get() . '::' . $request->getResponseBody();
    }

    /**
     * Run file job and return the captured output
     */
    private function runFileJob($qjob)
    {
        global $cronJob;

        $limit = 5;
        if (isset($GLOBALS['TL_CONFIG']['cron_limit'])) {
            $limit = intval($GLOBALS['TL_CONFIG']['cron_limit']);
        }
        if ($limit <= 0) {
            return;
        }

        //File exists and readable?
        if (!is_readable(TL_ROOT . '/' . $qjob->job)) {
            return '<span style="color:red;">' . $GLOBALS['TL_LANG']['tl_crontab']['file_not_readable'] . "</span> ($qjob->job)";
        }

        $currtime = time();
        $endtime = $currtime + $limit;
        $cronJob = [
            'id' => $qjob->id,
            'title' => $qjob->title,
            'lastrun' => $qjob->lastrun,
            'endtime' => $endtime,
            'runonce' => intval($qjob->runonce) > 0,
            'logging' => intval($qjob->logging) > 0,
            'completed' => true,
        ];
        ob_start();
        $e = error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED & ~E_USER_DEPRECATED);
        include(TL_ROOT . '/' . $qjob->job);
        error_reporting($e);
        return str_replace("\n", '<br>', trim(preg_replace('#<\s*br\s*/?\s*>#i', "\n", ob_get_flush())));
    } // runJob


    private function runCommandJob($qjob)
    {
        global $cronJob;

        $limit = 5;

        if (isset($GLOBALS['TL_CONFIG']['cron_limit'])) {
            $limit = intval($GLOBALS['TL_CONFIG']['cron_limit']);
        }

        if ($limit <= 0) {
            return;
        }

        $currtime = time();
        $endtime = $currtime + $limit;
        $cronJob = [
            'id' => $qjob->id,
            'title' => $qjob->title,
            'lastrun' => $qjob->lastrun,
            'endtime' => $endtime,
            'runonce' => intval($qjob->runonce) > 0,
            'logging' => intval($qjob->logging) > 0,
            'completed' => true,
        ];

        $rootDir = System::getContainer()->getParameter('kernel.project_dir');

        self::executeCommand('cd ' . $rootDir . ' && php bin/console ' . $qjob->job);
    }

    protected static function executeCommand($command)
    {
        $process = new Process($command);
        $process->run();

        // executes after the command finishes
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        echo $process->getOutput();
    }
}

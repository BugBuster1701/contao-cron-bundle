<?php

declare(strict_types=1);

/**
 * @copyright  Glen Langer 2018 <http://contao.ninja>
 * @author     Glen Langer (BugBuster)
 * @license    LGPL-3.0+
 */

namespace BugBuster\Cron;

use BugBuster\Cron\CronRequest;
use Contao\CoreBundle\Monolog\ContaoContext;
use Contao\Database;
use Contao\Environment;
use Psr\Log\LogLevel;
use Symfony\Component\HttpFoundation\Response;

/**
 * Back end 
 *
 * @author     Glen Langer (BugBuster)
 */
class ContaoFrontendController extends \Contao\Frontend
{

    /**
     * stop processung jobs in one trigger after this time in seconds
     * can be overwritten
     *
     * @var integer
     */
    private $cron_max_run = 5;

    /**
     * Job Constants
     * @var integer
     */
    const JOB_TYPE_FILE  = 1;
    const JOB_TYPE_ROUTE = 2;
    const JOB_TYPE_URL   = 3;

	/**
	 * Initialize the controller
	 */
	public function __construct()
	{
		parent::__construct();

		\Contao\System::loadLanguageFile('tl_crontab');
	}

	/**
	 * Run the controller and parse the template
	 *
	 * @return Response
	 */
	public function runJobs()
	{
	    global $cronJob;

	    $objResponse = new Response();

	    $limit = $this->cron_max_run;
        if (isset($GLOBALS['TL_CONFIG']['cron_limit'])) 
	    {
	        $limit = (int) ($GLOBALS['TL_CONFIG']['cron_limit']);
	    }

	    // Do not run if there is POST data 
	    if (!empty($_POST) || $limit <= 0)
	    {
	        return $objResponse;
	    }
	    $currtime = time();

	    // process cron list
	    $q = Database::getInstance()->prepare("SELECT * FROM `tl_crontab`
                                                WHERE `enabled`='1'
                                                AND (
                                                      (`nextrun`>0 and `nextrun`<?)
                                                   OR (`nextrun`=0 and `scheduled`<?)
                                                    )
                                                ORDER BY `nextrun`, `scheduled`")
                                    ->execute($currtime, $currtime-86400);
        $locked  = false;
        $endtime = time() + $limit;

        while ($q->next())
        {
            $currtime = time();
            if ($currtime >= $endtime)
            {
                break;
            }
            if (!$locked)
            {
                // ensure exclusive access
                $ql = Database::getInstance()->prepare("SELECT get_lock('cronlock',0) AS lockstate")->execute();
                if (!$ql->next() || !(int) ($ql->lockstate))
                {
                    return $objResponse;
                }
                $locked = true;
            } // if
            if ($q->nextrun>0)
            { // due to execute
                $cronJob = array(
                    'id'		=> $q->id,
                    'title'		=> $q->title,
                    'lastrun'	=> $q->lastrun,
                    'endtime'	=> $endtime,
                    'runonce'	=> (int) ($q->runonce) > 0,
                    'logging'	=> (int) ($q->logging) > 0,
                    'completed'	=> true
                );

                $output = $this->runJob($q);

                if ($cronJob['completed'])
                {
                    if ($cronJob['runonce'])
                    {
                        $dataset = array(
                            'lastrun'	=> $currtime,
                            'nextrun'	=> 0,
                            'scheduled'	=> 0,
                            'enabled'	=> '0'
                        );
                    }
                    else
                    {
                        $dataset = array(
                            'lastrun'	=> $currtime,
                            'nextrun'	=> $this->getNextRun($q),
                            'scheduled'	=> $currtime
                        );
                    }
                    Database::getInstance()->prepare("UPDATE `tl_crontab` %s WHERE id=?")
                                            ->set($dataset)
                                            ->execute($q->id);
                } // if
                if ($cronJob['logging'])
                {
                    if ($output!='')
                    {
                        \Contao\System::getContainer()
                                ->get('monolog.logger.contao')
                                ->log(LogLevel::ERROR,
                                    'Cron job '.$q->title.' failed: '.strip_tags($output),
                                    array('contao' => new ContaoContext('ContaoFrontendController runJobs()', ContaoContext::ERROR)));
                    }
                    else
                    {
                        \Contao\System::getContainer()
                                ->get('monolog.logger.contao')
                                ->log(LogLevel::ERROR,
                                    'Cron job '.$q->title.' '.($cronJob['completed'] ? 'completed.' : 'processed partially.'),
                                    array('contao' => new ContaoContext('ContaoFrontendController runJobs()', ContaoContext::GENERAL)));
                    }
                } // if
            }
            else
            {
                $dataset = array(
                    'nextrun'	=> $this->getNextRun($q),
                    'scheduled'	=> $currtime
                );
                Database::getInstance()->prepare("UPDATE `tl_crontab` %s WHERE id=?")
                                        ->set($dataset)
                                        ->execute($q->id);
            } // if
        } // while

        // release lock
        if ($locked)
        {
            Database::getInstance()->prepare("SELECT release_lock('cronlock')")->execute();
        }

        return $objResponse;	    
	}

	/**
	 * Run job and return the captured output
	 */
	private function runJob(&$qjob)
	{
	    $jobtype = $this->getJobType($qjob->job);

    	switch ($jobtype)
    	{
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
	 * Get the Job Type
	 * @param  string $strJob
	 * @return int    1: File, 2: Route 3: URL
	 */
	private function getJobType($strJob)
	{
	    if ('http:' == substr($strJob, 0, 5) || 'https:' == substr($strJob, 0, 6))
	    {
	        return self::JOB_TYPE_URL;
	    }

	    if ('.php' == substr($strJob, -4))
	    {
	        return self::JOB_TYPE_FILE;
	    }

	    return self::JOB_TYPE_ROUTE; // I hope :-)
	}

	/**
	 * Run route job and return the captured output
	 */
	private function runRouteJob($qjob)
	{
	    global $cronJob;

        // @var Router $router
	    $router = \Contao\System::getContainer()->get('router');

	    //Trennung Parameter im alten Stil: ?abcde.. (BackupDB Spam Schutz)
	    $arrFragments = \Contao\StringUtil::trimsplit('?', $qjob->job);
	    $arrRoute = $router->match($arrFragments[0]);

	    if ('contao_catch_all' == $arrRoute['_route']) 
	    {
	        return $GLOBALS['TL_LANG']['tl_crontab']['route_not_exists'] . " ($qjob->job)";
	    }

	    $url = Environment::get('base') . ltrim($qjob->job, '/');

	    try
	    {
	       $request = new CronRequest($url, (int) $qjob->expert_timeout);
	    } 
	    catch (\Exception $e) 
	    {
	        $cronJob['completed'] = false;

	        return;
	    }

	    $StatusCode = $request->get();

	    if (200 == $StatusCode) 
	    {
	        $cronJob['completed'] = true;

	        return;
	    }
	    $cronJob['completed'] = false;

	    return $StatusCode . "::" . $request->getResponseBody(); 
	}

	/**
	 * Run URL job and return the captured output
	 */
	private function runUrlJob($qjob)
	{
	    global $cronJob;

	    try
	    {
	       $request = new CronRequest($qjob->job, (int) $qjob->expert_timeout);
	    } 
	    catch (\Exception $e) 
	    {
	        $cronJob['completed'] = false;

	        return;
	    }

	    $StatusCode = $request->get();

	    if (200 == $StatusCode) 
	    {
	        $cronJob['completed'] = true;

	        return;
	    }
	    $cronJob['completed'] = false;

	    return $StatusCode . "::" . $request->getResponseBody();  
	}

	/**
	 * Run job and return the captured output
	 */
	private function runFileJob(&$qjob)
	{
		$rootDir = \Contao\System::getContainer()->getParameter('kernel.project_dir');
	    //File exists and readable?
	    if (!is_readable($rootDir . '/' . $qjob->job))
	    {
	        return $GLOBALS['TL_LANG']['tl_crontab']['file_not_readable'];
	    }

	    ob_start();
	    $e = error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED & ~E_USER_DEPRECATED);
	    include($rootDir . '/' . $qjob->job);
	    error_reporting($e);

	    return str_replace("\n", '<br>', trim(preg_replace('#<\s*br\s*//*?\s*>#i', "\n", ob_get_flush())));
	} // runJob

	private function getNextRun($qjob) : int
    {
        $dowNum =
        str_ireplace(
            array('Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'),
            array(0, 1, 2, 3, 4, 5, 6),
            $qjob->t_dow
            );
        $monthNum =
        str_ireplace(
            array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'),
            array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12),
            $qjob->t_month
            );
        $crontab = sprintf('%s %s %s %s %s', $qjob->t_minute, $qjob->t_hour, $qjob->t_dom, $monthNum, $dowNum);
        $cron = \Cron\CronExpression::factory($crontab);

        return (int) $cron->getNextRunDate()->format('U');

	}

}

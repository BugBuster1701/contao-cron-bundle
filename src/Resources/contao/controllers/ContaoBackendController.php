<?php

/**
 * @copyright  Glen Langer 2020 <http://contao.ninja>
 * @author     Glen Langer (BugBuster)
 * @license    LGPL-3.0+
 */

namespace BugBuster\Cron;

use BugBuster\Cron\CronRequest;
use Contao\CoreBundle\Monolog\ContaoContext;
use Contao\Database;
use Contao\Environment;
use Contao\Input;
use Psr\Log\LogLevel;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\Response;

/**
 * Back end 
 *
 * @author     Glen Langer (BugBuster)
 */
class ContaoBackendController extends \Backend
{

    /**
     * Job Constants
     * @var integer
     */
    const JOB_TYPE_FILE  = 1;
    const JOB_TYPE_ROUTE = 2;
	const JOB_TYPE_URL   = 3;

	private $jobreturncode = 200;

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
		$outputrun = '';
		$strEncypt = Input::get('crcst');
		$jobId = substr($strEncypt, 6); //TODO

		$GLOBALS['TL_CONFIG']['debugMode'] = false;

		$q = Database::getInstance()->prepare("SELECT * FROM `tl_crontab`
                                                WHERE id=?
                                            ")
                                    ->execute($jobId);
        if ($q->numRows > 0)
        {
            $objTemplate->cronjob = $q->job;
            $objTemplate->cronlogtitle = $q->title;
            $objTemplate->start_time = time();

            //$this->log('Running scheduler job manually', 'CronStart run()', TL_CRON);
            \System::getContainer()
                ->get('monolog.logger.contao')
                ->log(LogLevel::INFO,
                      'Running scheduler job manually',
                      array('contao' => new ContaoContext('ContaoBackendController run()', TL_CRON)));

            $output .= sprintf("[%s] %s<br>", date('d-M-Y H:i:s'), 'Running scheduler job manually');
            $outputrun = '::'.$this->runJob($q);
			//$this->log('Manually scheduler job complete', 'CronStart run()', TL_CRON);
			if (200 == $this->jobreturncode)
			{
				\System::getContainer()
					->get('monolog.logger.contao')
					->log(LogLevel::INFO,
						'Manually scheduler job complete',
						array('contao' => new ContaoContext('ContaoBackendController run()', TL_CRON)));
			}
			else 
			{
				\System::getContainer()
					->get('monolog.logger.contao')
					->log(LogLevel::ERROR,
						'Manually scheduler job not complete '.strip_tags($outputrun),
						array('contao' => new ContaoContext('ContaoBackendController run()', TL_ERROR)));
			}
			$output .= $outputrun . '<br>';
            $output .= sprintf("[%s] %s<br>", date('d-M-Y H:i:s'), 'Manually scheduler job complete');
        }
        else
        {
            $output .= '<br>Job not found!';
        }

        $objTemplate->cronlog  = $output;
        $objTemplate->theme    = $this->getTheme();
        $objTemplate->base     = Environment::get('base');
        $objTemplate->language = $GLOBALS['TL_LANGUAGE'];
        $objTemplate->title    = 'CronRunJobNow';
        $objTemplate->charset  = $GLOBALS['TL_CONFIG']['characterSet'];

		return $objTemplate->getResponse(); // compile and new Response()...
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
	    // @var Router $router
	    $router = \System::getContainer()->get('router');

	    //Trennung Parameter im alten Stil: ?abcde.. (BackupDB Spam Schutz)
	    $arrFragments = \StringUtil::trimsplit('?', $qjob->job);
	    $arrRoute = $router->match($arrFragments[0]);

	    if ('contao_catch_all' == $arrRoute['_route']) 
	    {
	        return '<span style="color:red;">'.$GLOBALS['TL_LANG']['tl_crontab']['route_not_exists'] . "</span> ($qjob->job)";
	    }

	    $url = Environment::get('base') . ltrim($qjob->job, '/');

	    try 
	    {
	        $request = new CronRequest($url, (int) $qjob->expert_timeout);
	    } 
	    catch (\Exception $e) 
	    {
	        return '<span style="color:red;">500::' . $e->getMessage() . '</span>';
	    }
		$request->get();
		$this->jobreturncode = $request->getResponseStatusCode();

	    return $this->jobreturncode . '::' . $request->getResponseBody(); 
	}

	/**
	 * Run URL job and return the captured output
	 */
	private function runUrlJob($qjob)
	{
	    try
	    {
	       $request = new CronRequest($qjob->job, (int) $qjob->expert_timeout);
	    }
	    catch (\Exception $e) 
	    {
	        return '<span style="color:red;">500::' . $e->getMessage() . '</span>';
		}

		$request->get();
		$this->jobreturncode = $request->getResponseStatusCode();

	    return $this->jobreturncode . '::' . $request->getResponseBody();
	}

	/**
	 * Run file job and return the captured output
	 */
	private function runFileJob($qjob)
	{
	    global  $cronJob;

	    $limit = 5;
	    if (isset($GLOBALS['TL_CONFIG']['cron_limit']))
	    {
	        $limit = (int) ($GLOBALS['TL_CONFIG']['cron_limit']);
	    }
	    if ($limit<=0)
	    {
	        return;
		}

	    //File exists and readable?
	    if (!is_readable(TL_ROOT . '/' . $qjob->job)) 
	    {
	        return '<span style="color:red;">'.$GLOBALS['TL_LANG']['tl_crontab']['file_not_readable'] . "</span> ($qjob->job)";
	    }

	    $currtime = time();
	    $endtime  = $currtime+$limit;
	    $cronJob = array(
	        'id'		=> $qjob->id,
	        'title'		=> $qjob->title,
	        'lastrun'	=> $qjob->lastrun,
	        'endtime'	=> $endtime,
	        'runonce'	=> (int) ($qjob->runonce) > 0,
	        'logging'	=> (int) ($qjob->logging) > 0,
	        'completed'	=> true
	    );
	    ob_start();
	    $e = error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED & ~E_USER_DEPRECATED);
	    include(TL_ROOT . '/' . $qjob->job);
	    error_reporting($e);

	    return str_replace("\n", '<br>', trim(preg_replace('#<\s*br\s*/?\s*>#i', "\n", ob_get_flush())));
	} // runJob

}

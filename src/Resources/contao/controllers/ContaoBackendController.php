<?php

/**
 * @copyright  Glen Langer 2018 <http://contao.ninja>
 * @author     Glen Langer (BugBuster)
 * @license    LGPL-3.0+
 */

namespace BugBuster\Cron;

use Symfony\Component\HttpFoundation\Response;
use Contao\Input;
use Contao\Database;
use Psr\Log\LogLevel;
use Contao\CoreBundle\Monolog\ContaoContext;

/**
 * Back end 
 *
 * @author     Glen Langer (BugBuster)
 */
class ContaoBackendController extends \Backend
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
		
		$q = Database::getInstance()->prepare("SELECT * FROM `tl_crontab`
                                                WHERE id=?
                                            ")
                                    ->execute($jobId);
        if ( $q->numRows > 0 )
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
            $output .= '::'.$this->runJob($q).'<br>';
            //$this->log('Manually scheduler job complete', 'CronStart run()', TL_CRON);
            \System::getContainer()
                ->get('monolog.logger.contao')
                ->log(LogLevel::INFO,
                      'Manually scheduler job complete',
                      array('contao' => new ContaoContext('ContaoBackendController run()', TL_CRON)));
            
            $output .= sprintf("[%s] %s<br>", date('d-M-Y H:i:s'), 'Manually scheduler job complete');
        }
        else
        {
            $output .= '<br>Job not found!';
        }

        $objTemplate->cronlog = $output;
		                                                
		return $objTemplate->getResponse(); // compile and new Response()...
	}
	
	/**
	 * Run job and return the captured output
	 */
	private function runJob(&$qjob)
	{
	    global  $cronJob;
	    $limit = is_null($GLOBALS['TL_CONFIG']['cron_limit']) ? 5 : intval($GLOBALS['TL_CONFIG']['cron_limit']);
	    if ($limit<=0)
	    {
	        return;
	    }
	    $currtime = time();
	    $endtime  = $currtime+$limit;
	    $cronJob = array(
	        'id'		=> $qjob->id,
	        'title'		=> $qjob->title,
	        'lastrun'	=> $qjob->lastrun,
	        'endtime'	=> $endtime,
	        'runonce'	=> intval($qjob->runonce) > 0,
	        'logging'	=> intval($qjob->logging) > 0,
	        'completed'	=> true
	    );
	    ob_start();
	    $e = error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED & ~E_USER_DEPRECATED);
	    include(TL_ROOT . '/' . $qjob->job);
	    error_reporting($e);
	    return str_replace("\n",'<br>', trim(preg_replace('#<\s*br\s*/?\s*>#i', "\n", ob_get_flush())));
	} // runJob
	
}

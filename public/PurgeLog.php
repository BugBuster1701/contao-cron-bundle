<?php 

use Contao\System;
use Psr\Log\LogLevel;
use Contao\CoreBundle\Monolog\ContaoContext;

/**
 * Contao Open Source CMS, Copyright (C) 2005-2017 Leo Feyer
 *
 * Contao Module "Cron Scheduler"
 * Sample PHP script to execute by cron: Purges the system log
 * Job: web/bundles/bugbustercron/PurgeLog.php
 *
 * @copyright  Glen Langer 2018 <http://contao.ninja>
 * @author     Glen Langer (BugBuster)
 * @package    Cron
 * @license    LGPL
 * @filesource
 * @see	       https://github.com/BugBuster1701/contao-cron-bundle
 */

/**
 * Class PurgeLog
 * 
 * @copyright  Glen Langer 2012..2018 <http://contao.ninja>
 * @author     Glen Langer (BugBuster)
 * @package    Cron
 */
class PurgeLog extends \Contao\Backend // TODO geht so nicht ohne Contao Framework, oder? initialize.php gibt es ja nicht mehr
// https://docs.contao.org/dev/framework/cron/
{

    /**
     * Initialize the controller
     */
    public function __construct()
    {
    	parent::__construct();
    } // __construct
    
    /**
     * Implement the commands to run by this batch program
     */
    public function run()
    {
        global  $cronJob; // from BugBuster\Cron\ContaoBackendController Class
        
        //no directly call
        if (!is_array($cronJob))
        {
            die('You can not access this file directly!');
        }
        //At this time the job should be defered,
        //no new actions should be started after this time.
        if (time() >= $cronJob['endtime'])
        {
            $cronJob['completed'] = false;
            return;
        }
        
        \Contao\Database::getInstance()->prepare("DELETE FROM `tl_log`")->execute();
        if ($cronJob['logging'])
        {
            System::getContainer()
                ->get('monolog.logger.contao')
                ->log(LogLevel::INFO,
                    'System log purged by cron job.',
                    array('contao' => new ContaoContext('PurgeLog run()', ContaoContext::GENERAL)));
        }
    } // run
	
} // class PurgeLog

/**
 * Instantiate log purger
 */
$objPurge = new PurgeLog();
$objPurge->run();


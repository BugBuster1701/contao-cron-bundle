<?php 

/**
 * Contao Open Source CMS, Copyright (C) 2005-2017 Leo Feyer
 *
 * Contao Module "Cron Scheduler"
 *
 * @copyright  Glen Langer 2018 <http://contao.ninja>
 * @author     Glen Langer (BugBuster)
 * @package    Cron
 * @license    LGPL
 * @filesource
 * @see	       https://github.com/BugBuster1701/contao-cron-bundle
 */

/**
 * -------------------------------------------------------------------------
 * BACK END MODULES
 * -------------------------------------------------------------------------
 */
$GLOBALS['BE_MOD']['system']['cron'] = array(
	'tables'		=>	array('tl_crontab'),
	//'icon'			=>	'system/modules/cron/public/CronIcon.php', // TODO Auslösung, Ersatz schaffen
	'stylesheet'	=>	'bundles/bugbustercron/cronbundle_be_style.css'
);


/**
 * -------------------------------------------------------------------------
 * FRONT END MODULES
 * -------------------------------------------------------------------------
 */
//$GLOBALS['FE_MOD']['miscellaneous']['cron_fe'] = 'BugBuster\Cron\ModuleCron';

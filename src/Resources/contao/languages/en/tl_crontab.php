<?php 

/**
 * Contao Open Source CMS, Copyright (C) 2005-2017 Leo Feyer
 *
 * Contao Module "Cron Scheduler"
 * TL_ROOT/system/modules/cron/languages/en/tl_crontab.php
 * English translation file
 *
 * @copyright  Glen Langer 2013..2017 <http://contao.ninja>
 * @author     Glen Langer (BugBuster)
 * @package    Cron
 * @license    LGPL
 * @filesource
 * @see	       https://github.com/BugBuster1701/contao-cron
 */
 
/**
 * Fields
 */
$GLOBALS['TL_LANG']['tl_crontab']['title']['0']    = "Titel";
$GLOBALS['TL_LANG']['tl_crontab']['title']['1']    = "Enter a descriptive title for the job.";
$GLOBALS['TL_LANG']['tl_crontab']['job']['0']      = "Job";
$GLOBALS['TL_LANG']['tl_crontab']['job']['1']      = "Enter either a relative path of a PHP script, a symfony route, or a URL.";
$GLOBALS['TL_LANG']['tl_crontab']['t_minute']['0'] = "Minute";
$GLOBALS['TL_LANG']['tl_crontab']['t_minute']['1'] = "List the minutes for example as 5,10,15-20,30.<br>Use the form */15 for example for every 15 minutes.<br>Enter * for every minute.";
$GLOBALS['TL_LANG']['tl_crontab']['t_hour']['0']   = "Hour";
$GLOBALS['TL_LANG']['tl_crontab']['t_hour']['1']   = "List the hours for example as 2,4,5-7,9.<br>Use the form */3 for example for every 3 hours.<br>Enter * for every hour.";
$GLOBALS['TL_LANG']['tl_crontab']['t_dom']['0']    = "Day of month";
$GLOBALS['TL_LANG']['tl_crontab']['t_dom']['1']    = "List the days of month for example as 1,10,14-16,20.<br>Enter * for all days.";
$GLOBALS['TL_LANG']['tl_crontab']['t_month']['0']  = "Month";
$GLOBALS['TL_LANG']['tl_crontab']['t_month']['1']  = "List the month numbers as 1,3,7-9, or name shortcuts as Jan,Mar,Jul-Sep for example.<br>Enter * for every month.";
$GLOBALS['TL_LANG']['tl_crontab']['t_dow']['0']    = "Day of week";
$GLOBALS['TL_LANG']['tl_crontab']['t_dow']['1']    = "List the day numbers (0=sunday) as 0,2-4,7 or name shortcuts as Sun,Tue-Thu,Sat for example.<br>Enter * for every day of week.";
$GLOBALS['TL_LANG']['tl_crontab']['runonce']['0']  = "Run once";
$GLOBALS['TL_LANG']['tl_crontab']['runonce']['1']  = "Disable job after completion.";
$GLOBALS['TL_LANG']['tl_crontab']['enabled']['0']  = "Enabled";
$GLOBALS['TL_LANG']['tl_crontab']['enabled']['1']  = "Enable execution of this job.";
$GLOBALS['TL_LANG']['tl_crontab']['logging']['0']  = "Logging";
$GLOBALS['TL_LANG']['tl_crontab']['logging']['1']  = "Make log entry when job is executed.";

/**
 * Reference
 */
$GLOBALS['TL_LANG']['tl_crontab']['tl_minute']	= 'Minute';
$GLOBALS['TL_LANG']['tl_crontab']['tl_hour']	= 'Hour';
$GLOBALS['TL_LANG']['tl_crontab']['tl_dom']		= 'Day of the month';
$GLOBALS['TL_LANG']['tl_crontab']['tl_month']	= 'Month';
$GLOBALS['TL_LANG']['tl_crontab']['tl_dow']		= 'Day of the week';
$GLOBALS['TL_LANG']['tl_crontab']['lastrun']	= 'Last run';
$GLOBALS['TL_LANG']['tl_crontab']['nextrun']	= 'Next run';

/**
 * Buttons
 */
$GLOBALS['TL_LANG']['tl_crontab']['new']['0']         = 'New';
$GLOBALS['TL_LANG']['tl_crontab']['new']['1']         = 'Create a new job.';
$GLOBALS['TL_LANG']['tl_crontab']['edit']['0']        = 'Edit';
$GLOBALS['TL_LANG']['tl_crontab']['edit']['1']        = 'Edit the settings of this job.';
$GLOBALS['TL_LANG']['tl_crontab']['copy']['0']        = 'Copy';
$GLOBALS['TL_LANG']['tl_crontab']['copy']['1']        = 'Copy this job.';
$GLOBALS['TL_LANG']['tl_crontab']['delete']['0']      = 'Delete';
$GLOBALS['TL_LANG']['tl_crontab']['delete']['1']      = 'Delete this job.';
$GLOBALS['TL_LANG']['tl_crontab']['show']['0']        = 'Show';
$GLOBALS['TL_LANG']['tl_crontab']['show']['1']        = 'View the details.';
$GLOBALS['TL_LANG']['tl_crontab']['ena_logging']['0'] = 'Enable logging';
$GLOBALS['TL_LANG']['tl_crontab']['ena_logging']['1'] = 'Enable logging for job %s';
$GLOBALS['TL_LANG']['tl_crontab']['dis_logging']['0'] = 'Disable logging';
$GLOBALS['TL_LANG']['tl_crontab']['dis_logging']['1'] = 'Disable logging for job %s';
$GLOBALS['TL_LANG']['tl_crontab']['enable']['0']      = 'Enable execution';
$GLOBALS['TL_LANG']['tl_crontab']['enable']['1']      = 'Enable execution of job %s';
$GLOBALS['TL_LANG']['tl_crontab']['disable']['0']     = 'Disable execution';
$GLOBALS['TL_LANG']['tl_crontab']['disable']['1']     = 'Disable execution of job %s';
$GLOBALS['TL_LANG']['tl_crontab']['startnow']['0']    = "Run";
$GLOBALS['TL_LANG']['tl_crontab']['startnow']['1']    = "Run the execution of this job %s";

/**
 * Errors
 */
$GLOBALS['TL_LANG']['tl_crontab']['file_not_readable'] = 'File not found or not readable.';
$GLOBALS['TL_LANG']['tl_crontab']['route_not_exists']  = 'None of the routes match the path.';
$GLOBALS['TL_LANG']['tl_crontab']['allow_url_fopen_not_set']  = 'The PHP flag "allow_url_fopen" is not set.';
$GLOBALS['TL_LANG']['tl_crontab']['curl_not_available']       = 'The PHP "cURL" extension is not available.';
$GLOBALS['TL_LANG']['tl_crontab']['one_is_necessary']         = 'One is necessary.';

$GLOBALS['TL_LANG']['tl_crontab']['job_type'] = ['Job Type', 'Plase select job type'];
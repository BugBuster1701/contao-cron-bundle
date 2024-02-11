<?php

/*
 * This file is part of a BugBuster Contao Bundle.
 *
 * @copyright  Glen Langer 2024 <http://contao.ninja>
 * @author     Glen Langer (BugBuster)
 * @package    Contao Cron Bundle
 * @link       https://github.com/BugBuster1701/contao-cron-bundle
 *
 * @license    LGPL-3.0-or-later
 */

/*
 * -------------------------------------------------------------------------
 * BACK END MODULES
 * -------------------------------------------------------------------------
 */
$GLOBALS['BE_MOD']['system']['cron'] = array(
	'tables'		=>	array('tl_crontab'),
	'stylesheet'	=>	'bundles/bugbustercron/cronbundle_be_style.css'
);

/*
 * -------------------------------------------------------------------------
 * FRONT END MODULES
 * -------------------------------------------------------------------------
 */
$GLOBALS['FE_MOD']['miscellaneous']['cron_fe'] = 'BugBuster\Cron\ModuleCron';

/*
 * Eigene Actions
 * http://easysolutionsit.de/artikel/contao-eine-eigene-aktion-anlegen.html
 */
$GLOBALS['BE_MOD']['system']['cron']['enable']  = array('BugBuster\Cron\CronActions', 'enable');
$GLOBALS['BE_MOD']['system']['cron']['disable'] = array('BugBuster\Cron\CronActions', 'disable');
$GLOBALS['BE_MOD']['system']['cron']['enable_logging']  = array('BugBuster\Cron\CronActions', 'enableLogging');
$GLOBALS['BE_MOD']['system']['cron']['disable_logging'] = array('BugBuster\Cron\CronActions', 'disableLogging');

/*
 * -------------------------------------------------------------------------
 * HOOKS
 * -------------------------------------------------------------------------
 */
// $GLOBALS['TL_HOOKS']['parseBackendTemplate'][]  = array('BugBuster\Cron\CronHook', 'startJobs');

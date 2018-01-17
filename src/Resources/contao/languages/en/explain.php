<?php 

/**
 * Contao Open Source CMS, Copyright (C) 2005-2017 Leo Feyer
 *
 * Contao Module "Cron Scheduler"
 * TL_ROOT/system/modules/cron/languages/en/explain.php 
 * English translation file
 * 
 * @copyright  Glen Langer 2013..2017 <http://contao.ninja>
 * @author     Glen Langer (BugBuster)
 * @package    Cron
 * @license    LGPL
 * @filesource
 * @see	       https://github.com/BugBuster1701/contao-cron
 */

$GLOBALS['TL_LANG']['XPL']['cron_elements']['0']['0'] = "Basic element syntax";
$GLOBALS['TL_LANG']['XPL']['cron_elements']['0']['1'] = "The basic syntax for the time element is:<br/><br/><pre>\n    begin[-end][/step]\n</pre><br/>The parts enclosed in brackets are optional. The units dependend on the element type and can be minute, hour, day of month, day of week or month. The part <code>begin[-end]</code> may be replaced by a * which means <em>all</em>.<br/>For example, these are valid elements:<br/><br/><pre>\n		    5       minute,hour,day,... number 5\n		    3-5     minutes,hours,days,... 3,4,5\n		    5-10/2  minutes,hours,days,... 5,7,9\n		    *       all minutes,hours,days,...\n		    */3     minutes,hours,days,... 0,3,6,...\n</pre>";
$GLOBALS['TL_LANG']['XPL']['cron_elements']['1']['0'] = "Element list";
$GLOBALS['TL_LANG']['XPL']['cron_elements']['1']['1'] = "Every part of the schedule can be entered as a comma separated list, for example:<br/><br/><pre>\n		   5,7,10-15/2,21  = Numbers 5,7,10,12,14,21\n</pre>";
$GLOBALS['TL_LANG']['XPL']['cron_elements']['2']['0'] = "Day of week";
$GLOBALS['TL_LANG']['XPL']['cron_elements']['2']['1'] = "Days of week can be entered either as number 0...6 where 0 = sunday, or as 3 character english shortcut as Mon, Tue, Wed, Thu, Fri, Sat, Sun:<br/><br/><pre>\n		   Mon-Fri/2 is equivalent to 1-5/2\n</pre>";
$GLOBALS['TL_LANG']['XPL']['cron_elements']['3']['0'] = "Months";
$GLOBALS['TL_LANG']['XPL']['cron_elements']['3']['1'] = "Months can be entered either as number 1...12, or as 3 character english shortcut as Jan, Feb, Mar, Apr, May, Jun, Jul, Aug, Sep, Oct, Nov, Dec:<br/><br/><pre>\n		   Feb-Nov/3 is equivalent to 2-11/3\n</pre>";


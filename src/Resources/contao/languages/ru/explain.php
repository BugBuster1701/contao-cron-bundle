<?php
/**
 * Translations are managed using Transifex. To create a new translation
 * or to help to maintain an existing one, please register at transifex.com.
 *
 * @link http://help.transifex.com/intro/translating.html
 * @link https://www.transifex.com/projects/p/contao-cron/language/ru/
 *
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 *
 * last-updated: 2014-12-14T21:52:39+01:00
 */

$GLOBALS['TL_LANG']['XPL']['cron_elements']['0']['0'] = 'Основной синтаксис элементов';
$GLOBALS['TL_LANG']['XPL']['cron_elements']['0']['1'] = 'Основной синтаксис времени:<br/><br/><pre>n    begin[-end][/step]n</pre><br/>Части заключенные в скобки являются необязательными. Независимыми элементами могут быть минуты, часы, дни месяца, дни недели или месяцы. Часть <code>begin[-end]</code> может быть заменена * (звездочкой) которая включает их <em>все</em>.<br/>Пример правильных элементов:<br/><br/><pre>n		    5       minute,hour,day,... number 5n		    3-5     minutes,hours,days,... 3,4,5n		    5-10/2  minutes,hours,days,... 5,7,9n		    *       all minutes,hours,days,...n		    */3     minutes,hours,days,... 0,3,6,...n</pre>';
$GLOBALS['TL_LANG']['XPL']['cron_elements']['1']['0'] = 'Синтаксис списка';
$GLOBALS['TL_LANG']['XPL']['cron_elements']['1']['1'] = 'Каждая часть планируемого расписания может быть отделена запятыми. Пример:<br/><br/><pre>n		   5,7,10-15/2,21  = Числа 5,7,10,12,14,21n</pre>';
$GLOBALS['TL_LANG']['XPL']['cron_elements']['2']['0'] = 'Дни недели';
$GLOBALS['TL_LANG']['XPL']['cron_elements']['2']['1'] = 'Дни недели могут быть введены либо как число 0...6 где 0 = воскресенье, либо как 3-х значное буквенное обозначение дня недели в английском языке Mon, Tue, Wed, Thu, Fri, Sat, Sun:<br/><br/><pre>n		   Mon-Fri/2 эквивалентно 1-5/2n</pre>';
$GLOBALS['TL_LANG']['XPL']['cron_elements']['3']['0'] = 'Месяцы';
$GLOBALS['TL_LANG']['XPL']['cron_elements']['3']['1'] = 'Месяцы могут быть введены либо как число 1...12, либо как 3-х значное буквенное обозначение месяца в английском языке Jan, Feb, Mar, Apr, May, Jun, Jul, Aug, Sep, Oct, Nov, Dec:<br/><br/><pre>n		   Feb-Nov/3 эквивалентно 2-11/3n</pre>';


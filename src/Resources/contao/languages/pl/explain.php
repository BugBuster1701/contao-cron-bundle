<?php
/**
 * Translations are managed using Transifex. To create a new translation
 * or to help to maintain an existing one, please register at transifex.com.
 *
 * @link http://help.transifex.com/intro/translating.html
 * @link https://www.transifex.com/projects/p/contao-cron/language/pl/
 *
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 *
 * last-updated: 2014-12-14T21:52:39+01:00
 */


$GLOBALS['TL_LANG']['XPL']['cron_elements']['0']['0'] = 'Podstawowa składnia elementów';
$GLOBALS['TL_LANG']['XPL']['cron_elements']['0']['1'] = 'Podstawowa składnia dla elementów czasu:<br/><br/><pre>n    początek[-koniec][/krok]n</pre><br/>Część pokazana powyżej jest opcjonalna. Jednostki są uzależnione od typu elementu i tak moga to być minuta, godzina, dzień miesiąca, dzień tygodnia lub miesiąc. Część <code>początek[-koniec]</code> może być zastąpiona przez * co oznacza <em>wszystkie</em>.<br/>Dla przykładu, to są poprawne zdefiniowane elementy:<br/><br/><pre>n    5       minuta,godzina,dzień,... number 5n    3-5     minuty,godziny,dni,... 3,4,5n    5-10/2  minuty,godziny,dni,... 5,7,9n    *       wszystkie minuty,godziny,dni,...n    */3     co (ile) minut,godzin,dni,... 0,3,6,...n</pre>';
$GLOBALS['TL_LANG']['XPL']['cron_elements']['1']['0'] = 'Lista elementów';
$GLOBALS['TL_LANG']['XPL']['cron_elements']['1']['1'] = 'Każda cześć planisty może być wypełniana jako lista rozdzielona przecinkami, np.:<br/><br/><pre>n   5,7,10-15/2,21  = Numbers 5,7,10,12,14,21n</pre>';
$GLOBALS['TL_LANG']['XPL']['cron_elements']['2']['0'] = 'Dni tygodnia';
$GLOBALS['TL_LANG']['XPL']['cron_elements']['2']['1'] = 'Dni tygodnia mogą być wpisywane jako numery 0...6 gdzie 0 = niedziela, lub trzyliterowe angielskie skróty tj. Mon, Tue, Wed, Thu, Fri, Sat, Sun:<br/><br/><pre>n   Mon-Fri/2 jest równoznaczne z 1-5/3n</pre>';
$GLOBALS['TL_LANG']['XPL']['cron_elements']['3']['0'] = 'Miesiące';
$GLOBALS['TL_LANG']['XPL']['cron_elements']['3']['1'] = 'Miesiące mogą być wpisywane jako numery 1...12, lub trzyliterowe angielskie skróty np. Jan, Feb, Mar, Apr, May, Jun, Jul, Aug, Sep, Oct, Nov, Dec:<br/><br/><pre>n   Feb-Nov/3 jest równoznaczne z 2-11/3n</pre>';


<?php
/**
 * Translations are managed using Transifex. To create a new translation
 * or to help to maintain an existing one, please register at transifex.com.
 *
 * @link http://help.transifex.com/intro/translating.html
 * @link https://www.transifex.com/projects/p/contao-cron/language/lv/
 *
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 *
 * last-updated: 2014-12-14T21:52:39+01:00
 */


$GLOBALS['TL_LANG']['XPL']['cron_elements']['0']['0'] = 'Pamata elementu sintakse';
$GLOBALS['TL_LANG']['XPL']['cron_elements']['0']['1'] = 'Pamata sintakse laika elementam ir:<br><br><pre>\n    begin[-end][/step]\n</pre><br>Iekavās iekļautās sadaļas ir opcionālas. Vienības ir atkarīgas no elementu tipa un var būt: minūtes, stundas, mēneša dienas, mēneša vai nedēļas dienas. Sadaļa <code>begin[-end]</code> var tikt aivietota ar * kas nozīmē <em>all</em>.<br>Piemēram: šie ir derīgi elementi:<br><br><pre>\n    5       minūtes,stunda,diena,... number 5\n    3-5     minūtes,stundas,dienas,... 3,4,5\n    5-10/2  minūtes,stundas,dienas,... 5,7,9\n    *       visas minūtes,stundas,dienas,...\n    */3     minūtes,stundas,dienas,... 0,3,6,...\n</pre>';
$GLOBALS['TL_LANG']['XPL']['cron_elements']['1']['0'] = 'Elementu saraksts';
$GLOBALS['TL_LANG']['XPL']['cron_elements']['1']['1'] = 'Katra plānotāja sadaļa var tik norādīta kā ar komatu atdalīts saraksts, piemēram::<br><br><pre>   5,7,10-15/2,21  = Skaitļi 5,7,10,12,14,21</pre>';
$GLOBALS['TL_LANG']['XPL']['cron_elements']['2']['0'] = 'Nedēļas diena';
$GLOBALS['TL_LANG']['XPL']['cron_elements']['2']['1'] = 'Mēneši var tikt ierakstīti kā skaitļi 0...6 kur 0 = sunday, vai arī kā 3 burtu angliskie saīsinājumi, piemēram: Mon, Tue, Wed, Thu, Fri, Sat, Sun:<br><br><pre>   Mon-Fri/2 ir līdzvērtīgs kā 1-5/2</pre>';
$GLOBALS['TL_LANG']['XPL']['cron_elements']['3']['0'] = 'Mēneši';
$GLOBALS['TL_LANG']['XPL']['cron_elements']['3']['1'] = 'Mēneši var tikt ierakstīti kā skaitļi 1...12, vai arī kā 3 burtu angliskie saīsinājumi, piemēram: Jan, Feb, Mar, Apr, May, Jun, Jul, Aug, Sep, Oct, Nov, Dec:<br><br><pre> Feb-Nov/3 ir līdzvērtīgs kā 2-11/3 </pre>';


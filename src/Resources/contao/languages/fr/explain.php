<?php
/**
 * Translations are managed using Transifex. To create a new translation
 * or to help to maintain an existing one, please register at transifex.com.
 *
 * @link http://help.transifex.com/intro/translating.html
 * @link https://www.transifex.com/projects/p/contao-cron/language/fr/
 *
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 *
 * last-updated: 2014-12-14T21:52:39+01:00
 */


$GLOBALS['TL_LANG']['XPL']['cron_elements']['0']['0'] = 'Syntaxe de base';
$GLOBALS['TL_LANG']['XPL']['cron_elements']['0']['1'] = 'La syntaxe de base pour l\'élément de temps est :<br><br><pre>    début[-fin][/étape]</pre><br>Les parties incluses entre crochets sont facultatives. Les unités dépendent du type d\'élément et peuvent être une minute, une heure, un jour du mois, un jour de semaine ou de mois. La partie <code>début[-fin]</code> peut-être remplacée par * ce qui signifie <em>tous</em>.<br>Par exemple, voici des éléments valides :<br><br><pre>	5       minute,heure,jour,... nombre 5\n	3-5     minutes,heures,jours,... 3,4,5\n	5-10/2  minutes,heures,jours,... 5,7,9\n	*       toutes les minutes,heures,jours,...\n	*/3     minutes,heures,jours,... 0,3,6,...</pre>';
$GLOBALS['TL_LANG']['XPL']['cron_elements']['1']['0'] = 'Liste d\'éléments';
$GLOBALS['TL_LANG']['XPL']['cron_elements']['1']['1'] = 'Chaque partie d\'une programmation peut-être saisie sous une liste séparée par des virgules. Par exemple :<br><br><pre>   5,7,10-15/2,21  = Nombres 5,7,10,12,14,21</pre>';
$GLOBALS['TL_LANG']['XPL']['cron_elements']['2']['0'] = 'Jour de la semaine';
$GLOBALS['TL_LANG']['XPL']['cron_elements']['2']['1'] = 'Les jours de la semaine peuvent être des nombres 0...6 où 0 = dimanche, ou des raccourcis anglais de 3 caractères, comme Mon, Tue, Wed, Thu, Fri, Sat, Sun :<br><br><pre>   Mon-Fri/2 est équivalent à 1-5/3</pre>';
$GLOBALS['TL_LANG']['XPL']['cron_elements']['3']['0'] = 'Mois';
$GLOBALS['TL_LANG']['XPL']['cron_elements']['3']['1'] = 'Les mois peuvent être des nombres 1...12, ou des raccourcis anglais de 3 caractères, comme Jan, Feb, Mar, Apr, May, Jun, Jul, Aug, Sep, Oct, Nov, Dec :<br><br><pre>   Feb-Nov/3 est équivalent à 2-11/3</pre>';


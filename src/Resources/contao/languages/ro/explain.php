<?php
/**
 * Translations are managed using Transifex. To create a new translation
 * or to help to maintain an existing one, please register at transifex.com.
 *
 * @link http://help.transifex.com/intro/translating.html
 * @link https://www.transifex.com/projects/p/contao-cron/language/ro/
 *
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 *
 * last-updated: 2014-12-14T21:52:39+01:00
 */


$GLOBALS['TL_LANG']['XPL']['cron_elements']['0']['0'] = 'Sintaxa elementului';
$GLOBALS['TL_LANG']['XPL']['cron_elements']['0']['1'] = 'Sintaxa de bază pentru elementul de timp este: <br/> <br/><pre> început[-sfârşit][/pas] </pre><br/> Ce se găseşte între paranteze este opţional. Unităţile depind de tipul de element şi pot fi minut, oră, zi din lună, zi din săptămână sau din lună. Partea <code> început[-sfârşit]</code> poate fi înlocuită cu un *, ceea ce înseamnă <em>toate</em> elementele. <br/>De exemplu, construcţii valide sunt: <br/><br/> <pre>5 minute,oră,zi, ... numărul 5 n3-5 minute,ore,zile, ... 3,4,5 5-10/2 nminute,ore,zile, ... 5,7,9 * toate nminute,ore,zile, ... * / 3 nminute,ore,zile, ... 0,3,6, ... </ pre>';
$GLOBALS['TL_LANG']['XPL']['cron_elements']['1']['0'] = 'Lista de elemente';
$GLOBALS['TL_LANG']['XPL']['cron_elements']['1']['1'] = 'Fiecare parte a programului poate fi introdusă sub formă de listă separată prin virgulă, de exemplu: <br/><br/><pre>5,7,10-15/2,21 = numerele 5,7,10,12,14,21 </pre>nsearch';
$GLOBALS['TL_LANG']['XPL']['cron_elements']['2']['0'] = 'Zile din săptămână';
$GLOBALS['TL_LANG']['XPL']['cron_elements']['2']['1'] = 'Ziua din săptămână poate fi introdusă fie ca număr 0...6 unde 0 = duminică, sau prin 3 caractere corespunzătoare limbii engleze ca Mon, Tue, Wed, Thu, Fri, Sat, Sun: <br/><br/><pre> Mon-Fri/2 este echivalent cu 1-5/2 </pre>';
$GLOBALS['TL_LANG']['XPL']['cron_elements']['3']['0'] = 'Lunile';
$GLOBALS['TL_LANG']['XPL']['cron_elements']['3']['1'] = 'Luna poate fi introdusă fie ca număr prin 1...12, sau prin 3 caractere corespunzătoare limbii engleze ca Jan, Feb, Mar, Apr, May, Jun, Jul, Aug, Sep, Oct, Nov, Dec: <br/><br/><pre>Feb-Nov/3 este equivalent cu 2-11/3 </pre>';


<?php
/**
 * Translations are managed using Transifex. To create a new translation
 * or to help to maintain an existing one, please register at transifex.com.
 *
 * @link http://help.transifex.com/intro/translating.html
 * @link https://www.transifex.com/projects/p/contao-cron/language/it/
 *
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 *
 * last-updated: 2014-12-14T21:52:39+01:00
 */


$GLOBALS['TL_LANG']['XPL']['cron_elements']['0']['0'] = 'Sintassi di base';
$GLOBALS['TL_LANG']['XPL']['cron_elements']['0']['1'] = 'La sintassi di base per l\'elemento temporale è:<br/><br/><pre>n    begin[-end][/step]n</pre><br/>Le parti racchiuse da parentesi sono opzionali. Le unità che dipendono dal tipo di elemento possono essere minuto, ora, giorno del mese, giorno della settimana o mese. La parte <code>begin[-end]</code> può essere sostituita da un * il che vuol dire <em>all</em>.<br/>Ad esempio, questi sono elementi validi:<br/><br/><pre>n    5       minuti,ore,giorni,... numero 5n    3-5     minuti,ore,giorni,... 3,4,5n    5-10/2  minuti,ore,giorni,... 5,7,9n    *       tutti minuti,ore,giorni,...n    */3     minuti,ore,giorni,... 0,3,6,...n</pre>';
$GLOBALS['TL_LANG']['XPL']['cron_elements']['1']['0'] = 'Elemento dell\'elenco';
$GLOBALS['TL_LANG']['XPL']['cron_elements']['1']['1'] = 'Ogni parte dello schedule può essere inserito come un elenco separato da virgola, ad esempio:<br/><br/><pre>n   5,7,10-15/2,21  = Numeri 5,7,10,12,14,21n</pre>';
$GLOBALS['TL_LANG']['XPL']['cron_elements']['2']['0'] = 'Giorno della settimana';
$GLOBALS['TL_LANG']['XPL']['cron_elements']['2']['1'] = 'I gionri della settimana possono essere inseriti o come numeri 0...6 dove 0 = domenica, oppure come 3 caratteri abbreviati in inglese come Mon, Tue, Wed, Thu, Fri, Sat, Sun:<br/><br/><pre>n   Mon-Fri/2 è equivalente a to 1-5/2n</pre>';
$GLOBALS['TL_LANG']['XPL']['cron_elements']['3']['0'] = 'Mesi';
$GLOBALS['TL_LANG']['XPL']['cron_elements']['3']['1'] = 'I mesi possono essere inseriti come numeri 1...12, oppure come 3 caratteri abbreviati in inglese Jan, Feb, Mar, Apr, May, Jun, Jul, Aug, Sep, Oct, Nov, Dec:<br/><br/><pre>n   Feb-Nov/3 è equivalente a 2-11/3n</pre>';


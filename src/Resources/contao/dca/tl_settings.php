<?php

/**
 * Contao Open Source CMS, Copyright (C) 2005-2018 Leo Feyer
 *
 * Contao Module "Cron Scheduler"
 *
 * @copyright  Glen Langer 2013..2018 <http://contao.ninja>
 * @author     Glen Langer (BugBuster)
 * @package    Cron
 * @license    LGPL
 * @filesource
 * @see           https://github.com/BugBuster1701/contao-cron-bundle
 */

/**
 * Add to palette
 */
$GLOBALS['TL_DCA']['tl_settings']['palettes']['default'] .= ';{cron_scheduler_legend},cron_limit';

/**
 * Add field
 */
$GLOBALS['TL_DCA']['tl_settings']['fields']['cron_limit'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_settings']['cron_limit'],
    'inputType' => 'text',
    'default' => '5',
    'eval' => ['mandatory' => true, 'rgxp' => 'digit', 'nospace' => true],
];

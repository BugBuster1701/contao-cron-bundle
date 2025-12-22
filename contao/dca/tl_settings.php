<?php

/*
 * This file is part of a BugBuster Contao Bundle.
 *
 * @copyright  Glen Langer 2025 <http://contao.ninja>
 * @author     Glen Langer (BugBuster)
 * @package    Contao Cron Bundle
 * @link       https://github.com/BugBuster1701/contao-cron-bundle
 *
 * @license    LGPL-3.0-or-later
 */

/*
 * Add to palette
 */
$GLOBALS['TL_DCA']['tl_settings']['palettes']['default'] .= ';{cron_scheduler_legend},cron_limit';

/*
 * Add field
 */
$GLOBALS['TL_DCA']['tl_settings']['fields']['cron_limit'] = array(
	'label'		=> &$GLOBALS['TL_LANG']['tl_settings']['cron_limit'],
	'inputType'	=> 'text',
	'default'	=> '5',
	'eval'		=> array('mandatory'=>true, 'rgxp'=>'digit', 'nospace'=>true)
);

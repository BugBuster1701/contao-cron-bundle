<?php

/**
 * Table tl_crontab
 */
$GLOBALS['TL_DCA']['tl_crontab'] = array
(

    // Config
    'config' => array
    (
        'dataContainer'               => 'Table',
        'enableVersioning'            => true,
        'sql' => array
        (
            'keys' => array
            (
                'id'    => 'primary'
            )
        )
    ),

    // List
    'list' => array
    (
        'sorting' => array
        (
            'mode'				=> 1,
            'fields'			=> array('title'),
            'flag'				=> 1
        ),
        'label' => array
        (
            'fields'			=> array('title'),
            'format'			=> '%s',
            'label_callback'	=> array('BugBuster\Cron\DcaCrontab', 'listJobs')
        ),
        'global_operations' => array
        (
            'all' => array
            (
                'label'			=> &$GLOBALS['TL_LANG']['MSC']['all'],
                'href'			=> 'act=select',
                'class'			=> 'header_edit_all',
                'attributes'	=> 'onclick="Backend.getScrollOffset();"'
            )
        ),
        'operations' => array
        (
            'edit' => array
            (
                'label'			=> &$GLOBALS['TL_LANG']['tl_crontab']['edit'],
                'href'			=> 'act=edit',
                'icon'			=> 'edit.gif'
            ),
            'copy' => array
            (
                'label'			=> &$GLOBALS['TL_LANG']['tl_crontab']['copy'],
                'href'			=> 'act=copy',
                'icon'			=> 'copy.gif'
            ),
            'delete' => array
            (
                'label'			=> &$GLOBALS['TL_LANG']['tl_crontab']['delete'],
                'href'			=> 'act=delete',
                'icon'			=> 'delete.gif',
                'attributes'	=> 'onclick="if (!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\')) return false; Backend.getScrollOffset();"'
            ),
            'show' => array
            (
                'label'			=> &$GLOBALS['TL_LANG']['tl_crontab']['show'],
                'href'			=> 'act=show',
                'icon'			=> 'show.gif'
            ),
            'enabled' => array
            (
                'button_callback'	=>	array('BugBuster\Cron\DcaCrontab', 'enabledButton')
            ),
            'logging' => array
            (
                'button_callback'	=>	array('BugBuster\Cron\DcaCrontab', 'loggingButton')
            ),
            'startnow' => array
            (
                'button_callback'	=>	array('BugBuster\Cron\DcaCrontab', 'startnowButton')
            )
        )
    ),
    // Palettes
    'palettes' => array
    (
        'default'	=> 'title,job;t_minute,t_hour,t_dom,t_month,t_dow;runonce,enabled,logging'
    ),
    // Fields
    'fields' => array
    (
        'id' => array
        (
            'sql'       => "int(10) unsigned NOT NULL auto_increment"
        ),
        'tstamp' => array
        (
            'sql'       => "int(10) unsigned NOT NULL default '0'"
        ),
        'lastrun' => array
        (
            'sql'       => "int(10) unsigned NOT NULL default '0'"
        ),
        'nextrun' => array
        (
            'sql'       => "int(10) unsigned NOT NULL default '0'"
        ),
        'scheduled' => array
        (
            'sql'       => "int(10) unsigned NOT NULL default '0'"
        ),
        'title' => array
        (
            'label'			=> &$GLOBALS['TL_LANG']['tl_crontab']['title'],
            'exclude'		=> true,
            'inputType'		=> 'text',
            'eval'			=> array('mandatory'=>true, 'maxlength'=>100),
            'sql'           => "varchar(100) NOT NULL default ''"
        ),
        'job' => array
        (
            'label'			=> &$GLOBALS['TL_LANG']['tl_crontab']['job'],
            'exclude'		=> true,
            'inputType'		=> 'text',
            'explanation'	=> 'cron_jobs',
            'eval'			=> array('mandatory'=>true, 'maxlength'=>100, 'helpwizard'=>true),
            'sql'           => "varchar(100) NOT NULL default ''"
        ),
        't_minute' => array
        (
            'label'			=> &$GLOBALS['TL_LANG']['tl_crontab']['t_minute'],
            'exclude'		=> true,
            'inputType'		=> 'text',
            'default'		=> '*',
            'explanation'	=> 'cron_elements',
            'eval'			=> array('nospace'=>true, 'maxlength'=>100, 'helpwizard'=>true),
            'sql'           => "varchar(100) NOT NULL default ''"
        ),
        't_hour' => array
        (
            'label'			=> &$GLOBALS['TL_LANG']['tl_crontab']['t_hour'],
            'exclude'		=> true,
            'inputType'		=> 'text',
            'default'		=> '*',
            'explanation'	=> 'cron_elements',
            'eval'			=> array('nospace'=>true, 'maxlength'=>100, 'helpwizard'=>true),
            'sql'           => "varchar(100) NOT NULL default ''"
        ),
        't_dom' => array
        (
            'label'			=> &$GLOBALS['TL_LANG']['tl_crontab']['t_dom'],
            'exclude'		=> true,
            'inputType'		=> 'text',
            'default'		=> '*',
            'explanation'	=> 'cron_elements',
            'eval'			=> array('nospace'=>true, 'maxlength'=>100, 'helpwizard'=>true),
            'sql'           => "varchar(100) NOT NULL default ''"
        ),
        't_month' => array
        (
            'label'			=> &$GLOBALS['TL_LANG']['tl_crontab']['t_month'],
            'exclude'		=> true,
            'inputType'		=> 'text',
            'default'		=> '*',
            'explanation'	=> 'cron_elements',
            'eval'			=> array('nospace'=>true, 'maxlength'=>100, 'helpwizard'=>true),
            'sql'           => "varchar(100) NOT NULL default ''"
        ),
        't_dow' => array
        (
            'label'			=> &$GLOBALS['TL_LANG']['tl_crontab']['t_dow'],
            'exclude'		=> true,
            'inputType'		=> 'text',
            'default'		=> '*',
            'explanation'	=> 'cron_elements',
            'eval'			=> array('nospace'=>true, 'maxlength'=>100, 'helpwizard'=>true),
            'sql'           => "varchar(100) NOT NULL default ''"
        ),
        'runonce' => array(
            'label'			=> &$GLOBALS['TL_LANG']['tl_crontab']['runonce'],
            'exclude'		=> true,
            'inputType'		=> 'checkbox',
            'sql'           => "char(1) NOT NULL default '0'"
        ),
        'enabled' => array(
            'label'			=> &$GLOBALS['TL_LANG']['tl_crontab']['enabled'],
            'exclude'		=> true,
            'inputType'		=> 'checkbox',
            'sql'           => "char(1) NOT NULL default '0'"
        ),
        'logging' => array(
            'label'			=> &$GLOBALS['TL_LANG']['tl_crontab']['logging'],
            'exclude'		=> true,
            'inputType'		=> 'checkbox',
            'sql'           => "char(1) NOT NULL default '0'"
        )
    )
);

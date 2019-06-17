<?php


/**
 * Table tl_crontab
 */
$GLOBALS['TL_DCA']['tl_crontab'] = [

    // Config
    'config' => [
        'dataContainer' => 'Table',
        'enableVersioning' => true,
        'sql' => [
            'keys' => [
                'id' => 'primary',
            ],
        ],
    ],

    // List
    'list' => [
        'sorting' => [
            'mode' => 1,
            'fields' => ['title'],
            'flag' => 1,
        ],
        'label' => [
            'fields' => ['title'],
            'format' => '%s',
            'label_callback' => ['BugBuster\Cron\DcaCrontab', 'listJobs'],
        ],
        'global_operations' => [
            'all' => [
                'label' => &$GLOBALS['TL_LANG']['MSC']['all'],
                'href' => 'act=select',
                'class' => 'header_edit_all',
                'attributes' => 'onclick="Backend.getScrollOffset();"',
            ],
        ],
        'operations' => [
            'edit' => [
                'label' => &$GLOBALS['TL_LANG']['tl_crontab']['edit'],
                'href' => 'act=edit',
                'icon' => 'edit.gif',
            ],
            'copy' => [
                'label' => &$GLOBALS['TL_LANG']['tl_crontab']['copy'],
                'href' => 'act=copy',
                'icon' => 'copy.gif',
            ],
            'delete' => [
                'label' => &$GLOBALS['TL_LANG']['tl_crontab']['delete'],
                'href' => 'act=delete',
                'icon' => 'delete.gif',
                'attributes' => 'onclick="if (!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\')) return false; Backend.getScrollOffset();"',
            ],
            'show' => [
                'label' => &$GLOBALS['TL_LANG']['tl_crontab']['show'],
                'href' => 'act=show',
                'icon' => 'show.gif',
            ],
            'enabled' => [
                'button_callback' => ['BugBuster\Cron\DcaCrontab', 'enabledButton'],
            ],
            'logging' => [
                'button_callback' => ['BugBuster\Cron\DcaCrontab', 'loggingButton'],
            ],
            'startnow' => [
                'button_callback' => ['BugBuster\Cron\DcaCrontab', 'startnowButton'],
            ],
        ],
    ],
    // Palettes
    'palettes' => [
        'default' => 'title,job_type,job;t_minute,t_hour,t_dom,t_month,t_dow;runonce,enabled,logging',
    ],
    // Fields
    'fields' => [
        'id' => [
            'sql' => "int(10) unsigned NOT NULL auto_increment",
        ],
        'tstamp' => [
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ],
        'lastrun' => [
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ],
        'nextrun' => [
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ],
        'scheduled' => [
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ],
        'title' => [
            'label' => &$GLOBALS['TL_LANG']['tl_crontab']['title'],
            'exclude' => true,
            'inputType' => 'text',
            'eval' => ['mandatory' => true, 'maxlength' => 100, 'tl_class' => 'w50'],
            'sql' => "varchar(100) NOT NULL default ''",
        ],
        'job_type' => [
            'label' => &$GLOBALS['TL_LANG']['tl_crontab']['job_type'],
            'exclude' => true,
            'inputType' => 'select',
            'options' => [
                1 => 'File',
                2 => 'Symfony Route',
                3 => 'Url',
                4 => 'Symfony Command',
            ],
            'eval' => ['tl_class' => 'w50', 'includeBlankOption' => true, 'mandatory' => true, 'chosen' => true],
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ],
        'job' => [
            'label' => &$GLOBALS['TL_LANG']['tl_crontab']['job'],
            'exclude' => true,
            'inputType' => 'text',
            'explanation' => 'cron_jobs',
            'eval' => ['mandatory' => true, 'maxlength' => 100, 'helpwizard' => true, 'tl_class' => 'clr'],
            'sql' => "varchar(100) NOT NULL default ''",
        ],
        't_minute' => [
            'label' => &$GLOBALS['TL_LANG']['tl_crontab']['t_minute'],
            'exclude' => true,
            'inputType' => 'text',
            'default' => '*',
            'explanation' => 'cron_elements',
            'eval' => ['nospace' => true, 'maxlength' => 100, 'helpwizard' => true],
            'sql' => "varchar(100) NOT NULL default ''",
        ],
        't_hour' => [
            'label' => &$GLOBALS['TL_LANG']['tl_crontab']['t_hour'],
            'exclude' => true,
            'inputType' => 'text',
            'default' => '*',
            'explanation' => 'cron_elements',
            'eval' => ['nospace' => true, 'maxlength' => 100, 'helpwizard' => true],
            'sql' => "varchar(100) NOT NULL default ''",
        ],
        't_dom' => [
            'label' => &$GLOBALS['TL_LANG']['tl_crontab']['t_dom'],
            'exclude' => true,
            'inputType' => 'text',
            'default' => '*',
            'explanation' => 'cron_elements',
            'eval' => ['nospace' => true, 'maxlength' => 100, 'helpwizard' => true],
            'sql' => "varchar(100) NOT NULL default ''",
        ],
        't_month' => [
            'label' => &$GLOBALS['TL_LANG']['tl_crontab']['t_month'],
            'exclude' => true,
            'inputType' => 'text',
            'default' => '*',
            'explanation' => 'cron_elements',
            'eval' => ['nospace' => true, 'maxlength' => 100, 'helpwizard' => true],
            'sql' => "varchar(100) NOT NULL default ''",
        ],
        't_dow' => [
            'label' => &$GLOBALS['TL_LANG']['tl_crontab']['t_dow'],
            'exclude' => true,
            'inputType' => 'text',
            'default' => '*',
            'explanation' => 'cron_elements',
            'eval' => ['nospace' => true, 'maxlength' => 100, 'helpwizard' => true],
            'sql' => "varchar(100) NOT NULL default ''",
        ],
        'runonce' => [
            'label' => &$GLOBALS['TL_LANG']['tl_crontab']['runonce'],
            'exclude' => true,
            'inputType' => 'checkbox',
            'sql' => "char(1) NOT NULL default '0'",
        ],
        'enabled' => [
            'label' => &$GLOBALS['TL_LANG']['tl_crontab']['enabled'],
            'exclude' => true,
            'inputType' => 'checkbox',
            'sql' => "char(1) NOT NULL default '0'",
        ],
        'logging' => [
            'label' => &$GLOBALS['TL_LANG']['tl_crontab']['logging'],
            'exclude' => true,
            'inputType' => 'checkbox',
            'sql' => "char(1) NOT NULL default '0'",
        ],
    ],
];

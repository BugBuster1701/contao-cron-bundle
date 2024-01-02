<?php

/*
 * This file is part of a BugBuster Contao Bundle.
 *
 * @copyright  Glen Langer 2024 <http://contao.ninja>
 * @author     Glen Langer (BugBuster)
 * @package    Contao Cron Bundle
 * @link       https://github.com/BugBuster1701/contao-cron-bundle
 *
 * @license    LGPL-3.0-or-later
 */

namespace BugBuster\Cron;

use Contao\Environment;
use Contao\StringUtil;
use Contao\System;
use Contao\Template;

/**
 * Hook parseBackendTemplate
 */
class CronHook extends System
{
	/**
	 * Current object instance
	 * @var object
	 */
	protected static $instance;

	/**
	 * Initialize
	 */
	public function __construct()
	{
		parent::__construct();

		System::loadLanguageFile('default');
		System::loadLanguageFile('tl_crontab');
	}

	/**
	 * Return the current object instance (Singleton)
	 * @return BotStatisticsHelper
	 */
	public static function getInstance()
	{
		if (self::$instance === null)
		{
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Start Jobs
	 *
	 * @param  string $strContent
	 * @param  string $strTemplate
	 * @return string
	 */
	public function startJobs($strContent, $strTemplate)
	{
		if ($strTemplate != 'be_main')
		{
			return $strContent;
		}

		$arrParams = array();
		$strUrl = System::getContainer()->get('router')->generate('cron_frontend_startjobs', $arrParams);
		$strUrl = substr($strUrl, \strlen(Environment::get('path')) + 1);

		$strScripts = Template::generateInlineScript('
            setTimeout(
                function(){
                        try{
                            var n=new XMLHttpRequest();
                        }catch(r){
                            return;
                        }
                        n.open("GET","' . StringUtil::ampersand($strUrl) . '",true);
                        n.send();
                },1000
            );');

		$searchString = '</body>';
		$strContent = str_replace($searchString, $strScripts . $searchString, $strContent);

		return $strContent;
	}// startJobs
}

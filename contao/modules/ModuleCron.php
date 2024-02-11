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

use Contao\BackendTemplate;
use Contao\Module;
use Contao\System;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ModuleCron
 *
 * @deprecated 1.6.0 No longer used by internal code and not recommended.
 */
class ModuleCron extends Module
{
	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'mod_cron_fe';

	/**
	 * Display a wildcard in the back end
	 * @deprecated 1.6.0 No longer used by internal code and not recommended.
	 * @return string
	 */
	public function generate()
	{
		if (
			System::getContainer()->get('contao.routing.scope_matcher')
			->isBackendRequest(System::getContainer()->get('request_stack')
			->getCurrentRequest() ?? Request::create(''))
		) {
			$objTemplate = new BackendTemplate('be_wildcard');
			$objTemplate->wildcard = '### Scheduler FE - DEPRECATED - do not use! ###';
			$objTemplate->title = $this->headline;
			$objTemplate->id    = $this->id;
			$objTemplate->link  = $this->name;
			$objTemplate->href  = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

			return $objTemplate->parse();
		}

		return parent::generate();
	}

	/**
	 * Generate module
	 * @deprecated 1.6.0 No longer used by internal code and not recommended.
	 */
	protected function compile()
	{
		$return = $this->run();
		$this->Template->out = $return;
	}

	/**
	 * generate Template output
	 *
	 * @deprecated 1.6.0 No longer used by internal code and not recommended.
	 * @return string
	 */
	public function run()
	{
		return '';
		// $arrParams = array();
		// $strUrl = System::getContainer()->get('router')->generate('cron_frontend_startjobs', $arrParams);
		// $strUrl = substr($strUrl, \strlen(Environment::get('path')) + 1);

		// $strScripts = Template::generateInlineScript('
		//     setTimeout(
		//         function(){
		//                 try{
		//                     var n=new XMLHttpRequest();
		//                 }catch(r){
		//                     return;
		//                 }
		//                 n.open("GET","' . StringUtil::ampersand($strUrl) . '",true);
		//                 n.send();
		//         },1000
		//     );');

		// return $strScripts;
	} // run
} // class

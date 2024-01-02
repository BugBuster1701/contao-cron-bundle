<?php

namespace BugBuster\Cron;

use Contao\Environment;
use Symfony\Bundle\FrameworkBundle\Routing\Router;

/**
 * Hook parseBackendTemplate
 */
class CronHook extends \Contao\System
{

    /**
     * Current object instance
     * @var object
     */
    protected static $instance = null;

    /**
     * Initialize 
     *
     */
    public function __construct()
    {
		parent::__construct();

		\Contao\System::loadLanguageFile('default');
		\Contao\System::loadLanguageFile('tl_crontab');
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
     * @return string $strContent
     */
    public function startJobs($strContent, $strTemplate)
    {
        if ($strTemplate != 'be_main')
        {
            return $strContent;
        }

        $arrParams = array();
        $strUrl = \Contao\System::getContainer()->get('router')->generate('cron_frontend_startjobs', $arrParams);
        $strUrl = substr($strUrl, \strlen(\Contao\Environment::get('path')) + 1);

        $strScripts = \Contao\Template::generateInlineScript('
            setTimeout(
                function(){
                        try{
                            var n=new XMLHttpRequest();
                        }catch(r){
                            return;
                        }
                        n.open("GET","'.\Contao\StringUtil::ampersand($strUrl).'",true);
                        n.send();
                },1000
            );');

        $searchString = '</body>';
        $strContent = str_replace($searchString, $strScripts.$searchString, $strContent);

        return $strContent;

    }//startJobs

}

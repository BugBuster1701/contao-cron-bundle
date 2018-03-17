<?php

/**
 * Contao Open Source CMS, Copyright (C) 2005-2018 Leo Feyer
*
* Contao Module "Cron Scheduler", FE Module
* for use on the frondend to trigger cron.
*
* @copyright  Glen Langer 2013..2018 <http://contao.ninja>
* @author     Glen Langer (BugBuster)
* @package    Cron
* @license    LGPL
* @filesource
* @see	       https://github.com/BugBuster1701/contao-cron-bundle
*/

namespace BugBuster\Cron;

/**
 * Class ModuleCron
 *
 * @copyright  Glen Langer 2013..2018 <http://contao.ninja>
 * @author     Glen Langer (BugBuster)
 * @package    Cron
 * @license    LGPL
 */
class ModuleCron extends \Module
{
    /**
     * Template
     * @var string
     */
    protected $strTemplate = 'mod_cron_fe';
    
    /**
     * Display a wildcard in the back end
     * @return string
     */
    public function generate()
    {
        if (TL_MODE == 'BE')
        {
            $objTemplate = new \BackendTemplate('be_wildcard');
            $objTemplate->wildcard = '### Scheduler FE ###';
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
     */
    protected function compile()
    {
        $return = $this->run();
        $this->Template->out = $return;
    }
    
    public function run() 
    {
        $arrParams = array();
        $strUrl = \System::getContainer()->get('router')->generate('cron_frontend_startjobs', $arrParams);
        $strUrl = substr($strUrl, strlen(\Environment::get('path')) + 1);
        
        $strScripts = \Template::generateInlineScript('
            setTimeout(
                function(){
                        try{
                            var n=new XMLHttpRequest();
                        }catch(r){
                            return;
                        }
                        n.open("GET","'.ampersand($strUrl).'",true);
                        n.send();
                },1000
            );');
        return $strScripts;
    } // run
    
} // class

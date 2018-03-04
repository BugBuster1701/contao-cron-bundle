<?php

/**
 * @copyright  Glen Langer 2018 <http://contao.ninja>
 * @author     Glen Langer (BugBuster)
 * @license    LGPL-3.0+
 */

namespace BugBuster\Cron;

use Symfony\Component\HttpFoundation\Response;
use Contao\Database;
use Psr\Log\LogLevel;
use Contao\CoreBundle\Monolog\ContaoContext;

/**
 * Back end 
 *
 * @author     Glen Langer (BugBuster)
 */
class ContaoFrontendController extends \Frontend
{

    /**
     * stop processung jobs in one trigger after this time in seconds
     * can be overwritten
     *
     * @var integer
     */
    private $cron_max_run = 5;
    
	/**
	 * Initialize the controller
	 *
	 */
	public function __construct()
	{
		parent::__construct();

		// See #4099
		if (!defined('BE_USER_LOGGED_IN'))
		{
		    define('BE_USER_LOGGED_IN', false);
		}
		
		if (!defined('FE_USER_LOGGED_IN'))
		{
		    define('FE_USER_LOGGED_IN', false);
		}
		
		\System::loadLanguageFile('tl_crontab');
	}


	/**
	 * Run the controller and parse the template
	 *
	 * @return Response
	 */
	public function runJobs()
	{
	    global $cronJob;
	    
	    $objResponse = new Response();
	    
	    $limit = is_null($GLOBALS['TL_CONFIG']['cron_limit']) ? $this->cron_max_run : intval($GLOBALS['TL_CONFIG']['cron_limit']);

	    // Do not run if there is POST data 
	    if (!empty($_POST) || $limit <= 0)
	    {
	        return $objResponse;
	    }
	    $currtime = time();
	    
	    // process cron list
	    $q = \Database::getInstance()->prepare("SELECT * FROM `tl_crontab`
                                                WHERE `enabled`='1'
                                                AND (
                                                      (`nextrun`>0 and `nextrun`<?)
                                                   OR (`nextrun`=0 and `scheduled`<?)
                                                    )
                                                ORDER BY `nextrun`, `scheduled`")
                                    ->execute($currtime, $currtime-86400);
        $locked  = false;
        $endtime = time() + $limit;

        while ($q->next())
        {
            $currtime = time();
            if ($currtime >= $endtime)
            {
                break;
            }
            if (!$locked)
            {
                // ensure exclusive access
                $ql = \Database::getInstance()->prepare("SELECT get_lock('cronlock',0) AS lockstate")->execute();
                if ( !$ql->next() || !intval($ql->lockstate) )
                {
                    return $objResponse;
                }
                $locked = true;
            } // if
            if ($q->nextrun>0)
            { // due to execute
                $cronJob = array(
                    'id'		=> $q->id,
                    'title'		=> $q->title,
                    'lastrun'	=> $q->lastrun,
                    'endtime'	=> $endtime,
                    'runonce'	=> intval($q->runonce) > 0,
                    'logging'	=> intval($q->logging) > 0,
                    'completed'	=> true
                );
                
                $output = $this->runJob($q);
                
                if ($cronJob['completed'])
                {
                    if ($cronJob['runonce'])
                    {
                        $dataset = array(
                            'lastrun'	=> $currtime,
                            'nextrun'	=> 0,
                            'scheduled'	=> 0,
                            'enabled'	=> '0'
                        );
                    }
                    else
                    {
                        $dataset = array(
                            'lastrun'	=> $currtime,
                            'nextrun'	=> $this->schedule($q),
                            'scheduled'	=> $currtime
                        );
                    }
                    \Database::getInstance()->prepare("UPDATE `tl_crontab` %s WHERE id=?")
                                            ->set($dataset)
                                            ->execute($q->id);
                } // if
                if ($cronJob['logging'] || $output!='')
                {
                    if ($output!='')
                    {
                        \System::getContainer()
                                ->get('monolog.logger.contao')
                                ->log(LogLevel::ERROR,
                                    'Cron job '.$q->title.' failed: '.$output,
                                    array('contao' => new ContaoContext('ContaoFrontendController runJobs()', TL_ERROR)));
                    }
                    else
                    {
                        \System::getContainer()
                                ->get('monolog.logger.contao')
                                ->log(LogLevel::ERROR,
                                    'Cron job '.$q->title.' '.($cronJob['completed'] ? 'completed.' : 'processed partially.'),
                                    array('contao' => new ContaoContext('ContaoFrontendController runJobs()', TL_GENERAL)));
                    }
                } // if
            }
            else
            {
                $dataset = array(
                    'nextrun'	=> $this->schedule($q),
                    'scheduled'	=> $currtime
                );
                \Database::getInstance()->prepare("UPDATE `tl_crontab` %s WHERE id=?")
                                        ->set($dataset)
                                        ->execute($q->id);
            } // if
        } // while

        // release lock
        if ($locked)
        {
            \Database::getInstance()->prepare("SELECT release_lock('cronlock')")->execute();
        }

        return $objResponse;	    
	}

	/**
	 * Run job and return the captured output
	 */
	private function runJob(&$qjob)
	{
	    //File exists and readable?
	    if (!is_readable(TL_ROOT . '/' . $qjob->job))
	    {
	        return $GLOBALS['TL_LANG']['tl_crontab']['file_not_readable'];
	    }
	    
	    ob_start();
	    $e = error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED & ~E_USER_DEPRECATED);
	    include(TL_ROOT . '/' . $qjob->job);
	    error_reporting($e);
	    return str_replace("\n",'<br>', trim(preg_replace('#<\s*br\s*//*?\s*>#i', "\n", ob_get_flush())));
	} // runJob
	
	/**
	 * Find new schedule time for job
	 */
	private function schedule(&$qjob)
	{
	    $minute = array();
	    $hour   = array();
	    $dom    = array();
	    $month  = array();
	    $dow    = array();
	     
	    $dowNum =
	    str_ireplace(
	        array('Sun','Mon','Tue','Wed','Thu','Fri','Sat'),
	        array(0,1,2,3,4,5,6),
	        $qjob->t_dow
	        );
	    $monthNum =
	    str_ireplace(
	        array('Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'),
	        array(1,2,3,4,5,6,7,8,9,10,11,12),
	        $qjob->t_month
	        );
	    $this->parseElement($qjob->t_minute,	$minute,	0,	60);
	    $this->parseElement($qjob->t_hour,		$hour,		0,	24);
	    $this->parseElement($qjob->t_dom,		$dom,		1,	31);
	    $this->parseElement($monthNum,			$month,		1,	12);
	    $this->parseElement($dowNum,			$dow,		0,	 7);
	
	    $nextrun = time()+60;
	    $maxdate = $nextrun+31536000; // schedule for one year ahead max
	    while ($nextrun < $maxdate)
	    {
	        $dateArr	= getdate($nextrun);
	        $_seconds	= $dateArr['seconds'];
	        $_minutes	= $dateArr['minutes'];
	        $_hours		= $dateArr['hours'];
	        $_mday		= $dateArr['mday'];
	        $_wday		= $dateArr['wday'];
	        $_mon		= $dateArr['mon'];
	         
	        if (!$month[$_mon] || !$dom[$_mday] || !$dow[$_wday])
	        {
	            // increment to 00:00:00 of next day
	            $nextrun += 60*(60*(24-$_hours)-$_minutes)-$_seconds;
	            continue;
	        } // if
	         
	        $allhours = ($_hours==0);
	        while ($_hours < 24)
	        {
	            if ($hour[$_hours])
	            {
	                $allminutes = ($_minutes==0);
	                while ($_minutes < 60)
	                {
	                    if ($minute[$_minutes]) return $nextrun;
	                    // increment to next minute
	                    $nextrun += 60-$_seconds;
	                    $_minutes++;
	                    $_seconds = 0;
	                } // while
	                if ($allminutes) return 0;
	                $_hours++;
	                $_minutes = 0;
	            }
	            else
	            {
	                // increment to next hour
	                $nextrun += 60*(60-$_minutes)-$_seconds;
	                $_hours++;
	                $_minutes = $_seconds = 0;
	            } // if
	        } // while
	        if ($allhours) return 0;
	    } // while
	    return 0;
	} // schedule
	
	/**
	 * Parse timer element of syntax  from[-to][/step] or *[/step] and set flag for each tick
	 */
	private function parseElement($element, &$targetArray, $base, $numberOfElements)
	{
	    if (trim($element)=='') $element = '*';
	    $subelements = explode(',', $element);
	    for ($i = $base; $i < $base+$numberOfElements; $i++)
	        $targetArray[$i] = $subelements[0] == "*";
	
	        for ($i = 0; $i < count($subelements); $i++) {
	            if ( preg_match("~^(\\*|([0-9]{1,2})(-([0-9]{1,2}))?)(/([0-9]{1,2}))?$~", $subelements[$i], $matches) )
	            {
	                if ($matches[1]=='*')
	                {
	                    $matches[2] = $base;					// all from
	                    $matches[4] = $base+$numberOfElements;	// all to
	                }
	                elseif ($matches[4]=='')
	                {
	                    $matches[4] = $matches[2];	// to = from
	                } // if
	                if ($matches[5][0]!='/')
	                    $matches[6] = 1;			// default step
	                    $from	= intval(ltrim($matches[2],'0'));
	                    $to		= intval(ltrim($matches[4],'0'));
	                    $step	= intval(ltrim($matches[6],'0'));
	                    for ($j = $from; $j <= $to; $j += $step) $targetArray[$j] = true;
	            } // if
	        } // for
	} // parseElement
	
	
}

<?php

namespace BugBuster\Cron;

/**
 * Class DcaCrontab
 *
 * Provide miscellaneous methods that are used by the data configuration array.
 */
class DcaCrontab extends \Backend
{
    /**
     * Job Constants
     * @var integer
     */
    const JOB_TYPE_FILE  = 1;
    const JOB_TYPE_ROUTE = 2;
    const JOB_TYPE_URL   = 3;

    /**
     * List a particular record
     */
    public function listJobs($row)
    {
        $this->setNextRun($row);
        $text = &$GLOBALS['TL_LANG']['tl_crontab'];
        $arrParams = array('do'=>'cron',
                           'act'=>'edit',
                           'id'=>$row['id'],
                           'rt'=>REQUEST_TOKEN
                          );

        $link = $this->route('contao_backend', $arrParams);

        $jobtypetxt = 'jobtype' . (string) $this->getJobType($row['job']);
        $jobtyperow = sprintf('<div class="jobtype">%s: %s</div>', $text['jobtypetitle'], $text[$jobtypetxt]);

        return
        '<a class="cron-list" href="'.$link.'"><div>' .
        '<div class="main">' .
        '<div class="title">' . $row['title'] . '</div>' .
        $jobtyperow .
        //					'<div class="job">' . $row['job'] . '</div>' .
        '</div>' .
        '<div>' .
        '<div class="floatleft">' .
        '<div class="caption">' . $text['tl_minute'] . '</div>' .
        '<div class="data">' . ($row['t_minute']=='' ? '*' : $row['t_minute']) . '</div>' .
        '</div>' .
        '<div class="floatleft">' .
        '<div class="caption">' . $text['tl_hour'] . '</div>' .
        '<div class="data">' . ($row['t_hour']=='' ? '*' : $row['t_hour']) . '</div>' .
        '</div>' .
        '<div class="floatleft">' .
        '<div class="caption">' . $text['tl_dom'] . '</div>' .
        '<div class="data">' . ($row['t_dom']=='' ? '*' : $row['t_dom']) . '</div>' .
        '</div>' .
        '<div class="floatleft">' .
        '<div class="caption">' . $text['tl_month'] . '</div>' .
        '<div class="data">' . ($row['t_month']=='' ? '*' : $row['t_month']) . '</div>' .
        '</div>' .
        '<div class="floatleft">' .
        '<div class="caption">' . $text['tl_dow'] . '</div>' .
        '<div class="data">' . ($row['t_dow']=='' ? '*' : $row['t_dow']) . '</div>' .
        '</div>' .
        '<div class="floatleft">' .
        '<div class="caption">' . $text['lastrun'] . '</div>' .
        '<div class="data">' . ($row['lastrun']==0 ? '' : date($GLOBALS['TL_CONFIG']['datimFormat'], $row['lastrun'])) . '</div>' .
        '</div>' .
        '<div class="floatleft">' .
        '<div class="caption">' . $text['nextrun'] . '</div>' .
        '<div class="data">' . ($row['nextrun']==0 ? '' : date($GLOBALS['TL_CONFIG']['datimFormat'], $row['nextrun'])) . '</div>' .
        '</div><div class="clear"></div>' .
        '</div>' .
        '</div></a>';
    } // listJobs

    /**
     * Return a route relative to the base URL
     *
     * @param string $strName   The route name
     * @param array  $arrParams The route parameters
     *
     * @return string The route
     */
    public function route($strName, $arrParams=array())
    {
        $strUrl = \System::getContainer()->get('router')->generate($strName, $arrParams);
        $strUrl = substr($strUrl, \strlen(\Environment::get('path')) + 1);

        return ampersand($strUrl);
    }

	/**
	 * Get the Job Type
	 * @param  string $strJob
	 * @return int    1: File, 2: Route 3: URL
	 */
	private function getJobType($strJob)
	{
	    if ('http:' == substr($strJob, 0, 5) || 'https:' == substr($strJob, 0, 6))
	    {
	        return self::JOB_TYPE_URL;
	    }

	    if ('.php' == substr($strJob, -4))
	    {
	        return self::JOB_TYPE_FILE;
	    }

	    return self::JOB_TYPE_ROUTE; // I hope :-)
    }

    /**
     * Create the enabled/disabled button
     */
    public function enabledButton($row, $href, $label, $title, $icon, $attributes)
    {
        if ($row['enabled']=='1')
        {
            $href = 'key=disable';
            $label = &$GLOBALS['TL_LANG']['tl_crontab']['disable'];
            $icon = 'bundles/bugbustercron/enabled.png';
        }
        else
        {
            $href = 'key=enable';
            $label = &$GLOBALS['TL_LANG']['tl_crontab']['enable'];
            $icon = 'bundles/bugbustercron/disabled.png';
        } // if
        $title = sprintf($label[1], $row['id']);

        return
        '<a href="' . $this->addToUrl($href.'&amp;id='.$row['id']) .
        '" title="' . \StringUtil::specialchars($title) . '"' . $attributes . '>' .
        '<img src="'.$icon.'" width="16" height="16" alt="'.\StringUtil::specialchars($title).'" />' .
        '</a> ';
    } // enabledButton

    /**
     * Create the logging on/off button
     */
    public function loggingButton($row, $href, $label, $title, $icon, $attributes)
    {
        if ($row['logging']=='1')
        {
            $href = 'key=disable_logging';
            $label = &$GLOBALS['TL_LANG']['tl_crontab']['dis_logging'];
            $icon = 'bundles/bugbustercron/logging.png';
        }
        else
        {
            $href = 'key=enable_logging';
            $label = &$GLOBALS['TL_LANG']['tl_crontab']['ena_logging'];
            $icon = 'bundles/bugbustercron/notlogging.png';
        } // if
        $title = sprintf($label[1], $row['id']);

        return
        '<a href="' . $this->addToUrl($href.'&amp;id='.$row['id']) .
        '" title="' . \StringUtil::specialchars($title) . '"' . $attributes . '>' .
        '<img src="'.$icon.'" width="16" height="16" alt="'.\StringUtil::specialchars($title).'" />' .
        '</a> ';
    } // loggingButton

    public function startnowButton($row, $href, $label, $title, $icon, $attributes)
    {
        $href = 'key=start_now';
        $label = &$GLOBALS['TL_LANG']['tl_crontab']['startnow'];
        $icon = 'bundles/bugbustercron/start_now.png';
        $title = sprintf($label[1], $row['id']);
        $strEncypt = 'later_'.$row['id']; //TODO base64_encode( Cron_Encryption::encrypt( serialize( array( $title,$row['id'] ) ) ) );

        //$href = 'system/modules/cron/public/CronStart.php?crcst='.$strEncypt.'';

        $arrParams = array('crcst' => $strEncypt, 'rt'=>REQUEST_TOKEN);
        $href = $this->route('cron_backend_startnow', $arrParams);

        return
        '<a href="' . $href . '"' .
        'onclick="if(!confirm(\''.$title.'?\'))return false;Backend.openModalIframe({\'width\':735,\'height\':405,\'title\':\'Cronjob Start\',\'url\':this.href});return false"'.
        ' title="' . \StringUtil::specialchars($title) . '"' . $attributes . '>' .
        '<img src="'.$icon.'" width="16" height="16" alt="'.\StringUtil::specialchars($title).'" />' .
        '</a> ';
    }

    /**
     * Set next run date, if it enabled but it is not set
     * @param array $row Job Array
     */
    private function setNextRun(&$row)
    {
        if ($row['enabled']=='1' && $row['nextrun']=='0')
        {
            //get job
            $q = \Database::getInstance()->prepare("SELECT * FROM `tl_crontab`
                                                    WHERE `enabled`='1'
                                                    AND id=?")
                                         ->execute($row['id']);
            //set next run date
            $dataset = array(
                'nextrun'	=> $this->schedule($q),
                'scheduled'	=> time()
            );
            \Database::getInstance()->prepare("UPDATE `tl_crontab` %s WHERE id=?")
                                    ->set($dataset)
                                    ->execute($q->id);
            $row['nextrun'] = $dataset['nextrun'];
        }

        return;
    }

    /**
     * Find new schedule time for job
     * @see CronController
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
            array('Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'),
            array(0, 1, 2, 3, 4, 5, 6),
            $qjob->t_dow
            );
        $monthNum =
        str_ireplace(
            array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'),
            array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12),
            $qjob->t_month
            );
        $this->parseElement($qjob->t_minute, $minute, 0, 60);
        $this->parseElement($qjob->t_hour, $hour, 0, 24);
        $this->parseElement($qjob->t_dom, $dom, 1, 31);
        $this->parseElement($monthNum, $month, 1, 12);
        $this->parseElement($dowNum, $dow, 0, 7);

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
     * @see CronControllers
     */
    private function parseElement($element, &$targetArray, $base, $numberOfElements)
    {
        if (trim($element)=='')
        {
            $element = '*';
        }
        $subelements = explode(',', $element);
        for ($i = $base; $i < $base+$numberOfElements; $i++)
        {
            $targetArray[$i] = $subelements[0] == "*";
        }

        for ($i = 0; $i < \count($subelements); $i++)
        {
            if (preg_match("~^(\\*|([0-9]{1,2})(-([0-9]{1,2}))?)(/([0-9]{1,2}))?$~", $subelements[$i], $matches))
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
                {
                    $matches[6] = 1;			// default step
                }
                $from	= (int) (ltrim($matches[2], '0'));
                $to		= (int) (ltrim($matches[4], '0'));
                $step	= (int) (ltrim($matches[6], '0'));
                for ($j = $from; $j <= $to; $j += $step)
                {
                    $targetArray[$j] = true;
                }
            } // if
        } // for
    } // parseElement

}

<?php

declare(strict_types=1);

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
    public function listJobs($row) : string
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
        '<div class="data">' . ((int) $row['lastrun'] == 0 ? '' : date($GLOBALS['TL_CONFIG']['datimFormat'], (int) $row['lastrun'])) . '</div>' .
        '</div>' .
        '<div class="floatleft">' .
        '<div class="caption">' . $text['nextrun'] . '</div>' .
        '<div class="data">' . ((int) $row['nextrun'] == 0 ? '' : date($GLOBALS['TL_CONFIG']['datimFormat'], (int) $row['nextrun'])) . '</div>' .
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
    public function route($strName, $arrParams=array()) : string
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
    public function enabledButton($row, $href, $label, $title, $icon, $attributes) : string
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
    public function loggingButton($row, $href, $label, $title, $icon, $attributes) : string
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

    public function startnowButton($row, $href, $label, $title, $icon, $attributes) : string
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
                'nextrun'	=> $this->getNextRun($q),
                'scheduled'	=> time()
            );
            if ($dataset['nextrun'] == 0)
            {
                //wrong value, disable job
                \Database::getInstance()->prepare("UPDATE `tl_crontab` SET `enabled`='0' WHERE id=?")->execute($row['id']);
                \Message::addInfo('Wrong value(s) in the scheduler formular: '.$q->title);
            }
            \Database::getInstance()->prepare("UPDATE `tl_crontab` %s WHERE id=?")
                                    ->set($dataset)
                                    ->execute($q->id);
            $row['nextrun'] = $dataset['nextrun'];
        }

        return;
    }

    private function getNextRun($qjob) : int
    {
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
        $crontab = sprintf('%s %s %s %s %s', $qjob->t_minute, $qjob->t_hour, $qjob->t_dom, $monthNum, $dowNum);
        //Hotfix, better later.
        try {
            $cron = \Cron\CronExpression::factory($crontab);
        } catch (\Throwable $th) {
            return 0;
        }

        return (int) $cron->getNextRunDate()->format('U');

    }

}

<?php

namespace BugBuster\Cron;

/**
* Class DCA_crontab
*
* Provide miscellaneous methods that are used by the data configuration array.
*/
class DCA_crontab extends \Backend
{
    /**
     * List a particular record
     */
    public function listJobs($row)
    {
        $this->setNextRun($row);
        $text = &$GLOBALS['TL_LANG']['tl_crontab'];
        $link = $this->Environment->script . '?do=cron&amp;act=edit&amp;id=' . $row['id'] .'&amp;rt=' . REQUEST_TOKEN;
        return
        '<a class="cron-list" href="'.$link.'"><div>' .
        '<div class="main">' .
        '<div class="title">' . $row['title'] . '</div>' .
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
        '</div>' .
        '</div>' .
        '</div></a>';
    } // listJobs
    
    /**
     * Create the enabled/disabled button
     */
    public function enabledButton($row, $href, $label, $title, $icon, $attributes)
    {
        if ($row['enabled']=='1')
        {
            $href = 'act=disable';
            $label = &$GLOBALS['TL_LANG']['tl_crontab']['disable'];
            $icon = 'system/modules/cron/assets/enabled.png';
        }
        else
        {
            $href = 'act=enable';
            $label = &$GLOBALS['TL_LANG']['tl_crontab']['enable'];
            $icon = 'system/modules/cron/assets/disabled.png';
        } // if
        $title = sprintf($label[1], $row['id']);
        return
        '<a href="' . $this->addToUrl($href.'&amp;id='.$row['id']) .
        '" title="' . specialchars($title) . '"' . $attributes . '>' .
        '<img src="'.$icon.'" width="16" height="16" alt="'.specialchars($title).'" />' .
        '</a> ';
    } // enabledButton
    
    /**
     * Create the logging on/off button
     */
    public function loggingButton($row, $href, $label, $title, $icon, $attributes)
    {
        if ($row['logging']=='1')
        {
            $href = 'act=dis_logging';
            $label = &$GLOBALS['TL_LANG']['tl_crontab']['dis_logging'];
            $icon = 'system/modules/cron/assets/logging.png';
        }
        else
        {
            $href = 'act=ena_logging';
            $label = &$GLOBALS['TL_LANG']['tl_crontab']['ena_logging'];
            $icon = 'system/modules/cron/assets/notlogging.png';
        } // if
        $title = sprintf($label[1], $row['id']);
        return
        '<a href="' . $this->addToUrl($href.'&amp;id='.$row['id']) .
        '" title="' . specialchars($title) . '"' . $attributes . '>' .
        '<img src="'.$icon.'" width="16" height="16" alt="'.specialchars($title).'" />' .
        '</a> ';
    } // loggingButton
    
    public function startnowButton($row, $href, $label, $title, $icon, $attributes)
    {
        $href = 'act=start_now';
        $label = &$GLOBALS['TL_LANG']['tl_crontab']['startnow'];
        $icon = 'system/modules/cron/assets/start_now.png';
        $title = sprintf($label[1], $row['id']);
        $strEncypt = 'later_'.$row['id']; //base64_encode( Cron_Encryption::encrypt( serialize( array( $title,$row['id'] ) ) ) );
         
        $href = 'system/modules/cron/public/CronStart.php?crcst='.$strEncypt.'';
         
        return
        '<a href="' . $href . '"' .
        'onclick="if(!confirm(\''.$title.'?\'))return false;Backend.openModalIframe({\'width\':735,\'height\':405,\'title\':\'Cronjob Start\',\'url\':this.href});return false"'.
        ' title="' . specialchars($title) . '"' . $attributes . '>' .
        '<img src="'.$icon.'" width="16" height="16" alt="'.specialchars($title).'" />' .
        '</a> ';
    }
    
}

<?php

namespace BugBuster\Cron;

/**
 * @author 
 */
class CronActions
{

    public function __construct()
    {}

    public function enable($dc)
    {
        if ($dc->id)
        {
            //set enabled
            \Database::getInstance()->prepare("UPDATE `tl_crontab` SET `enabled`='1' WHERE id=?")->execute($dc->id);
            //get job
            $q = \Database::getInstance()->prepare("SELECT * FROM `tl_crontab`
                                                    WHERE `enabled`='1'
                                                    AND id=?")
                                         ->execute($dc->id);
            //set next run date
            $dataset = array(
                'nextrun'	=> $this->getNextRun($q),
                'scheduled'	=> time()
            );
            \Database::getInstance()->prepare("UPDATE `tl_crontab` %s WHERE id=?")
                                    ->set($dataset)
                                    ->execute($q->id);
        }

        // Zurück zur Übersicht leiten
        $router = \System::getContainer()->get('router');
        $url = $router->generate('contao_backend');
        \Contao\Controller::redirect($url.'?do=cron');
    }

    public function disable($dc)
    {
        if ($dc->id)
        {
            \Database::getInstance()->prepare("UPDATE `tl_crontab` SET `enabled`='0', `nextrun`=0, `scheduled`=0 WHERE id=?")
                                    ->execute($dc->id);
        }
        // Zurück zur Übersicht leiten
        $router = \System::getContainer()->get('router');
        $url = $router->generate('contao_backend');
        \Contao\Controller::redirect($url.'?do=cron');

    }

    public function enableLogging($dc)
    {

        if ($dc->id)
        {
            \Database::getInstance()->prepare("UPDATE `tl_crontab` SET `logging`='1' WHERE id=?")
                                    ->execute($dc->id);
        }

        // Zurück zur Übersicht leiten
        $router = \System::getContainer()->get('router');
        $url = $router->generate('contao_backend');
        \Contao\Controller::redirect($url.'?do=cron');
    }

    public function disableLogging($dc)
    {

        if ($dc->id)
        {
            \Database::getInstance()->prepare("UPDATE `tl_crontab` SET `logging`='0' WHERE id=?")
                                    ->execute($dc->id);
        }

        // Zurück zur Übersicht leiten
        $router = \System::getContainer()->get('router');
        $url = $router->generate('contao_backend');
        \Contao\Controller::redirect($url.'?do=cron');
    }

    private function getNextRun($qjob)
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
        $cron = \Cron\CronExpression::factory($crontab);

        return $cron->getNextRunDate()->format('U');

    }

}


<?php

declare(strict_types=1);

/*
 * This file is part of a BugBuster Contao Bundle.
 *
 * @copyright  Glen Langer 2026 <http://contao.ninja>
 * @author     Glen Langer (BugBuster)
 * @package    Contao Cron Bundle
 * @link       https://github.com/BugBuster1701/contao-cron-bundle
 *
 * @license    LGPL-3.0-or-later
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 */

namespace BugBuster\CronBundle\EventListener\DataContainer;

use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Contao\DataContainer;
use Cron\CronExpression;

class SaveCallbackListener
{
    #[AsCallback(table: 'tl_crontab', target: 'fields.t_minute.save')]
    public function onSaveCallbackMinute($minute, DataContainer $dc): string
    {
        if (false === $this->checkCrontabValues($minute, '*', '*', '*', '*')) {
            throw new \InvalidArgumentException($GLOBALS['TL_LANG']['tl_crontab']['t_minute']['1']);
        }

        return $minute;
    }

    #[AsCallback(table: 'tl_crontab', target: 'fields.t_hour.save')]
    public function onSaveCallbackHour($hour, DataContainer $dc): string
    {
        if (false === $this->checkCrontabValues('*', $hour, '*', '*', '*')) {
            throw new \InvalidArgumentException($GLOBALS['TL_LANG']['tl_crontab']['t_hour']['1']);
        }

        return $hour;
    }

    #[AsCallback(table: 'tl_crontab', target: 'fields.t_dom.save')]
    public function onSaveCallbackDayofmonth($dayofmonth, DataContainer $dc): string
    {
        if (false === $this->checkCrontabValues('*', '*', $dayofmonth, '*', '*')) {
            throw new \InvalidArgumentException($GLOBALS['TL_LANG']['tl_crontab']['t_dom']['1']);
        }

        return $dayofmonth;
    }

    #[AsCallback(table: 'tl_crontab', target: 'fields.t_month.save')]
    public function onSaveCallbackMonth($month, DataContainer $dc): string
    {
        if (false === $this->checkCrontabValues('*', '*', '*', $month, '*')) {
            throw new \InvalidArgumentException($GLOBALS['TL_LANG']['tl_crontab']['t_month']['1']);
        }

        return $month;
    }

    #[AsCallback(table: 'tl_crontab', target: 'fields.t_dow.save')]
    public function onSaveCallbackDayofweek($dayofweek, DataContainer $dc): string
    {
        if (false === $this->checkCrontabValues('*', '*', '*', '*', $dayofweek)) {
            throw new \InvalidArgumentException($GLOBALS['TL_LANG']['tl_crontab']['t_dow']['1']);
        }

        return $dayofweek;
    }

    #[AsCallback(table: 'tl_crontab', target: 'fields.expert_timeout.save')]
    public function onSaveCallbackTimeout($expert_timeout, DataContainer $dc): string
    {
        if ((int) $expert_timeout < 5 || (int) $expert_timeout > 300) {
            throw new \InvalidArgumentException($GLOBALS['TL_LANG']['tl_crontab']['expert_timeout']['1']);
        }

        return $expert_timeout;
    }

    /**
     * Check Crontab Values via \Cron\CronExpression.
     *
     * @param string $minute
     * @param string $hour
     * @param string $dayofmonth
     * @param string $month
     * @param string $dayofweek
     */
    protected function checkCrontabValues($minute, $hour, $dayofmonth, $month, $dayofweek): bool
    {
        $dayofweekNum =
        str_ireplace(
            ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
            [0, 1, 2, 3, 4, 5, 6],
            $dayofweek,
        );
        $monthNum =
        str_ireplace(
            ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12],
            $month,
        );
        $crontab = sprintf('%s %s %s %s %s', $minute, $hour, $dayofmonth, $monthNum, $dayofweekNum);

        try {
            CronExpression::factory($crontab);
        } catch (\Throwable $th) {
            return false;
        }

        return true;
    }
}

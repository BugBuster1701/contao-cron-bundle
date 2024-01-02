<?php

declare(strict_types=1);

/*
 * This file is part of a BugBuster Contao Bundle.
 *
 * @copyright  Glen Langer 2024 <http://contao.ninja>
 * @author     Glen Langer (BugBuster)
 * @package    Contao Cron Bundle
 * @link       https://github.com/BugBuster1701/contao-cron-bundle
 *
 * @license    LGPL-3.0-or-later
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 */

namespace BugBuster\CronBundle\Controller;

use BugBuster\Cron\ContaoBackendController;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Handles back end routes.
 * path: /bbcron/startjobnow.
 */
class BackendController extends AbstractController
{
    /**
     * Renders the details content.
     *
     * @return Response
     */
    public function startJobNowAction()
    {
        $this->container->get('contao.framework')->initialize();
        $controller = new ContaoBackendController();

        return $controller->runJobNow();
    }
}

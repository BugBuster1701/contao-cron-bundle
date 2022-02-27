<?php

declare(strict_types=1);

/*
 * This file is part of a BugBuster Contao Bundle
 *
 * @copyright  Glen Langer 2020..2022 <http://contao.ninja>
 * @author     Glen Langer (BugBuster)
 * @package    CronBundle
 * @license    LGPL-3.0-or-later
 * @see        https://github.com/BugBuster1701/contao-cron-bundle
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

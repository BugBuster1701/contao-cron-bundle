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

use BugBuster\Cron\ContaoFrontendController;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Handles front end routes.
 * path: /bbcron/startjobs.
 */
class FrontendController extends AbstractController
{
    /**
     * Renders the details content.
     *
     * @return Response
     */
    public function startJobsAction()
    {
        $this->container->get('contao.framework')->initialize();
        $controller = new ContaoFrontendController();

        return $controller->runJobs();
    }
}

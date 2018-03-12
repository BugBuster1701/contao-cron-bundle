<?php

namespace BugBuster\CronBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use BugBuster\Cron\ContaoFrontendController;

/**
 * Handles front end routes.
 * path: /bbcron/startjobs
 * 
 */
class FrontendController extends Controller
{
    
    /**
     * Renders the details content.
     *
     * @return Response
     *
     */
    public function startJobsAction()
    {
        $this->container->get('contao.framework')->initialize();
        $controller = new ContaoFrontendController();
        return $controller->runJobs();
    }
}

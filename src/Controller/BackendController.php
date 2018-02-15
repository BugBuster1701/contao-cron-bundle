<?php

namespace BugBuster\CronBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use BugBuster\Cron\ContaoBackendController;

/**
 * Handles back end routes.
 * 
 */
class BackendController extends Controller
{
    
    /**
     * Renders the details content.
     *
     * @return Response
     *
     */
    public function startJobNowAction()
    {
        $this->container->get('contao.framework')->initialize();
        $controller = new ContaoBackendController();
        return $controller->runJobNow();
    }
}

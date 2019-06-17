<?php

namespace BugBuster\CronBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use BugBuster\Cron\ContaoBackendController;

/**
 * Handles back end routes.
 * path: /bbcron/startjobnow
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
        //TODO:: test
        $this->container->get('contao.framework')->initialize();
        $controller = new ContaoBackendController();
        return $controller->runJobNow();
    }
}

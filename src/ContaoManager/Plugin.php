<?php

/**
 * @copyright  Glen Langer 2018 <http://contao.ninja>
 * @author     Glen Langer (BugBuster)
 * @package    CronBundle
 * @license    LGPL-3.0+
 * @see	       https://github.com/BugBuster1701/contao-cron-bundle
 *
 */

namespace BugBuster\CronBundle\ContaoManager;

use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;

use Contao\ManagerPlugin\Routing\RoutingPluginInterface;
use Symfony\Component\Config\Loader\LoaderResolverInterface;
use Symfony\Component\HttpKernel\KernelInterface;

use Contao\ManagerPlugin\Config\ConfigPluginInterface;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Plugin for the Contao Manager.
 *
 * @author Glen Langer (BugBuster)
 */
class Plugin implements BundlePluginInterface, RoutingPluginInterface, ConfigPluginInterface
{
    /**
     * {@inheritdoc}
     */
    public function getBundles(ParserInterface $parser)
    {
        return [
            BundleConfig::create('BugBuster\CronBundle\BugBusterCronBundle')
                ->setLoadAfter(['Contao\CoreBundle\ContaoCoreBundle']),
            BundleConfig::create('Http\HttplugBundle\HttplugBundle')
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function getRouteCollection(LoaderResolverInterface $resolver, KernelInterface $kernel)
    {
        return $resolver
                ->resolve(__DIR__.'/../Resources/config/routing.yml')
                ->load(__DIR__.'/../Resources/config/routing.yml')
                ;
    }
    
    public function registerContainerConfiguration(LoaderInterface $loader, array $config)
    {
        $loader->load(
            function (ContainerBuilder $container) use ($loader) {
                if ('dev' === $container->getParameter('kernel.environment')) {
                    $loader->load('@BugBusterCronBundle/Resources/config/config_dev.yml');
                }
                else {
                    $loader->load('@BugBusterCronBundle/Resources/config/config.yml');
                }
            }
        );
    }
}

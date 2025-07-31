<?php

namespace App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    protected function configureContainer(ContainerConfigurator $container): void
    {
        $container->import('../config/{packages}/*.yaml');
        $container->import('../config/{packages}/' . $this->environment . '/*.yaml');

        $container->import('../config/services.yaml');
        if (is_file(__DIR__ . '/../config/services_' . $this->environment . '.yaml')) {
            $container->import('../config/services_' . $this->environment . '.yaml');
        }
    }

    protected function configureRoutes(RoutingConfigurator $routes): void
    {
        // Import des fichiers de routes YAML (API, bundles tiers, etc.)
        $routes->import('../config/{routes}/*.yaml');
        $routes->import('../config/{routes}/' . $this->environment . '/*.yaml');

        // Import automatique des attributs PHP dans les contrôleurs
        $routes->import('../src/Controller/', 'attribute');
    }
}

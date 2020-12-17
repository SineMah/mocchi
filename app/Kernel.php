<?php

namespace App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    protected Container $storage;

    public function registerBundles(): array
    {
        $bundles = [
            new \Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new \Symfony\Bundle\TwigBundle\TwigBundle(),
        ];

        if ($this->getEnvironment() == 'dev') {
            $bundles[] = new \Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
        }

        return $bundles;
    }

    protected function configureContainer(ContainerConfigurator $c): void
    {
        $this->getContainer();

        $c->import(__DIR__.'/../config/mocchi.yaml');

        // register all classes in /src/ as service
        $c->services()
            ->load('App\\', __DIR__.'/*')
            ->autowire()
            ->autoconfigure()
        ;
    }

    protected function configureRoutes(RoutingConfigurator $routes): void
    {
        $config = $this->storage->config('routes');

        foreach ($config as $route) {
            $controllerName = 'App\Controller\\' . ucfirst($route['controller']) . 'Controller';

            $routes->add($route['name'], $route['path'])->controller([$controllerName, $route['method']]);
        }

        $this->storage->middleware();
    }

    // optional, to use the standard Symfony cache directory
    public function getCacheDir(): string
    {
        return __DIR__.'/../var/cache/'.$this->getEnvironment();
    }

    // optional, to use the standard Symfony logs directory
    public function getLogDir(): string
    {
        return __DIR__.'/../var/log';
    }

    public function setContainer (Container $storage): void
    {
        $this->storage = $storage;
    }

    public function getContainer (): Container
    {

        if(!$this->storage) {

            $this->storage = new Container();
        }

        return $this->storage;
    }
}

<?php

namespace App;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Container as SymfonyContainer;
use Symfony\Component\Yaml\Yaml;

require_once APP_PATH . DIRECTORY_SEPARATOR . 'src/Core/env.php';

class Container {

    public function configJson(): array
    {

    }

    public function config($name): Array
    {
        $path = implode(DIRECTORY_SEPARATOR, [APP_PATH, 'config', $name . '.yaml']);
//        $loaded = Yaml::parse(file_get_contents($path));
        $loaded = Yaml::parseFile($path);
//        $config = [];

        if(isset($loaded[$name])) {

            $config = $loaded[$name];
        }else {

            $config = $loaded;
        }

        return $config;
    }

    public function middleware()
    {
        $config = $this->config('routes');

        $middle = new Middleware();

        $middle->handleMiddleware($config);
    }

    public function model(string $name): object
    {
        $className = 'App\Model\\' . ucfirst($name);
        $instance = new $className();
        $instance->boot($this);

        return $instance;
    }
}
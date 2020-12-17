<?php

namespace App;

use Symfony\Component\Yaml\Yaml;

require_once APP_PATH . DIRECTORY_SEPARATOR . 'src/Core/env.php';

class Container {

    public function config($name) {
        $path = implode(DIRECTORY_SEPARATOR, [APP_PATH, 'config', $name . '.yaml']);
        $loaded = Yaml::parse(file_get_contents($path));
        $config = [];

        if(isset($loaded[$name])) {

            $config = $loaded[$name];
        }

        return $config;
    }

    public function middleware()
    {
        $config = $this->config('routes');

        $middle = new Middleware();

        $middle->handleMiddleware($config);
    }
}
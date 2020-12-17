<?php

namespace App;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class Middleware {

    protected Request $request;
    protected Response $response;

    public function __construct()
    {
        $this->request = Request::createFromGlobals();
        $this->response = new Response();
    }

    protected function callMiddleware(array $middleware, array $params): void
    {
        foreach ($middleware as $name) {
            $middlewareName = 'App\Middleware\\' . ucfirst($name);
            $instance = new $middlewareName($this->request, $this->response);

            $instance->params = $params;

            $instance->next(function($req, $resp) {

                $this->request = $req;
                $this->response = $resp;
            });
        }
    }

    public function handleMiddleware(array $config): void
    {
        $middleware = [];
        $routes = new RouteCollection();

        foreach ($config as $route) {
            $configMiddleware = [];

            $routes->add($route['name'], new Route($route['path']));

            if(isset($route['middleware'])) {

                $configMiddleware = $route['middleware'];
            }

            $middleware[$route['name']] = $configMiddleware;
        }

        $context = new RequestContext();
        $context->fromRequest($this->request);
        $matcher = new UrlMatcher($routes, $context);

        $attributes = $matcher->match($this->request->getPathInfo());
        $routeName = $attributes['_route'];

        $params = $attributes;

        unset($params['_route']);

        $this->callMiddleware($middleware[$routeName], $params);
    }
}
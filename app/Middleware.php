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

    protected function callMiddleware(array $middleware, array $params, ?string $method=null): void
    {
        foreach ($middleware as $name) {
            $middlewareName = 'App\Middleware\\' . ucfirst($name);
            $instance = new $middlewareName($this->request, $this->response);

            if(!$params['route_404']) {

                $params['route_404'] = '/errors/404';
            }

            $instance->params = $params;

            if($method) {

                $instance->setMethod($method);
            }

            $instance->next(function($req, $resp) {

                $this->request = $req;
                $this->response = $resp;
            });
        }
    }

    public function handleMiddleware(array $config): void
    {
        $middleware = [];
        $httpMethod = [];
        $errorRoute = null;
        $routes = new RouteCollection();

        foreach ($config as $route) {
            $configMiddleware = [];

            $routes->add($route['name'], new Route($route['path']));

            if(isset($route['middleware'])) {

                $configMiddleware = $route['middleware'];
            }

            if(isset($route['name']) && $route['name'] === 'not_found') {

                $errorRoute = $route['path'];
            }

            $middleware[$route['name']] = $configMiddleware;
            $httpMethod[$route['name']] = isset($route['http_method']) ? $route['http_method'] : null;
        }

        $context = new RequestContext();
        $context->fromRequest($this->request);
        $matcher = new UrlMatcher($routes, $context);

        $attributes = $matcher->match($this->request->getPathInfo());
        $routeName = $attributes['_route'];

        $params = $attributes;
        $params['route_404'] = $errorRoute;

        unset($params['_route']);

        $this->callMiddleware($middleware[$routeName], $params, $httpMethod[$routeName]);
    }
}
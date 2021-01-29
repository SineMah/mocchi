<?php

namespace App\Middleware;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Base {

    public array $params = [];

    protected Request $request;
    protected Response $response;
    protected string $method = 'any';

    public function __construct($request, $response) {

        $this->request = $request;
        $this->response = $response;
    }

    public function next(callable $callback): void
    {
        $callback($this->request, $this->response);
    }

    public function setMethod(string $method): Base
    {
        $this->method = $method;

        return $this;
    }
}
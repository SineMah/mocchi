<?php

namespace App\Middleware;

use App\middlewareinterface;

class Http extends Base implements middlewareinterface {

    public function next(callable $callback): void
    {
        if(!strtoupper($this->method) !== 'ANY' && !$this->request->isMethod($this->method)) {

            redirect($this->request->getSchemeAndHttpHost() . $this->params['route_404'], 302);
        }
    }
}
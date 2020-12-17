<?php

namespace App\Middleware;

use App\middlewareinterface;

class Auth extends Base implements middlewareinterface {

    protected array $path;

    public function next(callable $callback): void
    {
        $this->path = [APP_PATH, 'storage', 'users.json'];
        $users = $this->json();

        if(isset($this->params['id'])) {

            if(!in_array($this->params['id'], $users)) {

                redirect($this->request->getSchemeAndHttpHost() . '/login', 302);
            }
        }
    }

    protected function json(): array
    {
        $users = [];
        $content = file_get_contents($this->path());
        $json = json_decode($content, true);

        if($json) {

            $users = $json;
        }

        return $users;
    }

    protected function path(): string
    {

        return implode(DIRECTORY_SEPARATOR, $this->path);
    }
}
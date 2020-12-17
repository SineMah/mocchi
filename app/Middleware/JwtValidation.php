<?php

namespace App\Middleware;

use App\middlewareinterface;
use \Firebase\JWT\JWT;

class JwtValidation extends Auth implements middlewareinterface {

    protected array $path;

    public function next(callable $callback): void
    {
        $isValid = false;
        $this->path = [APP_PATH, 'storage', 'users.json'];
        $users = $this->json();
        $jwt = $this->request->cookies->get(env('SESSION_COOKIE'));

        try {
            $decoded = JWT::decode($jwt, env('JWT_SECRET'), ['HS256']);

            $isValid = in_array($decoded->data->id, $users);
        }catch(\Exception $e) {

            $this->redirect();
        }

        if(!$isValid) {

            $this->redirect();
        }
    }

    protected function redirect() {

        redirect($this->request->getSchemeAndHttpHost() . '/login', 302);
    }

//    protected function json(): array
//    {
//        $users = [];
//        $content = file_get_contents($this->path());
//        $json = json_decode($content, true);
//
//        if($json) {
//
//            $users = $json;
//        }
//
//        return $users;
//    }
//
//    protected function path(): string
//    {
//        return implode(DIRECTORY_SEPARATOR, $this->path);
//    }
}
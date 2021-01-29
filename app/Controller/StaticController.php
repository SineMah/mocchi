<?php

namespace App\Controller;

use Mocchi\Http\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class StaticController extends Controller
{
    public function e404(Request $request): Response
    {
        return new JsonResponse([
            'message'   => 'not found',
            'error'     => true
        ]);
    }
}

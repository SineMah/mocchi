<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Cookie;
use \Firebase\JWT\JWT;

class LoginController extends AbstractController
{
    public function login(): Response
    {
        $number = random_int(0, 10);

        return $this->render('Login/index.html.twig', [
            'number' => $number,
        ]);
    }

    public function logout(Request $request): Response
    {
        $response = new JsonResponse([
            'message'   => 'logged out',
            'error'     => false,
            'tokrn'     => null,
            'redirect'  => $request->getSchemeAndHttpHost() . '/login'
        ]);

        $response->headers->setCookie($this->getCookie($request, '', (new \DateTime('now'))->modify('-1 day')));

        return $response;
    }

    public function index(): Response
    {
        return $this->redirect('/login');

//        return $this->redirect('/dashboard');
    }

    public function jwt(Request $request, string $id): Response
    {
        $token = [
            'iat' => time(), //issued at
            'exp' => time() + 60 * 60, // expires at
            'iss' => $request->getSchemeAndHttpHost() . $request->getRequestUri(), // issuer
            'data' => [
                'id' => $id
            ]
        ];
        $token = JWT::encode(
            $token,
            env('JWT_SECRET'),
            env('JWT_ALGORITHM', 'HS256')
        );
        $cookie = $this->getCookie($request, $token, (new \DateTime('now'))->modify('+1 day'));

        $response = new JsonResponse([
            'message'   => 'logged in',
            'error'     => false,
            'token'     => $token,
            'redirect'  => $request->getSchemeAndHttpHost() . '/dashboard'
        ]);

        $response
            ->setStatusCode(200)
            ->headers->setCookie($cookie);

        return $response;
    }

    protected function getCookie(Request $request, string $body, \DateTime $expireAt): Cookie
    {
        return new Cookie(
            env('SESSION_COOKIE'),
            $body,
            $expireAt,
            '/',
            env('DOMAIN'),
            $request->getScheme() === 'https',
            false,
            true,
            'Strict'
        );
    }
}

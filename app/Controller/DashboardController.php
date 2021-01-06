<?php

namespace App\Controller;

use Mocchi\Http\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends Controller
{
    /**
     * @Route("/dashboard")
     */
    public function index(): Response
    {
        $number = random_int(0, 10);

        return $this->render('Login/index.html.twig', [
            'number' => $number,
        ]);
    }
}

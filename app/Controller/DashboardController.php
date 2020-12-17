<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
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

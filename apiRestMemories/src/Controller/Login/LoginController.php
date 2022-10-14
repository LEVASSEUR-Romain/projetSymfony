<?php

namespace App\Controller\Login;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LoginController extends AbstractController
{
    #[Route('/login/login', name: 'app_login_login')]
    public function index(): Response
    {
        return $this->render('login/login/index.html.twig', [
            'controller_name' => 'loginController',
        ]);
    }
}

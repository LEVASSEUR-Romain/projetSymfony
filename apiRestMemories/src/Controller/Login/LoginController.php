<?php

namespace App\Controller\Login;



use Doctrine\Persistence\ManagerRegistry;
use App\Controller\RequestBase\LoginService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\ServiceError\PostServiceError;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class LoginController extends AbstractController
{
    public function __construct(ManagerRegistry $doctrine, UserPasswordHasherInterface $passwordHasher)
    {
        $this->doctrine = $doctrine;
        $this->passwordHasher = $passwordHasher;
    }

    #[Route('/login', name: 'login_in', methods: ['GET', 'POST'])]
    public function index(Request $request, SessionInterface $session): Response
    {
        //var_dump($this->getUser());
        $this->request = $request;
        $error = $this->tchekError();
        if ($error === "") {
            $error = "ok";
        }
        //$error = "ok";
        return $this->render('api/principal.html.twig', [
            'body' => $error,
        ]);
    }

    public function tchekError()
    {
        $postService = new PostServiceError;
        $postError = $postService->postErrorToString($this->request, ['pseudo', "mdp"]);
        if ($postError !== "") {
            return $postError;
        }

        $serviceLogin = new LoginService;
        $errorLogin = $serviceLogin->loginIn($this->request, $this->doctrine, $this->passwordHasher);
        if ($errorLogin !== true) {
            return $errorLogin;
        }

        return "";
    }
    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}

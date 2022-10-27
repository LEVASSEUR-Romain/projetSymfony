<?php

namespace App\Controller\Login;



use Doctrine\Persistence\ManagerRegistry;
use App\Controller\RequestBase\LoginService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\ServiceError\PostServiceError;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use OpenApi\Attributes as OA;

class LoginController extends AbstractController
{
    public function __construct(ManagerRegistry $doctrine, UserPasswordHasherInterface $passwordHasher)
    {
        $this->doctrine = $doctrine;
        $this->passwordHasher = $passwordHasher;
    }

    #[Route('api/login', name: 'login_in', methods: ['GET', 'POST'])]
    #[OA\Tag(name: 'Login')]
    public function index(Request $request): JsonResponse
    {
        //var_dump($this->getUser());
        $this->request = $request;
        $error = $this->tchekError();
        if (count($error) !== 0) {
            new JsonResponse($error, 400);
        }
        return new JsonResponse(["statut" => "ok"]);
    }

    public function tchekError(): array
    {
        if ($this->getUser() === null) {
            $postService = new PostServiceError;
            $serviceLogin = new LoginService;
            $postError = $postService->postError($this->request, $serviceLogin->getArrayObligation());
            if (count($postError) !== 0) {
                return $postError;
            }


            $errorLogin = $serviceLogin->loginIn($this->request, $this->doctrine, $this->passwordHasher);
            if ($errorLogin !== true) {
                return $errorLogin;
            }
        }
        return [];
    }
    #[Route(path: '/logout', name: 'app_logout')]
    #[OA\Tag(name: 'Login')]
    public function logout(): JsonResponse
    {

        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
        return new JsonResponse(["statut" => "ok"]);
    }
}

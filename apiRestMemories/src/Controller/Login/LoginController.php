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

    #[Route('api/login', name: 'login_in', methods: ['POST'])]
    #[OA\Tag(name: 'Login')]
    #[OA\Post(
        description: "Connecter un utilisateur",
        parameters: [
            new OA\Parameter(
                name: LoginService::PSEUDO_TO_SEND,
                in: 'query',
                required: true,
                schema: new OA\Schema(type: 'string')
            ),
            new OA\Parameter(
                name: LoginService::PASSWORD_TO_SEND,
                in: 'query',
                required: true,
                schema: new OA\Schema(type: 'string')
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "statut ok",
                content: new OA\MediaType(
                    mediaType: "application/json",
                    example: ['statut' => 'ok'],
                ),
            ),

            new OA\Response(
                response: 400,
                description: "Error post incomplet ou pseudo existe deja ou autre Erreur",
                content: new OA\MediaType(
                    mediaType: "application/json",
                )
            ),
        ]
    )]
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
    #[Route(path: 'api/login/out', name: 'login_out', methods: ['GET'])]
    #[OA\Tag(name: 'Login')]
    #[OA\Get(
        description: "deconnecter un utilisateur",
        parameters: [],
        responses: [
            new OA\Response(
                response: 200,
                description: "statut ok",
                content: new OA\MediaType(
                    mediaType: "application/json",
                    example: ['statut' => 'ok'],
                ),
            ),
        ]
    )]
    public function logout(): JsonResponse
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
    #[Route(path: '/approuve', name: 'approuve')]
    public function approuve(): JsonResponse
    {
        return new JsonResponse(["statut" => "ok"]);
    }
}

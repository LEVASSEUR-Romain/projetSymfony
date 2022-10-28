<?php

namespace App\Controller\Login;

use OpenApi\Attributes as OA;
use Doctrine\Persistence\ManagerRegistry;
use App\Controller\Services\methodDataBase;
use App\EventListener\AccessDeniedListener;
use App\Controller\RequestBase\LoginService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class PushAndDeleteLoginController extends AbstractController
{
    private $serviceRequest;
    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
        $this->serviceRequest = new LoginService;
    }
    #[Route(path: 'api/login', name: 'login_update', methods: ['PUT'])]
    #[IsGranted("ROLE_USER")]
    #[OA\Tag(name: 'Login')]
    #[OA\Put(
        description: "modifier le mot de passe ou l'email utilisateur",
        parameters: [
            new OA\RequestBody(required: true, content: new OA\JsonContent(example: [
                LoginService::MAIL_TO_SEND => 'recto content (optionnelle)',
                LoginService::PASSWORD_TO_SEND => 'verso content (optionnelle)',
            ]), description: "envoyer un json dans le body"),
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
                response: 401,
                description: "pas connecté",
                content: new OA\MediaType(
                    mediaType: "application/json",
                    example: ["error" => AccessDeniedListener::ERROR_NOT_CONNECT],
                )
            ),
            new OA\Response(
                response: 406,
                description: "Json mal agencé, ou mot de passe avec pas assez de caractere",
            ),
        ]
    )]
    public function updtateLogin(UserPasswordHasherInterface $passwordHasher, Request $request, ValidatorInterface $validator): JsonResponse
    {
        $user = $this->getUser();
        $status = $this->serviceRequest->logUpdate($request, $this->doctrine, $validator, $passwordHasher, $user);
        if (isset($status['error'])) {
            return new JsonResponse($status, 406);
        }
        return new JsonResponse($status);
    }

    #[Route(path: 'api/login', name: 'login_delete', methods: ['DELETE'])]
    #[IsGranted("ROLE_USER")]
    #[OA\Tag(name: 'Login')]
    #[OA\Delete(
        description: "supprimer un utilisateur",
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
            new OA\Response(
                response: 401,
                description: "pas connecté",
                content: new OA\MediaType(
                    mediaType: "application/json",
                    example: ["error" => AccessDeniedListener::ERROR_NOT_CONNECT],
                )
            ),
        ]
    )]
    public function deleteLogin(ManagerRegistry $doctrine)
    {
        $user = $this->getUser();
        $this->container->get('security.token_storage')->setToken(null);
        methodDataBase::delete($doctrine, $user);
        //$this->serviceRequest->logDelete($this->doctrine, $user, $session);
        return $this->redirectToRoute('login_out');
    }
}

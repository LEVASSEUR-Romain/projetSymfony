<?php

declare(strict_types=1);

namespace App\Controller\Login;

use App\Entity\User;
use OpenApi\Attributes as OA;
use Doctrine\Persistence\ManagerRegistry;
use App\Controller\RequestBase\LoginService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\ServiceError\PostServiceError;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class CreateLoginController extends AbstractController
{

    public function __construct(ValidatorInterface $validator, ManagerRegistry $doctrine, UserPasswordHasherInterface $passwordHasher)
    {
        $this->doctrine = $doctrine;
        $this->validator = $validator;
        $this->passwordHasher = $passwordHasher;
    }
    #[Route('api/login/create', name: 'createlogin', methods: ['POST'])]
    #[OA\Tag(name: 'Login')]
    #[OA\Post(
        description: "ajouter un nouvelle utilisateur",
        parameters: [
            new OA\Parameter(
                name: LoginService::PSEUDO_TO_SEND,
                in: 'query',
                required: true,
                description: "minimum taille : " . User::MIN_LENGTH_PSEUDO . ", maximum taille : " . User::MAX_LENGTH_PSEUDO . ", le pseudo ne doit que posseder des lettres minuscule ou majuscule ou des chiffres",
                schema: new OA\Schema(type: 'string', minLength: User::MIN_LENGTH_PSEUDO, maxLength: User::MAX_LENGTH_PSEUDO, description: "le pseudo ne doit que posseder des lettres minuscule ou majuscule ou des chiffres")
            ),
            new OA\Parameter(
                name: LoginService::PASSWORD_TO_SEND,
                in: 'query',
                required: true,
                description: "minimum taille : " . User::MIN_LENGTH_PASS,
                schema: new OA\Schema(type: 'string', minLength: User::MIN_LENGTH_PASS)
            ),
            new OA\Parameter(name: LoginService::MAIL_TO_SEND, in: 'query', required: false, schema: new OA\Schema(type: 'string')),
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
        $this->request = $request;
        $error = $this->tchekError();
        if (count($error) !== 0) {
            return new JsonResponse($error, 400);
        }
        return $this->noError();
    }

    public function tchekError(): array
    {
        $postErrorService = new PostServiceError();
        $loginService = new LoginService();

        $postError = $postErrorService->postError($this->request, $loginService->getArrayObligation());
        if (count($postError) !== 0) {
            return $postError;
        }

        $errorsValidation = $loginService->pushLogin($this->request, $this->doctrine, $this->validator, $this->passwordHasher);
        if (count($errorsValidation) !== 0) {
            return $errorsValidation;
        }
        return [];
    }

    public function noError(): JsonResponse
    {
        return new JsonResponse(["statut" => "ok"]);
    }
}

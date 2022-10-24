<?php

declare(strict_types=1);

namespace App\Controller\Login;

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
    #[Route('/createlogin', name: 'createlogin', methods: ['GET', 'POST'])]
    public function index(Request $request): JsonResponse
    {
        $this->request = $request;
        $error = $this->tchekError();
        if (count($error) !== 0) {
            return new JsonResponse($error);
        }
        return $this->noError();
    }

    public function tchekError(): array
    {
        $postErrorService = new PostServiceError();
        $postError = $postErrorService->postError($this->request, ["pseudo", "mdp"]);
        if (count($postError) !== 0) {
            return $postError;
        }

        $loginService = new LoginService();
        $errorsValidation = $loginService->pushLogin($this->request, $this->doctrine, $this->validator, $this->passwordHasher);
        if ($errorsValidation !== "") {
            $arrayErrors = [];
            $arrayErrors['error'] = true;
            for ($i = 0; $i < count($errorsValidation); $i++) {
                $arrayErrors[$errorsValidation[$i]->getpropertyPath()] = $errorsValidation[$i]->getMessage();
            }
            return $arrayErrors;
        }
        return [];
    }

    public function noError(): JsonResponse
    {
        return new JsonResponse(["statut" => "ok"]);
    }
}

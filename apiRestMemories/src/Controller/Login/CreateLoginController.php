<?php

declare(strict_types=1);

namespace App\Controller\Login;

use Doctrine\Persistence\ManagerRegistry;
use App\Controller\RequestBase\LoginService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\ServiceError\PostServiceError;
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
    #[Route('/createlogin', name: 'app_login_create_login', methods: ['GET', 'POST'])]
    public function index(Request $request): Response
    {
        $this->request = $request;
        $error = $this->tchekError();
        if ($error !== "") {
            return $this->render('api/principal.html.twig', [
                'body' => $error,
            ]);
        }

        return $this->noError();
    }

    public function tchekError(): string
    {
        $postErrorService = new PostServiceError();
        $postError = $postErrorService->postErrorToString($this->request, ["pseudo", "mail", "mdp"]);
        if ($postError !== "") {
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
            $arrayError = json_encode($arrayErrors);
            return $arrayError;
        }
        return "";
    }

    public function noError(): Response
    {
        return $this->render('api/principal.html.twig', [
            'body' => "ok",
        ]);
    }
}

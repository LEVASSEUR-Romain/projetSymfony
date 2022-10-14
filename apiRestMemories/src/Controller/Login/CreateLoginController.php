<?php

namespace App\Controller\Login;

use App\Entity\Login;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class CreateLoginController extends AbstractController
{
    #[Route('/createlogin', name: 'app_login_create_login', methods: ['GET', 'POST'])]
    public function index(ValidatorInterface $validator, Request $request, ManagerRegistry $doctrine): Response
    {
        $error = $this->tchekError($validator, $request, $doctrine);
        if ($error !== "") {
            return new Response($error);
        }
        //push base de donnée
        return $this->noError($validator);
    }

    public function tchekError(ValidatorInterface $validator, Request $request, $doctrine): string
    {

        if ($request->getMethod() !== 'POST') {
            $arrayErrors['error'] = true;
            $arrayErrors['POST'] = "aucune information envoyé";
            $arrayError = json_encode([
                $arrayErrors
            ]);
            return $arrayError;
        } else {
            $pseudo = empty($request->get('pseudo'));
            $mdp = empty($request->get('mdp'));
            $mail = empty($request->get('mail'));
            if ($pseudo ||  $mdp ||  $mail) {
                $listError = "";
                $listError .= $pseudo ? "pseudo, " : "";
                $listError .= $mdp ? "mdp, " : "";
                $listError .= $mail ? "mail " : "";
                $arrayErrors['error'] = true;
                $arrayErrors['POST'] = "il manque des informations dans le post :" . $listError;
                $arrayError = json_encode([
                    $arrayErrors
                ]);
                return $arrayError;
            }
        }
        $entityLogin = new Login();
        $entityLogin->setMail($request->get('mail'));
        $entityLogin->setPassword($request->get('mdp'));
        $entityLogin->setPseudo($request->get('pseudo'));
        $entityManager = $doctrine->getManager();
        $entityManager->persist($entityLogin);
        $entityManager->flush();

        $errors = $validator->validate($entityLogin);
        if (count($errors) > 0) {
            $arrayErrors = [];
            $arrayErrors['error'] = true;
            for ($i = 0; $i < count($errors); $i++) {
                $arrayErrors[$errors[$i]->getpropertyPath()] = $errors[$i]->getMessage();
            }
            $arrayError = json_encode([
                $arrayErrors
            ]);
            return $arrayError;
        }
        return "";
    }

    public function noError(): Response
    {
        $return =    json_encode([
            'error' => false,
        ]);
        return new Response($return);
    }
}

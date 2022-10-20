<?php

declare(strict_types=1);

namespace App\Controller\RequestBase;


use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class LoginService
{
    public function pushLogin(Request $request, ManagerRegistry $doctrine, ValidatorInterface $validator, UserPasswordHasherInterface $passwordHasher): string|ConstraintViolationListInterface
    {
        $entityLogin = new User();
        $entityLogin->setMail($request->get('mail'))
            ->setPseudo($request->get('pseudo'))
            ->setPlainPassword($request->get('mdp'));
        $valid = $validator->validate($entityLogin);
        if (count($valid) > 0) {
            return $valid;
        }
        $hashedPassword =  $passwordHasher->hashPassword(
            $entityLogin,
            $request->get('mdp')
        );
        $entityLogin->setPassword($hashedPassword);

        $entityManager = $doctrine->getManager();
        $entityManager->persist($entityLogin);
        $entityManager->flush();
        return "";
    }

    public function loginIn(Request $request, ManagerRegistry $doctrine, UserPasswordHasherInterface $passwordHasher): string|bool
    {
        $error = [];
        $error["error"] = true;
        $repo = $doctrine->getRepository(User::class);
        $isPseudo =  $repo->findOneBy(["pseudo" => $request->get('pseudo')]);
        if ($isPseudo === NULL) {
            $error["pseudo"] = "le pseudo n'est pas enregistré";
            return json_encode($error);
        }
        $isValidPassword =  $passwordHasher->isPasswordValid(
            $isPseudo,
            $request->get('mdp')
        );
        if (!$isValidPassword) {
            $error["mdp"] = "le mot de passe est incorect";
            return json_encode($error);
        }
        return true;
    }
}

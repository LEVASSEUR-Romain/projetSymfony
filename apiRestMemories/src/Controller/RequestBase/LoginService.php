<?php

declare(strict_types=1);

namespace App\Controller\RequestBase;


use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use App\Controller\Services\methodDataBase;
use Symfony\Component\HttpFoundation\Request;
use App\Controller\Services\ConstraintViolationService;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class LoginService
{
    const MAIL_TO_SEND = "mail";
    const PSEUDO_TO_SEND = "pseudo";
    const PASSWORD_TO_SEND = "mdp";
    const ERROR_LOGIN = "le pseudo n'est pas enregistré";
    const ERROR_PASSWORD = "le mot de passe est incorect";


    public function getArrayObligation(): array
    {
        return [self::PASSWORD_TO_SEND, self::PSEUDO_TO_SEND];
    }

    public function pushLogin(Request $request, ManagerRegistry $doctrine, ValidatorInterface $validator, UserPasswordHasherInterface $passwordHasher): array
    {
        $entityLogin = new User();
        $entityLogin->setMail($request->get(self::MAIL_TO_SEND))
            ->setPseudo($request->get(self::PSEUDO_TO_SEND))
            ->setPlainPassword($request->get(self::PASSWORD_TO_SEND));
        $valid = $validator->validate($entityLogin);
        if (count($valid) > 0) {
            return ConstraintViolationService::toArray($valid);
        }
        $hashedPassword =  $passwordHasher->hashPassword(
            $entityLogin,
            $request->get(self::PASSWORD_TO_SEND)
        );
        $entityLogin->setPassword($hashedPassword);
        methodDataBase::push($doctrine, $entityLogin);
        return [];
    }

    public function loginIn(Request $request, ManagerRegistry $doctrine, UserPasswordHasherInterface $passwordHasher): array|bool
    {
        $error = [];
        $error["error"] = true;
        $repo = $doctrine->getRepository(User::class);
        $isPseudo =  $repo->findOneBy([self::PSEUDO_TO_SEND => $request->get(self::PSEUDO_TO_SEND)]);
        if ($isPseudo === NULL) {
            $error[self::PSEUDO_TO_SEND] = self::ERROR_LOGIN;
            return $error;
        }
        $isValidPassword =  $passwordHasher->isPasswordValid(
            $isPseudo,
            $request->get(self::PASSWORD_TO_SEND)
        );
        if (!$isValidPassword) {
            $error[self::PASSWORD_TO_SEND] = self::ERROR_PASSWORD;
            return $error;
        }
        return true;
    }
}

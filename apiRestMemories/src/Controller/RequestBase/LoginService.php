<?php

declare(strict_types=1);

namespace App\Controller\RequestBase;

use App\Entity\Login;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;


class LoginService
{
    public function pushLogin(Request $request, ManagerRegistry $doctrine, ValidatorInterface $validator)
    {
        $entityLogin = new Login();
        $entityLogin->setMail($request->get('mail'))
            ->setPassword($request->get('mdp'))
            ->setPseudo($request->get('pseudo'));
        $entityManager = $doctrine->getManager();
        $entityManager->persist($entityLogin);
        $entityManager->flush();
        return $validator->validate($entityLogin);
    }
}

<?php

namespace App\Controller\RequestBase;

use App\Entity\ListMemory;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ListMemoryService
{
    public function addList(Request $request, ManagerRegistry $doctrine, ValidatorInterface $validator, User $user)
    {
        $listMemory = new ListMemory;
        $listMemory->setName($request->get('nom'));
        if (!empty($request->get('desciption'))) {
            $listMemory->setDescription($request->get('desciption'));
        }
        $listMemory->setUserId($user);

        $valid = $validator->validate($listMemory);
        if (count($valid) > 0) {
            return $valid;
        }
        $entityManager = $doctrine->getManager();
        $entityManager->persist($listMemory);
        $entityManager->flush();
        return ["statut" => "ok"];
    }

    public function removeList(ManagerRegistry $doctrine, User $user, int $id): array
    {
        $repositoryListMemory = $doctrine->getRepository(ListMemory::class);
        $rqtMemory = $repositoryListMemory->findOneBy(['id' => $id, "user_id" => $user->getId()]);
        if ($rqtMemory === null) {
            return ['error' => true, "message" => "l'id de la liste n'appartient pas au user"];
        }
        // user connecter la list lui appartient
        /*         $repositoryListMemory = $doctrine->getRepository(ListMemory::class);
        $repositoryUser = $doctrine->getRepository(User::class);
        $rqtMemory = $repositoryListMemory->findOneBy(['id' => $request->get('id')]);
        if ($rqtMemory === null) {
            return ['error' => true, "message" => "id non trouvé"];
        }
        $rqtUser =  $repositoryUser->findOneBy(['id' => $rqtMemory->getUserId()]);
        if ($rqtUser === null) {
            return ['error' => true, "message" => "le user n'existe pas"];
        }
        if ($rqtUser->getPseudo() !== $user->getPseudo()) {
            return ['error' => true, "message" => "Cette liste n'appartient pas a l'utilisateur connected"];
        } */
        // delete
        $entityManager = $doctrine->getManager();
        $entityManager->remove($rqtMemory);
        $entityManager->flush();
        return ["statut" => "ok"];
    }
}

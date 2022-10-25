<?php

namespace App\Controller\RequestBase;

use App\Controller\Services\methodDataBase;
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
        methodDataBase::push($doctrine, $listMemory);
        return ["statut" => "ok"];
    }

    public function removeList(ManagerRegistry $doctrine, User $user, int $id): array
    {
        $repositoryListMemory = $doctrine->getRepository(ListMemory::class);
        $rqtMemory = $repositoryListMemory->findOneBy(['id' => $id, "user_id" => $user->getId()]);
        if ($rqtMemory === null) {
            return ['error' => "l'id de la liste n'appartient pas au user"];
        }
        methodDataBase::delete($doctrine, $rqtMemory);
        return ["statut" => "ok"];
    }

    public function updateList(Request $request, ManagerRegistry $doctrine, User $user, int $id): array
    {
        $repositoryListMemory = $doctrine->getRepository(ListMemory::class);
        $rqtMemory = $repositoryListMemory->findOneBy(['id' => $id, "user_id" => $user->getId()]);
        if ($rqtMemory === null) {
            return ['error' => "l'id de la liste n'appartient pas au user"];
        }
        $rqt = json_decode($request->getContent());
        if (!isset($rqt)) {
            return ["erreur" => "le json envoyer n'est pas dans le bon format"];
        }
        if (!empty($rqt->nom)) {
            $rqtMemory->setName($rqt->nom);
        }
        if (!empty($rqt->description)) {
            $rqtMemory->setDescription($rqt->description);
        }
        methodDataBase::push($doctrine, $rqtMemory);
        return ["statut" => "ok"];
    }

    public function getList(ManagerRegistry $doctrine, $id): array
    {
        $repositoryListMemory = $doctrine->getRepository(ListMemory::class);
        $rqtMemory = $repositoryListMemory->findOneBy(['id' => $id]);
        if (!isset($rqtMemory)) {
            return ['error' => "l'id correspondant n'existe pas"];
        }
        return ["name" => $rqtMemory->getName(), "description" => $rqtMemory->getDescription()];
    }

    public function getAllList(ManagerRegistry $doctrine, User $user): array
    {
        $repositoryListMemory = $doctrine->getRepository(ListMemory::class);
        $rqtMemory = $repositoryListMemory->findAll(["user_id" => $user->getId()]);
        if (!isset($rqtMemory)) {
            return ['error' => "l'utilisateur n'a pas de liste"];
        }
        $return = [];
        foreach ($rqtMemory as &$value) {
            $return[] = ["nom" => $value->getName(), "description" => $value->getDescription()];
        }
        return $return;
    }
}

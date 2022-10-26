<?php

namespace App\Controller\RequestBase;

use App\Controller\Services\ConstraintViolationService;
use App\Entity\User;
use App\Entity\ListMemory;
use Doctrine\Persistence\ManagerRegistry;
use App\Controller\Services\methodDataBase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ListMemoryService
{
    const NAME_TO_SEND = "nom";
    const DESCRIPTION_TO_SEND = "description";
    const KEY_CARD_ID = "Card_id";
    const ERROR_ID_USER = "l'id de la listMemory n'appartient pas au user ou l'id n'existe plus";
    const ERROR_ID = "l'id correspondant n'existe pas";
    const ERROR_USER = "l'utilisateur n'a pas de liste";
    const ERROR_JSON = "le json envoyer n'est pas dans le bon format";

    public function getArrayObligation()
    {
        return [self::NAME_TO_SEND];
    }

    public function addList(Request $request, ManagerRegistry $doctrine, ValidatorInterface $validator, User $user): array
    {
        $listMemory = new ListMemory;
        $listMemory->setName($request->get(self::NAME_TO_SEND));
        if (!empty($request->get(self::DESCRIPTION_TO_SEND))) {
            $listMemory->setDescription($request->get(self::DESCRIPTION_TO_SEND));
        }
        $listMemory->setUserId($user);

        $valid = $validator->validate($listMemory);
        if (count($valid) > 0) {
            return ConstraintViolationService::toArray($valid);
        }
        methodDataBase::push($doctrine, $listMemory);
        return ["statut" => "ok", "id" => $listMemory->getId()];
    }

    public function removeList(ManagerRegistry $doctrine, User $user, int $id): array
    {
        $repositoryListMemory = $doctrine->getRepository(ListMemory::class);
        $rqtMemory = $repositoryListMemory->findOneBy(['id' => $id, "user_id" => $user->getId()]);
        if ($rqtMemory === null) {
            return ['error' => self::ERROR_ID_USER];
        }
        methodDataBase::delete($doctrine, $rqtMemory);
        return ["statut" => "ok"];
    }

    public function updateList(Request $request, ManagerRegistry $doctrine, User $user, ValidatorInterface $validator, int $id): array
    {
        $repositoryListMemory = $doctrine->getRepository(ListMemory::class);
        $rqtMemory = $repositoryListMemory->findOneBy(['id' => $id, "user_id" => $user->getId()]);
        if ($rqtMemory === null) {
            return ['error' => self::ERROR_ID_USER];
        }
        $rqt = json_decode($request->getContent());
        if (!isset($rqt)) {
            return ["erreur" => self::ERROR_JSON];
        }
        if (!empty($rqt->{self::NAME_TO_SEND})) {
            $rqtMemory->setName($rqt->{self::NAME_TO_SEND});
        }
        if (!empty($rqt->{self::DESCRIPTION_TO_SEND})) {
            $rqtMemory->setDescription($rqt->{self::DESCRIPTION_TO_SEND});
        }
        $valid = $validator->validate($rqtMemory);

        if (count($valid) > 0) {
            return ConstraintViolationService::toArray($valid);
        }
        methodDataBase::push($doctrine, $rqtMemory);
        return ["statut" => "ok"];
    }

    public function getList(ManagerRegistry $doctrine, $id): array
    {
        $repositoryListMemory = $doctrine->getRepository(ListMemory::class);
        $rqtMemory = $repositoryListMemory->findOneBy(['id' => $id]);
        if (!isset($rqtMemory)) {
            return ['error' => self::ERROR_ID];
        }
        $links = $rqtMemory->getListCards()->getValues();
        $listId = [];
        foreach ($links as &$value) {
            $listId[] = $value->getCardId()->getId();
        }
        return [
            "id" => $rqtMemory->getID(),
            self::NAME_TO_SEND => $rqtMemory->getName(),
            self::DESCRIPTION_TO_SEND => $rqtMemory->getDescription(),
            self::KEY_CARD_ID => $listId,
        ];
    }

    public function getAllList(ManagerRegistry $doctrine, User $user): array
    {
        $repositoryListMemory = $doctrine->getRepository(ListMemory::class);
        $rqtMemory = $repositoryListMemory->findBy(["user_id" => $user->getId()]);
        if (!isset($rqtMemory)) {
            return ['error' => self::ERROR_USER];
        }
        $return = [];
        foreach ($rqtMemory as &$value) {
            $links = $value->getListCards()->getValues();
            $listId = [];
            foreach ($links as &$link) {
                $listId[] = $link->getCardId()->getId();
            }
            $return[] = [
                "id" => $value->getId(),
                self::NAME_TO_SEND => $value->getName(),
                self::DESCRIPTION_TO_SEND => $value->getDescription(),
                self::KEY_CARD_ID => $listId,
            ];
        }
        return $return;
    }
}

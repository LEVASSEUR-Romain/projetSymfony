<?php

namespace App\Controller\RequestBase;

use App\Entity\User;
use App\Entity\CardMemory;
use Doctrine\Persistence\ManagerRegistry;
use App\Controller\Services\methodDataBase;
use Symfony\Component\HttpFoundation\Request;

class LinkCardMemoryService
{
    const ID_LIST_TO_SEND = "list_id";
    const ID_CARD_TO_SEND = "card_id";
    const ERROR_USER_LIST = "l'utilisateur ne possede pas cette liste";
    const ERROR_ALL_ID = "un des id proposer ne match pas avec l'autre";

    public function getArrayObligation(): array
    {
        return [self::ID_CARD_TO_SEND, self::ID_LIST_TO_SEND];
    }

    private function isUserHasList($idList, $user, $doctrine): bool
    {
        $repositoryListMemory = $doctrine->getRepository(ListMemory::class);
        $rqtMemory = $repositoryListMemory->findOneBy(['id' => $idList, "user_id" => $user->getId()]);
        if (!isset($rqtMemory)) {
            return true;
        }
        return false;
    }

    public function addLink(Request $request, ManagerRegistry $doctrine, User $user): array
    {
        if ($this->isUserHasList($request->get(self::ID_LIST_TO_SEND), $user, $doctrine)) {
            $cardMemory = new CardMemory;
            $cardMemory->setCardId($request->get(self::ID_CARD_TO_SEND));
            $cardMemory->setMemoryId($request->get(self::ID_LIST_TO_SEND));
            methodDataBase::push($doctrine, $cardMemory);
            return ["statut" => "ok"];
        }
        return ["error" => self::ERROR_USER_LIST];
    }

    public function removeLink(ManagerRegistry $doctrine, User $user, int $idList, int $idCard): array
    {
        if ($this->isUserHasList($idList, $user, $doctrine)) {
            $repositoryCardMemory = $doctrine->getRepository(CardMemory::class);
            $rqtCardMemory = $repositoryCardMemory->findOneBy(['memory_id' => $idList, "card_id" => $idCard]);
            if ($rqtCardMemory === null) {
                return ['error' => self::ERROR_ALL_ID];
            }
            methodDataBase::delete($doctrine, $rqtCardMemory);
            return ["statut" => "ok"];
        }
        return ["error" => self::ERROR_USER_LIST];
    }
}

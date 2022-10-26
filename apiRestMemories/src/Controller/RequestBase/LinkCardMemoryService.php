<?php

namespace App\Controller\RequestBase;

use App\Entity\User;
use App\Entity\Cards;
use App\Entity\ListCard;
use App\Entity\ListMemory;
use Doctrine\Persistence\ManagerRegistry;
use App\Controller\Services\methodDataBase;

class LinkCardMemoryService
{
    const ERROR_USER_LIST = "l'utilisateur ne possede pas cette liste";
    const ERROR_ALL_ID = "un des id proposer ne match pas avec l'autre";
    private function isUserHasList($idList, $user, $doctrine): bool
    {
        $repositoryListMemory = $doctrine->getRepository(ListMemory::class);
        $rqtMemory = $repositoryListMemory->findOneBy(['id' => $idList, "user_id" => $user->getId()]);
        if (!isset($rqtMemory)) {
            return false;
        }
        return true;
    }

    public function addLink(ManagerRegistry $doctrine, User $user, int $idList, int $idCard): array
    {
        if ($this->isUserHasList($idList, $user, $doctrine)) {
            $cardMemory = new ListCard;
            $repositoryListMemory = $doctrine->getRepository(ListMemory::class);
            $repositoryCard = $doctrine->getRepository(Cards::class);
            $rqtMemory = $repositoryListMemory->findOneBy(['id' => $idList]);
            $rqtCard = $repositoryCard->findOneBy(['id' => $idCard]);
            if (!isset($rqtMemory) && !isset($rqtCard)) {
                return ['error' => self::ERROR_ALL_ID];
            }
            $cardMemory->setCardId($rqtCard);
            $cardMemory->setListId($rqtMemory);
            methodDataBase::push($doctrine, $cardMemory);
            return ["statut" => "ok"];
        }
        return ["error" => self::ERROR_USER_LIST];
    }

    public function removeLink(ManagerRegistry $doctrine, User $user, int $idList, int $idCard): array
    {
        if ($this->isUserHasList($idList, $user, $doctrine)) {
            $repositoryCardMemory = $doctrine->getRepository(ListCard::class);
            $rqtCardMemory = $repositoryCardMemory->findOneBy(['list_id' => $idList, "card_id" => $idCard]);
            if ($rqtCardMemory === null) {
                return ['error' => self::ERROR_ALL_ID];
            }
            methodDataBase::delete($doctrine, $rqtCardMemory);
            return ["statut" => "ok"];
        }
        return ["error" => self::ERROR_USER_LIST];
    }
}

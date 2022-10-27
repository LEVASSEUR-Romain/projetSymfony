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
    const ERROR_ALL_ID = "un des id proposé ne match pas avec l'autre";
    private function isUserHasList($idList, $user): bool
    {
        $list = $user->getListMemories();
        $existe = $list->exists(
            function ($key, $value) use ($idList) {
                return $value->getId() === $idList;
            }
        );
        return $existe;
    }

    public function addLink(ManagerRegistry $doctrine, User $user, int $idList, int $idCard): array
    {
        if ($this->isUserHasList($idList, $user)) {
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
        if ($this->isUserHasList($idList, $user)) {
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

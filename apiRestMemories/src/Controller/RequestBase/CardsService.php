<?php

namespace App\Controller\RequestBase;

use App\Controller\Services\ConstraintViolationService;
use App\Entity\User;
use App\Entity\Cards;
use Doctrine\Persistence\ManagerRegistry;
use App\Controller\Services\methodDataBase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CardsService
{
    const FRONT_TO_SEND = "devant";
    const BACK_TO_SEND = "derrière";
    const FRONT_PERSO_TO_SEND = "devant_perso";
    const BACK_PERSO_TO_SEND = "derrière_perso";
    const ERROR_ID_USER = "l'id de la card n'appartient pas au user ou l'id n'existe plus";
    const ERROR_ID = "l'id correspondant n'existe pas";
    const ERROR_USER = "l'utilisateur n'a pas de liste";
    const ERROR_JSON = "le json envoyer n'est pas dans le bon format";


    public function getArrayObligation(): array
    {
        return [self::FRONT_TO_SEND, self::BACK_TO_SEND];
    }


    public function addCard(Request $request, ManagerRegistry $doctrine, ValidatorInterface $validator, User $user)
    {
        $card = new Cards;
        $card->setFront($request->get(self::FRONT_TO_SEND));
        $card->setBack($request->get(self::BACK_TO_SEND));
        if (!empty($request->get(self::FRONT_PERSO_TO_SEND))) {
            $card->setPersoFront($request->get(self::FRONT_PERSO_TO_SEND));
        }
        if (!empty($request->get(self::BACK_PERSO_TO_SEND))) {
            $card->setPersoBack($request->get(self::BACK_PERSO_TO_SEND));
        }
        $card->setUserId($user);

        $valid = $validator->validate($card);
        if (count($valid) > 0) {
            return ConstraintViolationService::toArray($valid);
        }
        methodDataBase::push($doctrine, $card);
        return ["statut" => "ok", "id" => $card->getId()];
    }

    public function removeCard(ManagerRegistry $doctrine, User $user, int $id): array
    {
        $repositoryCards = $doctrine->getRepository(Cards::class);
        $rqtCard = $repositoryCards->findOneBy(['id' => $id, "user_id" => $user->getId()]);
        if ($rqtCard === null) {
            return ['error' => self::ERROR_ID_USER];
        }
        methodDataBase::delete($doctrine, $rqtCard);
        return ["statut" => "ok"];
    }

    public function updateCard(Request $request, ManagerRegistry $doctrine, User $user, ValidatorInterface $validator, int $id): array
    {
        $repositoryCards = $doctrine->getRepository(Cards::class);
        $rqtCard = $repositoryCards->findOneBy(['id' => $id, "user_id" => $user->getId()]);
        if ($rqtCard === null) {
            return ['error' => self::ERROR_ID_USER];
        }
        $rqt = json_decode($request->getContent());
        if (!isset($rqt)) {
            return ["erreur" => self::ERROR_JSON];
        }
        if (!empty($rqt->{self::FRONT_TO_SEND})) {
            $rqtCard->setFront($rqt->{self::FRONT_TO_SEND});
        }
        if (!empty($rqt->{self::BACK_TO_SEND})) {
            $rqtCard->setBack($rqt->{self::BACK_TO_SEND});
        }
        if (!empty($rqt->{self::BACK_PERSO_TO_SEND})) {
            $rqtCard->setPersoBack($rqt->{self::BACK_PERSO_TO_SEND});
        }
        if (!empty($rqt->{self::FRONT_PERSO_TO_SEND})) {
            $rqtCard->setPersoFront($rqt->{self::FRONT_PERSO_TO_SEND});
        }
        $valid = $validator->validate($rqtCard);
        if (count($valid) > 0) {
            return ConstraintViolationService::toArray($valid);
        }
        methodDataBase::push($doctrine, $rqtCard);
        return ["statut" => "ok"];
    }

    public function getCard(ManagerRegistry $doctrine, $id): array
    {
        $repositoryCard = $doctrine->getRepository(Cards::class);
        $rqtCard = $repositoryCard->findOneBy(['id' => $id]);
        if (!isset($rqtCard)) {
            return ['error' => self::ERROR_ID];
        }
        return [
            self::FRONT_TO_SEND => $rqtCard->getFront(),
            self::BACK_TO_SEND => $rqtCard->getBack(),
            self::FRONT_PERSO_TO_SEND => $rqtCard->getPersoFront(),
            self::BACK_PERSO_TO_SEND => $rqtCard->getPersoBack(),
        ];
    }

    public function getAllCard(ManagerRegistry $doctrine, User $user): array
    {
        $repositoryCard = $doctrine->getRepository(Cards::class);
        $rqtCard = $repositoryCard->findAll(["user_id" => $user->getId()]);
        if (!isset($rqtCard)) {
            return ['error' => self::ERROR_USER];
        }
        $return = [];
        foreach ($rqtCard as &$value) {
            $return[] = [
                "id" => $value->getId(),
                self::FRONT_TO_SEND => $value->getFront(),
                self::BACK_TO_SEND => $value->getBack(),
                self::FRONT_PERSO_TO_SEND => $value->getPersoFront(),
                self::BACK_PERSO_TO_SEND => $value->getPersoBack(),
            ];
        }
        return $return;
    }
}

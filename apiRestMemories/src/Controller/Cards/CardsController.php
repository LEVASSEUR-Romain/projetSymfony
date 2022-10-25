<?php

namespace App\Controller\Cards;

use Doctrine\Persistence\ManagerRegistry;
use App\Controller\RequestBase\CardsService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\ServiceError\PostServiceError;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CardsController extends AbstractController
{
    private $postError;
    private $serviceRequest;
    public function __construct()
    {
        $this->postError = new PostServiceError;
        $this->serviceRequest = new CardsService;
    }
    #[Route('/card', name: 'add_card', methods: ['POST'])]
    #[IsGranted("ROLE_USER")]
    public function addCard(Request $request, ManagerRegistry $doctrine, ValidatorInterface $validator): JsonResponse
    {
        $error = $this->postError->postError($request, $this->serviceRequest->getArrayObligation());
        if (count($error) !== 0) {
            return new JsonResponse($error);
        }

        $user = $this->getUser();
        $reponse = $this->serviceRequest->addCard($request, $doctrine, $validator, $user);
        return new JsonResponse($reponse);
    }

    #[Route('/card/{id}', name: 'delete_card', methods: ['DELETE'], requirements: ["id" => "^[0-9]+$"])]
    #[IsGranted("ROLE_USER")]
    public function deleteCard(ManagerRegistry $doctrine, int $id): JsonResponse
    {
        $user = $this->getUser();
        $reponse = $this->serviceRequest->removeCard($doctrine, $user, $id);
        return new JsonResponse($reponse);
    }

    #[Route('/card/{id}', name: 'update_card', methods: ['PUT'], requirements: ["id" => "^[0-9]+$"])]
    #[IsGranted("ROLE_USER")]
    public function updateCard(Request $request, ManagerRegistry $doctrine, ValidatorInterface $validator, int $id): JsonResponse
    {
        $user = $this->getUser();
        $response = $this->serviceRequest->updateCard($request, $doctrine, $user, $validator, $id);
        return new JsonResponse($response);
    }

    #[Route('/card/{id}', name: 'read_card', methods: ['GET'], requirements: ["id" => "^[0-9]+$"])]
    #[IsGranted("ROLE_USER")]
    public function readCard(ManagerRegistry $doctrine, int $id): JsonResponse
    {
        $response = $this->serviceRequest->getCard($doctrine, $id);
        return new JsonResponse($response);
    }

    #[Route('/card/all', name: 'read_card_all', methods: ['GET'])]
    #[IsGranted("ROLE_USER")]
    public function readAllCard(ManagerRegistry $doctrine): JsonResponse
    {

        $user = $this->getUser();
        $response = $this->serviceRequest->getAllCard($doctrine, $user);
        return new JsonResponse($response);
    }
}

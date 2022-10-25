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
    // TODO
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
        $error = $this->postError->postError($request, ['nom']);
        if (count($error) !== 0) {
            return new JsonResponse($error);
        }
        return $this->render('api/principal.html.twig', [
            'body' => $error,
        ]);
    }

    #[Route('/card/{id}', name: 'delete_card', methods: ['DELETE'])]
    #[IsGranted("ROLE_USER")]
    public function deleteCard(): JsonResponse
    {
        $error = "";
        return $this->render('api/principal.html.twig', [
            'body' => $error,
        ]);
    }

    #[Route('/card/{id}', name: 'update_card', methods: ['PUT'])]
    #[IsGranted("ROLE_USER")]
    public function updateCard(): JsonResponse
    {
        $error = "";
        return $this->render('api/principal.html.twig', [
            'body' => $error,
        ]);
    }

    #[Route('/card/{id}', name: 'read_card', methods: ['GET'])]
    #[IsGranted("ROLE_USER")]
    public function readCard(): JsonResponse
    {
        $error = "";
        return $this->render('api/principal.html.twig', [
            'body' => $error,
        ]);
    }

    #[Route('/card/all', name: 'read_card_all', methods: ['GET'])]
    #[IsGranted("ROLE_USER")]
    public function readAllCard(): JsonResponse
    {
        $error = "";
        return $this->render('api/principal.html.twig', [
            'body' => $error,
        ]);
    }
}

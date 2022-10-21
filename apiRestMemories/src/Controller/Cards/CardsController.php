<?php

namespace App\Controller\Cards;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class CardsController extends AbstractController
{
    #[Route('/cards/cards', name: 'app_cards_cards')]
    public function index(): JsonResponse
    {
        $error = "";
        return $this->render('api/principal.html.twig', [
            'body' => $error,
        ]);
    }
}

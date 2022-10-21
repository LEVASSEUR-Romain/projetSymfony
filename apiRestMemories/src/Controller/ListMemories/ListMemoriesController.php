<?php

namespace App\Controller\ListMemories;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ListMemoriesController extends AbstractController
{
    #[Route('/list/memories/list/memories', name: 'app_list_memories_list_memories')]
    public function index(): JsonResponse
    {
        $error = "";
        return $this->render('api/principal.html.twig', [
            'body' => $error,
        ]);
    }
}

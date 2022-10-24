<?php

namespace App\Controller\ListMemories;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\RequestBase\ListMemoryService;
use App\Controller\ServiceError\PostServiceError;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class ListMemoriesController extends AbstractController
{
    private $postError;
    private $serviceRequest;
    public function __construct()
    {
        $this->postError = new PostServiceError;
        $this->serviceRequest = new ListMemoryService;
    }

    #[Route('/list-memory', name: 'add_memories', methods: ['POST'])]
    #[IsGranted("ROLE_USER")]
    public function addList(Request $request, ManagerRegistry $doctrine, ValidatorInterface $validator): JsonResponse
    {
        $error = $this->postError->postError($request, ['nom']);
        if (count($error) !== 0) {
            return new JsonResponse($error);
        }
        // add to list
        $user = $this->getUser();
        $reponse = $this->serviceRequest->addList($request, $doctrine, $validator, $user);
        return new JsonResponse($reponse);
    }

    #[Route('/list-memory/{id}', name: 'remove_memories', methods: ['DELETE'], requirements: ["id" => "^[0-9]+$"])]
    #[IsGranted("ROLE_USER")]
    public function removeList(Request $request, ManagerRegistry $doctrine, int $id): JsonResponse
    {
        /*       if ($id === null || preg_match("/^[0-9]+$/", $id)) {
            return new JsonResponse(['error' => "Vous n'avez pas indiquer l'id"], 400);
        }
        */
        /** @var User $user */
        $user = $this->getUser();
        $reponse = $this->serviceRequest->removeList($doctrine, $user, $id);
        return new JsonResponse($reponse);
    }

    #[Route('/list-memory/{id}', name: 'update_memories', methods: ['PUT'], requirements: ["id" => "^[0-9]+$"])]
    #[IsGranted("ROLE_USER")]
    public function updateList(): Response
    {
        $error = "";
        return $this->render('api/principal.html.twig', [
            'body' => $error,
        ]);
    }

    #[Route('/list-memory/{id}', name: 'list_memories', methods: ['GET'], requirements: ["id" => "^[0-9]+$"])]
    #[IsGranted("ROLE_USER")]
    public function getList(): Response
    {
        $error = "";
        return $this->render('api/principal.html.twig', [
            'body' => $error,
        ]);
    }
}

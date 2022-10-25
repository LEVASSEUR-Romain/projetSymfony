<?php

namespace App\Controller\ListMemories;


use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
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
        $error = $this->postError->postError($request, $this->serviceRequest->getArrayObligation());
        if (count($error) !== 0) {
            return new JsonResponse($error);
        }
        $user = $this->getUser();
        $reponse = $this->serviceRequest->addList($request, $doctrine, $validator, $user);
        return new JsonResponse($reponse);
    }

    #[Route('/list-memory/{id}', name: 'delete_memories', methods: ['DELETE'], requirements: ["id" => "^[0-9]+$"])]
    #[IsGranted("ROLE_USER")]
    public function removeList(ManagerRegistry $doctrine, int $id): JsonResponse
    {
        $user = $this->getUser();
        $reponse = $this->serviceRequest->removeList($doctrine, $user, $id);
        return new JsonResponse($reponse);
    }

    #[Route('/list-memory/{id}', name: 'update_memories', methods: ['PUT'], requirements: ["id" => "^[0-9]+$"])]
    #[IsGranted("ROLE_USER")]
    public function updateList(Request $request, ManagerRegistry $doctrine, ValidatorInterface $validator, int $id): JsonResponse
    {
        $user = $this->getUser();
        $response = $this->serviceRequest->updateList($request, $doctrine, $user, $validator, $id);
        return new JsonResponse($response);
    }

    #[Route('/list-memory/{id}', name: 'read_memories', methods: ['GET'], requirements: ["id" => "^[0-9]+$"])]
    public function getList(ManagerRegistry $doctrine, int $id): JsonResponse
    {
        $response = $this->serviceRequest->getList($doctrine, $id);
        return new JsonResponse($response);
    }

    #[Route('/list-memory/all', name: 'read_memories_all', methods: ['GET'], requirements: ["id" => "^[0-9]+$"])]
    #[IsGranted("ROLE_USER")]
    public function getAllList(ManagerRegistry $doctrine): JsonResponse
    {
        $user = $this->getUser();
        $response = $this->serviceRequest->getAllList($doctrine, $user);
        return new JsonResponse($response);
    }
}

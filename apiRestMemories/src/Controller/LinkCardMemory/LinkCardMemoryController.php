<?php

namespace App\Controller\LinkCardMemory;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\ServiceError\PostServiceError;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Controller\RequestBase\LinkCardMemoryService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class LinkCardMemoryController extends AbstractController
{
    private $serviceRequest;
    private $postError;
    public function __construct()
    {
        $this->postError = new PostServiceError;
        $this->serviceRequest = new LinkCardMemoryService;
    }
    #[Route('/link-list-card', name: 'add_link', methods: ['POST'])]
    #[IsGranted("ROLE_USER")]
    public function addToLink(Request $request, ManagerRegistry $doctrine): JsonResponse
    {
        $error = $this->postError->postError($request, $this->serviceRequest->getArrayObligation());
        if (count($error) !== 0) {
            return new JsonResponse($error);
        }
        $user = $this->getUser();
        $reponse = $this->serviceRequest->addLink($request, $doctrine, $user);
        return new JsonResponse($reponse);
    }

    #[Route(
        '/link-list-card/{idList}/{idCard}',
        name: 'remove_link',
        methods: ['DELETE'],
        requirements: ["idList" => "^[0-9]+$", "idCard" => "^[0-9]+$"]
    )]
    #[IsGranted("ROLE_USER")]
    public function removeToLink(ManagerRegistry $doctrine, int $idList, int $idCard): JsonResponse
    {
        $user = $this->getUser();
        $reponse = $this->serviceRequest->removeLink($doctrine, $user, $idList, $idCard);
        return new JsonResponse($reponse);
    }
}

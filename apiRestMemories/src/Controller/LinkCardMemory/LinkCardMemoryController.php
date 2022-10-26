<?php

namespace App\Controller\LinkCardMemory;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Controller\RequestBase\LinkCardMemoryService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class LinkCardMemoryController extends AbstractController
{
    private $serviceRequest;
    public function __construct()
    {
        $this->serviceRequest = new LinkCardMemoryService;
    }
    #[Route(
        '/link-list-card/{idList}/{idCard}',
        name: 'add_link',
        methods: ['GET', 'POST'],
        requirements: ["idList" => "^[0-9]+$", "idCard" => "^[0-9]+$"]
    )]
    #[IsGranted("ROLE_USER")]
    public function addToLink(ManagerRegistry $doctrine, int $idList, int $idCard): JsonResponse
    {
        $user = $this->getUser();
        $reponse = $this->serviceRequest->addLink($doctrine, $user, $idList, $idCard);
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

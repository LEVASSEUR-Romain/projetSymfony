<?php

namespace App\Controller\LinkCardMemory;

use OpenApi\Attributes as OA;
use Doctrine\Persistence\ManagerRegistry;
use App\EventListener\AccessDeniedListener;
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
        'api/link-list-card/{idList}/{idCard}',
        name: 'add_link',
        methods: ['GET', 'POST'],
        requirements: ["idList" => "^[0-9]+$", "idCard" => "^[0-9]+$"]
    )]
    #[IsGranted("ROLE_USER")]
    #[OA\Tag(name: 'Link_List_Card')]
    #[OA\Get(
        description: "lier un card a une liste",
        parameters: [
            new OA\Parameter(name: 'idList', in: 'path', required: true, schema: new OA\Schema(type: 'int')),
            new OA\Parameter(name: 'idCard', in: 'path', required: true, schema: new OA\Schema(type: 'int')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "return statut ok",
                content: new OA\MediaType(
                    mediaType: "application/json",
                    example: [
                        "statut" => "ok"
                    ],
                ),
            ),
            new OA\Response(
                response: 400,
                description: "utilisateur ne possede pas la carte ou la liste",
                content: new OA\MediaType(
                    mediaType: "application/json",
                    example: ["error" => LinkCardMemoryService::ERROR_ALL_ID],
                )
            ),
            new OA\Response(
                response: 401,
                description: "pas connecté",
                content: new OA\MediaType(
                    mediaType: "application/json",
                    example: ["error" => AccessDeniedListener::ERROR_NOT_CONNECT],
                )
            )
        ]
    )]
    #[OA\Post(
        description: "lier un card a une liste",
        parameters: [
            new OA\Parameter(name: 'idList', in: 'path', required: true, schema: new OA\Schema(type: 'int')),
            new OA\Parameter(name: 'idCard', in: 'path', required: true, schema: new OA\Schema(type: 'int')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "utilisateur ne possede pas la carte ou la liste",
                content: new OA\MediaType(
                    mediaType: "application/json",
                    example: [
                        "statut" => "ok"
                    ],
                ),
            ),
            new OA\Response(
                response: 400,
                description: "utilisateur ne possede pas la carte ou la liste",
                content: new OA\MediaType(
                    mediaType: "application/json",
                    example: ["error" => LinkCardMemoryService::ERROR_ALL_ID],
                )
            ),
            new OA\Response(
                response: 401,
                description: "pas connecté",
                content: new OA\MediaType(
                    mediaType: "application/json",
                    example: ["error" => AccessDeniedListener::ERROR_NOT_CONNECT],
                )
            )
        ]
    )]
    public function addToLink(ManagerRegistry $doctrine, int $idList, int $idCard): JsonResponse
    {
        $user = $this->getUser();
        $reponse = $this->serviceRequest->addLink($doctrine, $user, $idList, $idCard);
        if (isset($response['error'])) {
            return new JsonResponse($reponse, 400);
        }
        return new JsonResponse($reponse);
    }

    #[Route(
        'api/link-list-card/{idList}/{idCard}',
        name: 'remove_link',
        methods: ['DELETE'],
        requirements: ["idList" => "^[0-9]+$", "idCard" => "^[0-9]+$"]
    )]
    #[IsGranted("ROLE_USER")]
    #[OA\Tag(name: 'Link_List_Card')]
    #[OA\Delete(
        description: "suprimer le lien entre card et list",
        parameters: [
            new OA\Parameter(name: 'idList', in: 'path', required: true, schema: new OA\Schema(type: 'int')),
            new OA\Parameter(name: 'idCard', in: 'path', required: true, schema: new OA\Schema(type: 'int')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Retourne un statut ok",
                content: new OA\MediaType(
                    mediaType: "application/json",
                    example: [
                        "statut" => "ok"
                    ],
                ),
            ),
            new OA\Response(
                response: 400,
                description: "utilisateur ne possede pas la carte ou la liste",
                content: new OA\MediaType(
                    mediaType: "application/json",
                    example: ["error" => LinkCardMemoryService::ERROR_ALL_ID],
                )
            ),
            new OA\Response(
                response: 401,
                description: "pas connecté",
                content: new OA\MediaType(
                    mediaType: "application/json",
                    example: ["error" => AccessDeniedListener::ERROR_NOT_CONNECT],
                )
            )
        ]
    )]
    public function removeToLink(ManagerRegistry $doctrine, int $idList, int $idCard): JsonResponse
    {
        $user = $this->getUser();
        $reponse = $this->serviceRequest->removeLink($doctrine, $user, $idList, $idCard);
        if (isset($response['error'])) {
            return new JsonResponse($reponse, 400);
        }
        return new JsonResponse($reponse);
    }
}

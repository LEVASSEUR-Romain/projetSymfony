<?php

namespace App\Controller\ListMemories;


use OpenApi\Attributes as OA;
use Doctrine\Persistence\ManagerRegistry;
use App\EventListener\AccessDeniedListener;
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

    #[Route('api/list-memory', name: 'add_memories', methods: ['POST'])]
    #[IsGranted("ROLE_USER")]
    #[OA\Tag(name: 'List_Card')]
    #[OA\Post(
        description: "ajouter une list",
        parameters: [
            new OA\Parameter(name: ListMemoryService::NAME_TO_SEND, in: 'query', required: true, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: ListMemoryService::DESCRIPTION_TO_SEND, in: 'query', required: false, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "statut ok",
                content: new OA\MediaType(
                    mediaType: "application/json",
                    example: ['statut' => 'ok'],
                ),
            ),
            new OA\Response(
                response: 400,
                description: "Erreur dans le post",
                content: new OA\MediaType(
                    mediaType: "application/json",
                    example: ["error" => PostServiceError::ERROR_EMPTY_POST],
                )
            ),
            new OA\Response(
                response: 406,
                description: "Erreur dans la requete",
                content: new OA\MediaType(
                    mediaType: "application/json",
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
    public function addList(Request $request, ManagerRegistry $doctrine, ValidatorInterface $validator): JsonResponse
    {
        $error = $this->postError->postError($request, $this->serviceRequest->getArrayObligation());
        if (count($error) !== 0) {
            return new JsonResponse($error, 400);
        }
        $user = $this->getUser();
        $reponse = $this->serviceRequest->addList($request, $doctrine, $validator, $user);
        if (isset($response['error'])) {
            return new JsonResponse($response, 406);
        }
        return new JsonResponse($reponse);
    }

    #[Route('api/list-memory/{id}', name: 'delete_memories', methods: ['DELETE'], requirements: ["id" => "^[0-9]+$"])]
    #[IsGranted("ROLE_USER")]
    #[OA\Tag(name: 'List_Card')]
    #[OA\Delete(
        description: "ajouter une list",
        parameters: [
            new OA\Parameter(name: "id", in: 'path', required: true, schema: new OA\Schema(type: 'int')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "statut ok",
                content: new OA\MediaType(
                    mediaType: "application/json",
                    example: ['statut' => 'ok'],
                ),
            ),
            new OA\Response(
                response: 400,
                description: "utilisateur ne possede pas la liste",
                content: new OA\MediaType(
                    mediaType: "application/json",
                    example: ["error" => ListMemoryService::ERROR_ID_USER],
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
    public function removeList(ManagerRegistry $doctrine, int $id): JsonResponse
    {
        $user = $this->getUser();
        $reponse = $this->serviceRequest->removeList($doctrine, $user, $id);
        if (isset($response['error'])) {
            return new JsonResponse($response, 400);
        }
        return new JsonResponse($reponse);
    }

    #[Route('api/list-memory/{id}', name: 'update_memories', methods: ['PUT'], requirements: ["id" => "^[0-9]+$"])]
    #[IsGranted("ROLE_USER")]
    #[OA\Tag(name: 'List_Card')]
    #[OA\Put(
        description: "Modifier une liste",
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'int')),
            new OA\RequestBody(required: true, content: new OA\JsonContent(example: [
                ListMemoryService::NAME_TO_SEND => 'nom (optionnelle)',
                ListMemoryService::DESCRIPTION_TO_SEND => 'description (optionnelle)',
            ]), description: "envoyer un json dans le body"),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Retourne statut ok",
                content: new OA\MediaType(
                    mediaType: "application/json",
                    example: [
                        "statut" => "ok"
                    ],
                ),
            ),
            new OA\Response(
                response: 400,
                description: "utilisateur ne possede pas la liste",
                content: new OA\MediaType(
                    mediaType: "application/json",
                    example: ["error" => ListMemoryService::ERROR_ID_USER],
                )
            ),
            new OA\Response(
                response: 406,
                description: "Json non valide",
                content: new OA\MediaType(
                    mediaType: "application/json",
                    example: ["error" => ListMemoryService::ERROR_JSON],
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
    public function updateList(Request $request, ManagerRegistry $doctrine, ValidatorInterface $validator, int $id): JsonResponse
    {
        $user = $this->getUser();
        $response = $this->serviceRequest->updateList($request, $doctrine, $user, $validator, $id);
        if (isset($response['error'])) {
            return new JsonResponse($response, 400);
        }
        if (isset($response['error']) && $response['error'] === ListMemoryService::ERROR_JSON) {
            return new JsonResponse($response, 406);
        }
        return new JsonResponse($response);
    }

    #[Route('api/list-memory/{id}', name: 'read_memories', methods: ['GET'], requirements: ["id" => "^[0-9]+$"])]
    #[IsGranted("ROLE_USER")]
    #[OA\Tag(name: 'List_Card')]
    #[OA\Get(
        description: "lire une liste",
        parameters: [
            new OA\Parameter(name: "id", in: 'path', required: true, schema: new OA\Schema(type: 'int')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "statut ok",
                content: new OA\MediaType(
                    mediaType: "application/json",
                    example: [
                        'id' => 2, ListMemoryService::NAME_TO_SEND => "nom",
                        ListMemoryService::DESCRIPTION_TO_SEND => "description",
                        ListMemoryService::KEY_CARD_ID => [1, 2]
                    ],
                ),
            ),
            new OA\Response(
                response: 400,
                description: "Erreur l'id n'existe pas ou n'appartient pas a l'utilisateur",
                content: new OA\MediaType(
                    mediaType: "application/json",
                    example: ["error" => ListMemoryService::ERROR_ID],
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
    public function getList(ManagerRegistry $doctrine, int $id): JsonResponse
    {
        $response = $this->serviceRequest->getList($doctrine, $id);
        if (isset($response['error'])) {
            return new JsonResponse($response, 400);
        }
        return new JsonResponse($response);
    }

    #[Route('api/list-memory', name: 'read_memories_all', methods: ['GET'], requirements: ["id" => "^[0-9]+$"])]
    #[IsGranted("ROLE_USER")]
    #[OA\Tag(name: 'List_Card')]
    #[OA\Get(
        description: "lire toute les listes d'un utilisateur",
        parameters: [],
        responses: [
            new OA\Response(
                response: 200,
                description: "statut ok",
                content: new OA\MediaType(
                    mediaType: "application/json",
                    example: [[
                        'id' => 2, ListMemoryService::NAME_TO_SEND => "nom",
                        ListMemoryService::DESCRIPTION_TO_SEND => "description",
                        ListMemoryService::KEY_CARD_ID => [1, 2]
                    ], [
                        'id' => 3, ListMemoryService::NAME_TO_SEND => "nom",
                        ListMemoryService::DESCRIPTION_TO_SEND => "description",
                        ListMemoryService::KEY_CARD_ID => [1, 5]
                    ]],
                ),
            ),
            new OA\Response(
                response: 400,
                description: "Erreur l'id n'existe pas ou n'appartient pas a l'utilisateur",
                content: new OA\MediaType(
                    mediaType: "application/json",
                    example: ["error" => ListMemoryService::ERROR_ID],
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
    public function getAllList(ManagerRegistry $doctrine): JsonResponse
    {
        $user = $this->getUser();
        $response = $this->serviceRequest->getAllList($doctrine, $user);
        if (isset($response['error'])) {
            return new JsonResponse($response, 400);
        }
        return new JsonResponse($response);
    }
}

<?php

namespace App\Controller\Cards;

use OpenApi\Attributes as OA;
use Doctrine\Persistence\ManagerRegistry;
use App\Controller\RequestBase\CardsService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\ServiceError\PostServiceError;
use App\EventListener\AccessDeniedListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CardsController extends AbstractController
{
    // ToDO add card with id list
    private $postError;
    private $serviceRequest;
    public function __construct()
    {
        $this->postError = new PostServiceError;
        $this->serviceRequest = new CardsService;
    }
    #[Route('api/card', name: 'add_card', methods: ['POST'])]
    #[IsGranted("ROLE_USER")]
    #[OA\Tag(name: 'Card')]
    #[OA\Post(
        description: "ajouter une nouvelle carte",
        parameters: [
            new OA\Parameter(name: CardsService::FRONT_TO_SEND, in: 'query', required: true, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: CardsService::BACK_TO_SEND, in: 'query', required: true, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: CardsService::FRONT_PERSO_TO_SEND, in: 'query', required: false, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: CardsService::BACK_PERSO_TO_SEND, in: 'query', required: false, schema: new OA\Schema(type: 'string')),
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
                description: "Post incomplet",
                content: new OA\MediaType(
                    mediaType: "application/json",
                    example: ["error" => PostServiceError::ERROR_EMPTY_POST],
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
    public function addCard(Request $request, ManagerRegistry $doctrine, ValidatorInterface $validator): JsonResponse
    {
        $error = $this->postError->postError($request, $this->serviceRequest->getArrayObligation());
        if (count($error) !== 0) {
            return new JsonResponse($error, 400);
        }

        $user = $this->getUser();
        $reponse = $this->serviceRequest->addCard($request, $doctrine, $validator, $user);
        return new JsonResponse($reponse);
    }


    #[Route('api/{idList}/card', name: 'add_card_by_list', methods: ['POST'], requirements: ["idList" => "^[0-9]+$"])]
    #[IsGranted("ROLE_USER")]
    #[OA\Tag(name: 'Card')]
    #[OA\Post(
        description: "ajouter une nouvelle carte dans une list",
        parameters: [
            new OA\Parameter(name: 'idList', in: 'path', required: true, schema: new OA\Schema(type: 'int')),
            new OA\Parameter(name: CardsService::FRONT_TO_SEND, in: 'query', required: true, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: CardsService::BACK_TO_SEND, in: 'query', required: true, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: CardsService::FRONT_PERSO_TO_SEND, in: 'query', required: false, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: CardsService::BACK_PERSO_TO_SEND, in: 'query', required: false, schema: new OA\Schema(type: 'string')),
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
                description: "Post incomplet",
                content: new OA\MediaType(
                    mediaType: "application/json",
                    example: ["error" => PostServiceError::ERROR_EMPTY_POST],
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
    public function addCardWithList(Request $request, ManagerRegistry $doctrine, ValidatorInterface $validator, int $idList): JsonResponse
    {
        $error = $this->postError->postError($request, $this->serviceRequest->getArrayObligation());
        if (count($error) !== 0) {
            return new JsonResponse($error, 400);
        }

        $user = $this->getUser();
        $reponse = $this->serviceRequest->addCardAndList($request, $doctrine, $validator, $user, $idList);
        return new JsonResponse($reponse);
    }

    #[Route('api/card/{id}', name: 'delete_card', methods: ['DELETE'], requirements: ["id" => "^[0-9]+$"])]
    #[IsGranted("ROLE_USER")]
    #[OA\Tag(name: 'Card')]
    #[OA\Delete(
        description: "supprimer une carte",
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'int')),
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
                description: "utilisateur ne possede pas la carte",
                content: new OA\MediaType(
                    mediaType: "application/json",
                    example: ["error" => CardsService::ERROR_ID_USER],
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
    public function deleteCard(ManagerRegistry $doctrine, int $id): JsonResponse
    {
        $user = $this->getUser();
        $reponse = $this->serviceRequest->removeCard($doctrine, $user, $id);
        if (isset($response['error'])) {
            return new JsonResponse($response, 400);
        }
        return new JsonResponse($reponse);
    }

    #[Route('api/card/{id}', name: 'update_card', methods: ['PUT'], requirements: ["id" => "^[0-9]+$"])]
    #[IsGranted("ROLE_USER")]
    #[OA\Tag(name: 'Card')]
    #[OA\Put(
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'int')),
            new OA\RequestBody(required: true, content: new OA\JsonContent(example: [
                "id" => 1,
                CardsService::FRONT_TO_SEND => 'recto content',
                CardsService::BACK_TO_SEND => 'verso content',
                CardsService::FRONT_PERSO_TO_SEND => 'recto perso content',
                CardsService::BACK_PERSO_TO_SEND => 'verso perso content',
                CardsService::KEYS_LIST_ID => "array d'id de liste qui contient cette carte",
                CardsService::KEYS_LIST_NAME => "array de nom de list qui contient cette carte"
            ]), description: "envoyer un json dans le body"),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Retourne un json avec toutes les informations des cartes que l'utilisateur posséde ou error",
                content: new OA\MediaType(
                    mediaType: "application/json",
                    example: [
                        "statut" => "ok"
                    ],
                ),
            ),
            new OA\Response(
                response: 400,
                description: "utilisateur ne possede pas la carte",
                content: new OA\MediaType(
                    mediaType: "application/json",
                    example: ["error" => CardsService::ERROR_ID_USER],
                )
            ),
            new OA\Response(
                response: 406,
                description: "Json mal agencé",
                content: new OA\MediaType(
                    mediaType: "application/json",
                    example: ["error" => CardsService::ERROR_JSON],
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
    public function updateCard(Request $request, ManagerRegistry $doctrine, ValidatorInterface $validator, int $id): JsonResponse
    {
        $user = $this->getUser();
        $response = $this->serviceRequest->updateCard($request, $doctrine, $user, $validator, $id);
        if (isset($response['error'])) {
            return new JsonResponse($response, 400);
        }
        if (isset($response['error']) && $response['error'] === CardsService::ERROR_JSON) {
            return new JsonResponse($response, 406);
        }
        return new JsonResponse($response);
    }

    #[Route('api/card/{id}', name: 'read_card', methods: ['GET'], requirements: ["id" => "^[0-9]+$"])]
    #[IsGranted("ROLE_USER")]
    #[OA\Tag(name: 'Card')]
    #[OA\Get(
        description: "lire une carte",
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'int')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Retourne un json avec toutes les informations de la cartes que l'utilisateur posséde",
                content: new OA\MediaType(
                    mediaType: "application/json",
                    example: [
                        "id" => 1,
                        CardsService::FRONT_TO_SEND => 'recto content',
                        CardsService::BACK_TO_SEND => 'verso content',
                        CardsService::FRONT_PERSO_TO_SEND => 'recto perso content',
                        CardsService::BACK_PERSO_TO_SEND => 'verso perso content',
                        CardsService::KEYS_LIST_ID => "array d'id de liste qui contient cette carte",
                        CardsService::KEYS_LIST_NAME => "array de nom de list qui contient cette carte"
                    ],
                ),
            ),
            new OA\Response(
                response: 400,
                description: "utilisateur ne possede pas la carte",
                content: new OA\MediaType(
                    mediaType: "application/json",
                    example: ["error" => CardsService::ERROR_ID_USER],
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
    public function readCard(ManagerRegistry $doctrine, int $id): JsonResponse
    {
        $response = $this->serviceRequest->getCard($doctrine, $id);
        if (isset($response['error'])) {
            return new JsonResponse($response, 400);
        }
        return new JsonResponse($response);
    }

    #[Route('api/card', name: 'read_card_all', methods: ['GET'])]
    #[IsGranted("ROLE_USER")]
    #[OA\Tag(name: 'Card')]
    #[OA\Get(
        description: "lire toutes les cartes d'un utilisateur",
        parameters: [],
        responses: [
            new OA\Response(
                response: 200,
                description: "Retourne un json avec toutes les informations des cartes que l'utilisateur posséde",
                content: new OA\MediaType(
                    mediaType: "application/json",
                    example: [
                        [
                            "id" => 1,
                            CardsService::FRONT_TO_SEND => 'recto content',
                            CardsService::BACK_TO_SEND => 'verso content',
                            CardsService::FRONT_PERSO_TO_SEND => 'recto perso content',
                            CardsService::BACK_PERSO_TO_SEND => 'verso perso content',
                            CardsService::KEYS_LIST_ID => "array d'id de liste qui contient cette carte",
                            CardsService::KEYS_LIST_NAME => "array de nom de list qui contient cette carte"
                        ],
                        [
                            "id" => 2,
                            CardsService::FRONT_TO_SEND => 'recto content',
                            CardsService::BACK_TO_SEND => 'verso content',
                            CardsService::FRONT_PERSO_TO_SEND => 'recto perso content',
                            CardsService::BACK_PERSO_TO_SEND => 'verso perso content',
                            CardsService::KEYS_LIST_ID => "array d'id de liste qui contient cette carte",
                            CardsService::KEYS_LIST_NAME => "array de nom de list qui contient cette carte"
                        ],
                    ],
                ),
            ),
            new OA\Response(
                response: 400,
                description: "utilisateur n'a pas de carte",
                content: new OA\MediaType(
                    mediaType: "application/json",
                    example: ["error" => CardsService::ERROR_USER],
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
    public function readAllCard(ManagerRegistry $doctrine): JsonResponse
    {

        $user = $this->getUser();
        $response = $this->serviceRequest->getAllCard($doctrine, $user);
        if (isset($response['error'])) {
            return new JsonResponse($response, 400);
        }
        return new JsonResponse($response);
    }
}

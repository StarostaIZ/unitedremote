<?php

namespace App\Controller;

use App\Entity\Event;
use App\Repository\EventRepository;
use App\Serializer\EventSerializer;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OAT;
use OpenApi\Attributes\Items;
use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\Schema;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route("/api/events")]
#[OAT\Tag("events")]
class EventController extends AbstractController
{


    public function __construct(
        private EntityManagerInterface $em,
        private ValidatorInterface     $validator,
        private EventRepository        $repository,
        private EventSerializer        $serializer
    )
    {
    }

    #[Route("/{id}", methods: ["GET"])]
    #[
        OAT\Response(
            response: 200,
            description: "OK",
            content: new Model(type: Event::class)
        ),
        OAT\Response(
            response: 404,
            description: "Resource not found",
        ),

    ]
    public function get(int $id): JsonResponse
    {
        $event = $this->repository->find($id);
        if (!$event) {
            return $this->json(null, Response::HTTP_NOT_FOUND);
        }

        return $this->json($event);
    }

    #[Route("", methods: ["GET"])]
    #[
        OAT\Response(
            response: 200,
            description: "OK",
            content: new JsonContent(
                type: "array",
                items: new Items(new Model(type: Event::class)))
        ),
        OAT\Response(
            response: 404,
            description: "Resource not found",
        ),

    ]
    public function getALl(): JsonResponse
    {
        return $this->json($this->repository->findAll());
    }

    #[Route("", methods: ["POST"])]
    #[
        OAT\Response(
            response: 201,
            description: "OK",
        ),
        OAT\RequestBody(
            content: new OAT\JsonContent(
                ref: new Model(type: Event::class)
            )
        )
    ]
    public function create(Request $request): JsonResponse
    {
        $event = $this->serializer->getEventFromJson($request->getContent());

        $errors = $this->validator->validate($event);

        if ($errors->count() != 0) {
            return $this->json(null, Response::HTTP_BAD_REQUEST);
        }

        $this->repository->add($event);
        return $this->json("Event created!", Response::HTTP_CREATED);
    }

    #[Route("/{id}", methods: ["DELETE"])]
    #[
        OAT\Response(
            response: 204,
            description: "No content",
        ),
        OAT\Response(
            response: 404,
            description: "Resource not found",
        ),

    ]
    public function remove(int $id): JsonResponse
    {
        $event = $this->repository->find($id);
        if (!$event) {
            return $this->json(null, Response::HTTP_NOT_FOUND);
        }
        $this->repository->remove($event, true);

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }

    #[Route("/{id}", methods: ["PATCH"])]
    #[Route("", methods: ["POST"])]
    #[
        OAT\Response(
            response: 200,
            description: "OK",
        ),
        OAT\RequestBody(
            content: new OAT\JsonContent(
                ref: new Model(type: Event::class)
            )
        )
    ]
    public function edit(int $id, Request $request): JsonResponse
    {
        $event = $this->repository->find($id);
        if (!$event) {
            return $this->json(null, Response::HTTP_NOT_FOUND);
        }
        $this->serializer->editEventFromJson($request->getContent(), $event);
        $this->em->flush();

        return $this->json($event);
    }
}
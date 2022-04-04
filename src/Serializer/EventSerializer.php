<?php

namespace App\Serializer;

use App\Entity\Event;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class EventSerializer
{

    public function __construct(
        private SerializerInterface $serializer
    )
    {
    }

    public function getEventFromJson(string $json): Event
    {
        return $this->serializer->deserialize(
            $json,
            Event::class,
            "json");
    }

    public function editEventFromJson(string $json, Event $event): Event
    {
        return $this->serializer->deserialize($json, Event::class, 'json', [
            AbstractNormalizer::OBJECT_TO_POPULATE => $event,
            AbstractNormalizer::IGNORED_ATTRIBUTES => ['id']
        ]);
    }
}
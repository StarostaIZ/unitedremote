<?php

namespace App\DataFixtures;

use App\Entity\Event;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

class EventFixtures extends Fixture
{
    private Generator $faker;

    public function __construct()
    {
       $this->faker = Factory::create();
    }

    public function load(ObjectManager $manager)
    {
        for ($i=0; $i<10; $i++) {
            $event = new Event();
            $event->setName($this->faker->colorName);
            $event->setDate($this->faker->dateTimeThisYear);
            $manager->persist($event);
        }
        $manager->flush();
    }
}
<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\Enum\TaskStatus;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class TaskFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        $statuses = [
            TaskStatus::TODO,
            TaskStatus::IN_PROGRESS,
            TaskStatus::DONE
        ];

        for ($i = 0; $i < 50; $i++) {
            $task = new Task();
            $task->setTitle($faker->sentence(rand(3, 6)))
                ->setDescription($faker->paragraphs(rand(1, 3), true))
                ->setStatus($statuses[array_rand($statuses)]);

            if (rand(0, 1) && $task->getStatus() !== TaskStatus::TODO) {
                $task->setUpdatedAt();
            }

            $manager->persist($task);
        }

        $manager->flush();
    }
}
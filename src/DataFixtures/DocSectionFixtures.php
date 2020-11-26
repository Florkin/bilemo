<?php

namespace App\DataFixtures;

use App\Entity\DocSection;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class DocSectionFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');
        $faker->addProvider(new \DavidBadura\FakerMarkdownGenerator\FakerProvider($faker));

        for ($i = 0; $i < 20; $i++) {
            $docSection = new DocSection();
            $docSection->setTitle($faker->sentences(1, true));
            $docSection->setContent($faker->markdown());

            $manager->persist($docSection);
        }

        $manager->flush();
    }
}

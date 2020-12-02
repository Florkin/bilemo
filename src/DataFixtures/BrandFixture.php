<?php

namespace App\DataFixtures;

use App\Entity\Brand;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class BrandFixture extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');
        for ($i = 0; $i < 6; $i++) {
            $brand = new Brand();
            $brand
                ->setName($faker->words(1, true) . "brand")
                ->setDescription($faker->sentences(6, true));

            $manager->persist($brand);

            $this->addReference("brandref_" . $i, $brand);
        }

        $manager->flush();
    }
}

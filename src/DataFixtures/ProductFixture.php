<?php

namespace App\DataFixtures;

use App\Entity\Product;
use App\Repository\BrandRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ProductFixture extends Fixture implements DependentFixtureInterface
{
    /**
     * @var BrandRepository
     */
    private $brandRepository;

    /**
     * ProductFixture constructor.
     * @param BrandRepository $brandRepository
     */
    public function __construct(BrandRepository $brandRepository)
    {
        $this->brandRepository = $brandRepository;
    }

    public function load(ObjectManager $manager)
    {
        $brands = $this->brandRepository->findAll();
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < 60; $i++) {
            $product = new Product();
            $product
                ->setName($faker->words(2, true))
                ->setDescription($faker->sentences(6, true))
                ->setPrice($faker->randomFloat(2, 99, 2000))
                ->setBrand($faker->randomElement($brands));

            $manager->persist($product);

        }

        $manager->flush();;
    }

    public function getDependencies()
    {
        return array(
            BrandFixture::class,
        );
    }
}


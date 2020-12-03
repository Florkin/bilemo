<?php

namespace App\DataFixtures;

use App\Entity\ClientUser;
use App\Repository\ClientRepository;
use App\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ClientUserFixture extends Fixture implements DependentFixtureInterface
{
    /**
     * @var ClientRepository
     */
    private ClientRepository $clientRepository;

    /**
     * UserFixture constructor.
     * @param ClientRepository $clientRepository
     */
    public function __construct(ClientRepository $clientRepository)
    {
        $this->clientRepository = $clientRepository;
    }

    public function load(ObjectManager $manager)
    {
        $clients = $this->clientRepository->findAll();
        $faker = Factory::create('fr_FR');
        for ($i = 0; $i < 100; $i++) {
            $user = new ClientUser();
            $user
                ->setFirstname($faker->firstName())
                ->setLastname($faker->lastName)
                ->setEmail($faker->email)
                ->setClient($faker->randomElement($clients));

            $manager->persist($user);

        }

        $manager->flush();;
    }

    public function getDependencies()
    {
        return array(
            ClientFixture::class,
        );
    }
}

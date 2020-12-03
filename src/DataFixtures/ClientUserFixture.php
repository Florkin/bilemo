<?php

namespace App\DataFixtures;

use App\Entity\ClientUser;
use App\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ClientUserFixture extends Fixture implements DependentFixtureInterface
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * UserFixture constructor.
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function load(ObjectManager $manager)
    {
        $users = $this->userRepository->findAll();
        $faker = Factory::create('fr_FR');
        for ($i = 0; $i < 100; $i++) {
            $user = new ClientUser();
            $user
                ->setFirstname($faker->firstName())
                ->setLastname($faker->lastName)
                ->setEmail($faker->email)
                ->setUser($faker->randomElement($users));

            $manager->persist($user);

        }

        $manager->flush();;
    }

    public function getDependencies()
    {
        return array(
            UserFixture::class,
        );
    }
}

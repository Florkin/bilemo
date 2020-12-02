<?php

namespace App\DataFixtures;

use App\Entity\Client;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ClientFixture extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        // CREATE ADMIN
        $user = new Client;
        $user
            ->setEmail("tristan@bilemo.fr")
            ->setPassword($this->encoder->encodePassword($user, "demodemo"))
            ->setUsername("Tristan")
            ->setRoles(["ROLE_ADMIN"]);
        $manager->persist($user);

        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < 6; $i++) {
            $user = new Client;
            $user
                ->setEmail($faker->email)
                ->setPassword($this->encoder->encodePassword($user, "demodemo"))
                ->setUsername($faker->userName)
                ->setRoles(["ROLE_USER"]);
            $manager->persist($user);
        }

        $manager->flush();
    }
}


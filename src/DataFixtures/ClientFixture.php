<?php

namespace App\DataFixtures;

use App\Entity\Client;
use Doctrine\Bundle\FixturesBundle\Fixture;
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
        $faker = Factory::create('fr_FR');
        $user = new Client;
        $user
            ->setEmail("tristan@bilemo.fr")
            ->setPassword($this->encoder->encodePassword($user, "demodemo"))
            ->setUsername("Tristan")
            ->setRoles(["ROLE_ADMIN"]);
        $manager->persist($user);
        $manager->flush();
    }
}


<?php

namespace App\Controller\Api\Tests;

use App\Repository\ClientRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class UserControllerTest extends WebTestCase
{
    /**
     * Create a client with a default Authorization header.
     *
     * @param string $username
     * @param string $password
     *
     * @return \Symfony\Bundle\FrameworkBundle\Client;
     */
    protected function createAuthenticatedClient($username = 'client@demo.fr', $password = 'demodemo')
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/api/login_check',
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            json_encode(array(
                'username' => $username,
                'password' => $password,
            ))
        );

        $data = json_decode($client->getResponse()->getContent(), true);

        $client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $data['token']));

        return $client;
    }

    /**
     * GET /users
     */
    public function testUserIndex()
    {
        $client = $this->createAuthenticatedClient();
        $client->request('GET', '/api/users');
        $this->assertResponseIsSuccessful();
        $this->assertTrue($client->getResponse()->headers->contains(
            'Content-Type', 'application/json'
        ));
        $this->assertResponseHeaderSame('Content-Type', 'application/json');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    /**
     * GET /users/{id}
     */
    public function testUserShow()
    {
        $client = $this->createAuthenticatedClient();
        $container = $this->getContainer($client);
        $clientRepository = $container->get(ClientRepository::class);
        $userRepository = $container->get(UserRepository::class);
        $clientUser = $clientRepository->findOneBy(['email' => 'client@demo.fr']);
        $users = $userRepository->findBy(['client' => $clientUser->getId()]);
        $rand = array_rand($users, 1);
        $user = $users[$rand];

        $client->request('GET', '/api/users/'. (string)$user->getId());
        $this->assertResponseIsSuccessful();
        $this->assertTrue($client->getResponse()->headers->contains(
            'Content-Type', 'application/json'
        ));
        $this->assertResponseHeaderSame('Content-Type', 'application/json');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    /**
     * POST /users
     */
    public function testUserNew()
    {
        $client = $this->createAuthenticatedClient();
        $client->request( 'POST',
            '/api/users',
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            json_encode(array(
                'firstname' => 'testuserFirstname',
                'lastname' => 'testuserLastname',
                'email' => 'testuser@email.fr',
            )));
        $this->assertResponseIsSuccessful();
        $this->assertTrue($client->getResponse()->headers->contains(
            'Content-Type', 'application/json'
        ));
        $this->assertResponseHeaderSame('Content-Type', 'application/json');
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
    }

    /**
     * PATCH /users/{id}
     */
    public function testUserEdit()
    {
        $client = $this->createAuthenticatedClient();
        $userRepository = $this->getContainer($client)->get(UserRepository::class);
        $user = $userRepository->findOneBy(['email' => 'testuser@email.fr']);
        $client->request( 'PATCH',
            '/api/users/' . $user->getId(),
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            json_encode(array(
                'firstname' => 'testuserModifiedFirstname',
                'email' => 'testusermodified@email.fr',
            )));
        $this->assertResponseIsSuccessful();
        $this->assertTrue($client->getResponse()->headers->contains(
            'Content-Type', 'application/json'
        ));
        $this->assertResponseHeaderSame('Content-Type', 'application/json');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    /**
     * DELETE /users/{id}
     */
    public function testUserDelete()
    {
        $client = $this->createAuthenticatedClient();
        $userRepository = $this->getContainer($client)->get(UserRepository::class);
        $user = $userRepository->findOneBy(['email' => 'testusermodified@email.fr']);
        $client->request( 'DELETE',
            '/api/users/' . $user->getId()
        );
        $this->assertResponseIsSuccessful();
        $this->assertTrue($client->getResponse()->headers->contains(
            'Content-Type', 'application/json'
        ));
        $this->assertResponseHeaderSame('Content-Type', 'application/json');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    private function getContainer($client)
    {
        return $client->getContainer()->get('test.service_container');
    }
}

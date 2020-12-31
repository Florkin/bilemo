<?php

namespace App\Controller\Api\Tests;

use App\Repository\BrandRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class BrandControllerTest extends WebTestCase
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
     * GET /brands
     */
    public function testBrandIndex()
    {
        $client = $this->createAuthenticatedClient();
        $client->request('GET', '/api/brands');
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
    public function testBrandShow()
    {
        $client = $this->createAuthenticatedClient();
        $container = $this->getContainer($client);
        $brandRepository = $container->get(BrandRepository::class);
        $brands = $brandRepository->findAll();
        $rand = array_rand($brands, 1);
        $brand = $brands[$rand];

        $client->request('GET', '/api/brands/'. (string)$brand->getId());
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

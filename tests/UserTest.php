<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Repository\UserRepository;
use App\Entity\User;

class UserTest extends WebTestCase
{
    public function testUserCurrentNotLogged(): void
    {
        $client = static::createClient();
        $client->request('GET', '/user/current');
        $response = $client->getResponse();

        $status = $response->getStatusCode();
        $body = $response->getContent();
        $body = json_decode($body);

        $this->assertEquals(401, $status);
        $this->assertTrue(isset($body->error));
        $this->assertEquals($body->error, "No user found");

    }

    public function testUserCurrentApiKey(): void
    {
        $client = static::createClient();
        $apikey = 'TEST-API-KEY';
        $client->request('GET', '/user/current', [], [], [
            'HTTP_X-AUTH-TOKEN' => $apikey,
        ]);
        $response = $client->getResponse();

        $status = $response->getStatusCode();
        $body = $response->getContent();
        $body = json_decode($body);

        $this->assertEquals(200, $status);
        $this->assertFalse(isset($body->error));
        $this->assertEquals("unit.test@unit.test", $body->user->email);
        $this->assertEquals($apikey, $body->user->apikey);

    }

    public function testUserCurrentPassword(): void
    {
        $client = static::createClient();
        $apikey = 'TEST-API-KEY';

        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('unit.test@unit.test');
        $client->loginUser($testUser);

        $client->request('GET', '/user/current');
        $response = $client->getResponse();

        $status = $response->getStatusCode();
        $body = $response->getContent();
        $body = json_decode($body);

        $this->assertEquals(200, $status);
        $this->assertFalse(isset($body->error));
        $this->assertEquals("unit.test@unit.test", $body->user->email);
        $this->assertEquals($apikey, $body->user->apikey);

    }
}

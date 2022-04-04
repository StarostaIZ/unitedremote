<?php

namespace App\Tests\Integration;

use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AuthTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testGetResourceUnauthorized(): void
    {
        $this->client->request('GET', '/api/events/1');
        $this->assertResponseStatusCodeSame(401);
    }

    public function testGetResourceNoToken(): void
    {
        $this->client->request('GET', '/api/events/1', [], [], [
            'HTTP_X_API_TOKEN' => ''
        ]);
        $this->assertResponseStatusCodeSame(401);
    }

    public function testGetResourceInvalidToken(): void
    {
        $this->client->request('GET', '/api/events/1', [], [], [
            'HTTP_X_API_TOKEN' => 'INVALID_TOKEN'
        ]);
        $this->assertResponseStatusCodeSame(401);
    }
}

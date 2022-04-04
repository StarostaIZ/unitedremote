<?php

namespace App\Tests\Integration;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class EventControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    public function testGetEvent()
    {

        $this->client->request('GET', '/api/events/1');
        $this->assertResponseIsSuccessful();
        $response = json_decode($this->client->getResponse()->getContent());
        $this->assertEquals(1, $response->id);
        $this->assertNotNull($response->name);
        $this->assertNotNull($response->date);
    }

    public function testGetEventNotExist()
    {

        $this->client->request('GET', '/api/events/11');
        $this->assertResponseStatusCodeSame(404);
    }

    public function testGetEventList()
    {
        $this->client->request('GET', '/api/events');
        $this->assertResponseIsSuccessful();
        $response = json_decode($this->client->getResponse()->getContent());
        $this->assertCount(10, $response);
    }

    public function testPostEvent()
    {
        $this->client->request('POST', '/api/events', content: '{"name": "testEvent", "date": "2022-03-08T00:00:00+01:00"}');
        $this->assertResponseStatusCodeSame(201);
        $this->client->request('GET', '/api/events');
        $response = json_decode($this->client->getResponse()->getContent());
        $this->assertCount(11, $response);
    }

    public function testPostEventInvalidData()
    {
        $this->client->request('POST', '/api/events', content: '{"name": "testEvent"}');
        $this->assertResponseStatusCodeSame(400);
    }

    public function testEditEvent()
    {
        $this->client->request('PATCH', '/api/events/1', content: '{"name": "testEvent"}');
        $this->assertResponseStatusCodeSame(200);
        $this->client->request('GET', '/api/events/1');
        $response = json_decode($this->client->getResponse()->getContent());
        $this->assertEquals("testEvent", $response->name);
    }

    public function testEditEventNotExits()
    {
        $this->client->request('PATCH', '/api/events/11', content: '{"name": "testEvent"}');
        $this->assertResponseStatusCodeSame(404);
    }


    public function testDeleteEvent()
    {
        $this->client->request('DELETE', '/api/events/1');
        $this->assertResponseStatusCodeSame(204);
        $this->client->request('GET', '/api/events/1');
        $this->assertResponseStatusCodeSame(404);
    }

    public function testDeleteEventNotExist()
    {
        $this->client->request('DELETE', '/api/events/11');
        $this->assertResponseStatusCodeSame(404);
    }

    protected function setUp(): void
    {
        $this->client = static::createClient(server: [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $_ENV['API_TOKEN']
        ]);
    }

}

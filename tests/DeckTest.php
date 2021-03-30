<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DeckTest extends WebTestCase
{

    public function setUp(): void
    {
        if (!isset($this->client)) {
            $this->client = static::createClient();
        }
    }

    // TESTS
    
    // Deck

    public function testDeckCreation(): void
    {
        $res = $this->createDeck();
        $response = $res['response'];
        $body = $res['body'];

        $this->assertTrue($response->isSuccessful(), "POST should be successful");
        $this->assertIsInt($body->deck->id, "Deck ID should be successful");
        $this->assertEquals($res['deck']['name'], $body->deck->name, "Deck name should be correct");
        $this->assertEquals($res['deck']['description'], $body->deck->description, "Deck description should be correct");
        $this->assertFalse($body->deck->published, "Deck should not be published");
        $this->assertNull($body->deck->version, "Deck version should be null");
    }

    public function testDeckUpdate(): void
    {
        $res = $this->createDeck();
        $id = $res['body']->deck->id;

        $patch = [
            'name' => 'patched name',
            'description' => 'patched description',
        ];

        $pubRes = $this->req('PATCH', '/api/decks/' . $id, $patch);
        $body = $pubRes->getResponse()->getContent();
        $body = json_decode($body);

        $this->assertTrue($pubRes->getResponse()->isSuccessful(), "PATCH should be successful");
        $this->assertEquals($res['body']->deck->id, $body->deck->id, "Deck ID should be identical");
        $this->assertEquals($patch['name'], $body->deck->name);
        $this->assertEquals($patch['description'], $body->deck->description);
    }

    public function testDeckPublication(): void
    {
        $res = $this->createDeck();
        $id = $res['body']->deck->id;

        $pubRes = $this->req('POST', '/api/decks/' . $id . '/published');
        $body = $pubRes->getResponse()->getContent();
        $body = json_decode($body);

        $this->assertTrue($pubRes->getResponse()->isSuccessful(), "POST should be successful");
        $this->assertEquals($res['body']->deck->id, $body->deck->parent->id, "Publication parent shoult be set properly");
        $this->assertEquals($res['body']->deck->name, $body->deck->name, "Name should be like its parent");
        $this->assertEquals($res['body']->deck->description, $body->deck->description, "Description should be like its parent");
        $this->assertTrue($body->deck->published, "Deck should be published");
        $this->assertEquals(1, $body->deck->version, "Deck version should be 1");
    }
    
    // Cards

    public function testCardCreation(): void
    {
        $res = $this->createDeck();
        $id = $res['body']->deck->id;

        $card = [
            'question' => 'some question',
            'answer' => 'some answer',
        ];

        $pubRes = $this->req('POST', '/api/decks/' . $id . '/cards', $card);
        $body = $pubRes->getResponse()->getContent();
        $body = json_decode($body);

        $this->assertTrue($pubRes->getResponse()->isSuccessful(), "Response should be successful");
        $this->assertEquals($res['body']->deck->id, $body->card->deck_id, "Card should have right id");
        $this->assertEquals($card['question'], $body->card->question, "Question should be correct");
        $this->assertEquals($card['answer'], $body->card->answer, "Answer should be correct");
    }

    // HELPERS

    public function req($method, $url, $body = null)
    {
        $this->client->request($method, $url, [], [], [
            'HTTP_X-AUTH-TOKEN' => 'TEST-API-KEY',
            'CONTENT_TYPE' => 'application/json'
        ], json_encode($body));
        return $this->client;
    }
    

    public function createDeck()
    {
        $deck = [
            'name' => 'new deck name',
            'description' => 'new deck description',
        ];
        $this->client = $this->req('POST', '/api/decks', $deck);
        $response = $this->client->getResponse();

        $body = $response->getContent();
        $body = json_decode($body);

        $res = [
            'deck' => $deck,
            'response' => $response,
            'body' => $body,
        ];
        return $res;
    }
    
}

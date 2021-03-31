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
        $deckid = $body->deck->id;

        $this->assertTrue($response->isSuccessful(), "POST should be successful");
        $this->assertIsInt($body->deck->id, "Deck ID should be successful");
        $this->assertEquals($res['deck']['name'], $body->deck->name, "Deck name should be correct");
        $this->assertEquals($res['deck']['description'], $body->deck->description, "Deck description should be correct");
        $this->assertFalse($body->deck->published, "Deck should not be published");
        $this->assertNull($body->deck->version, "Deck version should be null");

        // Check creation
        $resGet = $this->req('GET', '/api/decks/' . $deckid);
        $bodyGet = $resGet->getResponse()->getContent();
        $bodyGet = json_decode($bodyGet);
        $this->assertTrue($resGet->getResponse()->isSuccessful(), "GET should be successful");
        $this->assertEquals($body->deck->name, $bodyGet->deck->name, "Deck name should be correct");
        $this->assertEquals($body->deck->description, $bodyGet->deck->description, "Deck description should be correct");
    }

    public function testDeckUpdate(): void
    {
        $res = $this->createDeck();
        $deckid = $res['body']->deck->id;

        $patch = [
            'name' => 'patched name',
            'description' => 'patched description',
        ];

        $updateRes = $this->req('PATCH', '/api/decks/' . $deckid, $patch);
        $body = $updateRes->getResponse()->getContent();
        $body = json_decode($body);

        $this->assertTrue($updateRes->getResponse()->isSuccessful(), "PATCH should be successful");
        $this->assertEquals($res['body']->deck->id, $body->deck->id, "Deck ID should be identical");
        $this->assertEquals($patch['name'], $body->deck->name, "Deck name should be patched");
        $this->assertEquals($patch['description'], $body->deck->description, "Deck description should be patched");
        // Check update
        $resGet = $this->req('GET', '/api/decks/' . $deckid);
        $bodyGet = $resGet->getResponse()->getContent();
        $bodyGet = json_decode($bodyGet);
        $this->assertTrue($resGet->getResponse()->isSuccessful(), "GET should be successful");
        $this->assertEquals($patch['name'], $bodyGet->deck->name, "Deck name should be correct");
        $this->assertEquals($patch['description'], $bodyGet->deck->description, "Deck description should be correct");
    }

    public function testDeckPublication(): void
    {
        $res = $this->createDeck();
        $deck = $res['body']->deck;
        $deckid = $res['body']->deck->id;

        $pubRes = $this->req('POST', '/api/decks/' . $deckid . '/published');
        $body = $pubRes->getResponse()->getContent();
        $body = json_decode($body);
        $publishedid = $body->deck->id;

        $this->assertTrue($pubRes->getResponse()->isSuccessful(), "POST should be successful");
        $this->assertEquals($res['body']->deck->id, $body->deck->parent->id, "Publication parent shoult be set properly");
        $this->assertEquals($res['body']->deck->name, $body->deck->name, "Name should be like its parent");
        $this->assertEquals($res['body']->deck->description, $body->deck->description, "Description should be like its parent");
        $this->assertTrue($body->deck->published, "Deck should be published");
        $this->assertEquals(1, $body->deck->version, "Deck version should be 1");

        // Check publication
        $resGet = $this->req('GET', '/api/decks/' . $publishedid);
        $bodyGet = $resGet->getResponse()->getContent();
        $bodyGet = json_decode($bodyGet);
        $this->assertTrue($resGet->getResponse()->isSuccessful(), "GET should be successful");
        $this->assertEquals($deck->name, $bodyGet->deck->name,  "Deck name should be correct");
        $this->assertEquals($deck->description, $bodyGet->deck->description, "Deck description should be correct");
    }

    public function testDeckDelete()
    {
        // Create
        $res = $this->createDeck();
        $id = $res['body']->deck->id;
        $getRes = $this->req('GET', '/api/decks/' . $id);
        $this->assertTrue($getRes->getResponse()->isSuccessful());

        // Delete
        $deleteRes = $this->req('DELETE', '/api/decks/' . $id);
        $this->assertTrue($deleteRes->getResponse()->isSuccessful());
        $body = $deleteRes->getResponse()->getContent();
        $body = json_decode($body);

        // Check deletion
        $get2Res = $this->req('GET', '/api/decks/' . $id);
        $this->assertFalse($get2Res->getResponse()->isSuccessful());
        $this->assertEquals(404, $get2Res->getResponse()->getStatusCode(), "Deck should have been removed");
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

        // Check response
        $createRes = $this->req('POST', '/api/decks/' . $id . '/cards', $card);
        $bodyCreate = $createRes->getResponse()->getContent();
        $bodyCreate = json_decode($bodyCreate);
        $cardid = $bodyCreate->card->id;
        $this->assertTrue($createRes->getResponse()->isSuccessful(), "Response should be successful");
        $this->assertEquals($res['body']->deck->id, $bodyCreate->card->deck_id, "Card should have right id");
        $this->assertEquals($card['question'], $bodyCreate->card->question, "Question should be correct");
        $this->assertEquals($card['answer'], $bodyCreate->card->answer, "Answer should be correct");

        // Check creation
        $getRes = $this->req('GET', '/api/cards/' . $cardid);
        $bodyGet = $getRes->getResponse()->getContent();
        $bodyGet = json_decode($bodyGet);
        $this->assertTrue($getRes->getResponse()->isSuccessful(), "Response should be successful");
        $this->assertEquals($res['body']->deck->id, $bodyGet->card->deck_id, "Card should have right id");
        $this->assertEquals($card['question'], $bodyGet->card->question, "Question should be correct");
        $this->assertEquals($card['answer'], $bodyGet->card->answer, "Answer should be correct");
    }

    public function testCardUpdate()
    {
        $res = $this->createDeck();
        $id = $res['body']->deck->id;

        // Create card
        $card = [
            'question' => 'some question',
            'answer' => 'some answer',
        ];
        $createRes = $this->req('POST', '/api/decks/' . $id . '/cards', $card);
        $body = $createRes->getResponse()->getContent();
        $body = json_decode($body);
        $cardid = $body->card->id;

        // Update card
        $patch = [
            'question' => 'patched question',
            'answer' => 'patched answer',
        ];
        $updateRes = $this->req('PATCH', '/api/cards/' . $cardid, $patch);
        $bodyUpdated = $updateRes->getResponse()->getContent();
        $bodyUpdated = json_decode($bodyUpdated);
        $this->assertTrue($updateRes->getResponse()->isSuccessful(), "Response should be successful");
        $this->assertEquals($patch['question'], $bodyUpdated->card->question, "Card question should be patched");
        $this->assertEquals($patch['answer'], $bodyUpdated->card->answer, "Card answer should be patched");

        // Check update
        $resGet = $this->req('GET', '/api/cards/' . $cardid);
        $bodyGet = $resGet->getResponse()->getContent();
        $bodyGet = json_decode($bodyGet);
        $this->assertTrue($resGet->getResponse()->isSuccessful(), "GET should be successful");
        $this->assertEquals($patch['question'], $bodyGet->card->question,  "Deck name should be correct");
        $this->assertEquals($patch['answer'], $bodyGet->card->answer, "Deck description should be correct");
    }

    public function testCardDelete()
    {
        $res = $this->createDeck();
        $id = $res['body']->deck->id;

        // Create card
        $card = [
            'question' => 'some question',
            'answer' => 'some answer',
        ];
        $createRes = $this->req('POST', '/api/decks/' . $id . '/cards', $card);
        $body = $createRes->getResponse()->getContent();
        $body = json_decode($body);
        $cardid = $body->card->id;
        $this->assertTrue($createRes->getResponse()->isSuccessful(), "POST should be successful");

        // Delete card
        $deleteRes = $this->req('DELETE', '/api/cards/' . $cardid, "DELETE should be successful");
        $this->assertTrue($deleteRes->getResponse()->isSuccessful());

        // Check deletion
        $get2Res = $this->req('GET', '/api/cards/' . $cardid, "GET should NOT be successful");
        $this->assertFalse($get2Res->getResponse()->isSuccessful());
        $this->assertEquals(404, $get2Res->getResponse()->getStatusCode(), "Card should have been removed");
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

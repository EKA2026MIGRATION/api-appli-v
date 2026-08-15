<?php

namespace App\Tests\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\Person;
use App\Tests\TestTrait;

class PersonControllerTest extends WebTestCase
{
    use TestTrait;

    /**
     * Tests creation Person
     */
    public function testCreate()
    {
        $this->clientAuthenticated->request(
            'POST',
            '/person/create',
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"firstname": "Firstname", "lastname": "Lastname", "key": "cf23ba48c06d7e6d4b41a205bfb3cac3bb7b1e38", "identifier": "6e2bbc8541e18b7475952c6d7e8d9113", "photo": "/url/photo", "relations": [{"related": "2", "relation": "Relation"}]}'
        );
        $response = $this->clientAuthenticated->getResponse();
        $content = $this->assertJsonResponse($response, 200);
        $this->assertArrayHasKey('personId', $content['person']);

        self::$objectId = $content['person']['personId'];
    }

    /**
     * Tests display Person
     */
    public function testDisplay()
    {
        $this->clientAuthenticated->request('GET', '/person/display/' . self::$objectId);
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);
    }

    /**
     * Tests modify Person
     */
    public function testModify()
    {
        //Tests with full data array
        $this->clientAuthenticated->request(
            'PUT',
            '/person/modify/' . self::$objectId,
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"firstname": "Firstname modifié", "lastname": "Lastname", "photo": "/url/photo", "relations": [{"related": "2", "relation": "Relation"}]}'
        );
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);

        //Tests with partial data array
        $this->clientAuthenticated->request(
            'PUT',
            '/person/modify/' . self::$objectId,
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"firstname": "Firstname modifié 2"}'
        );
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);
    }

    /**
     * Tests list of Person
     */
    public function testList()
    {
        //Tests list
        $this->clientAuthenticated->request('GET', '/person/list');
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);
        $content = $this->assertJsonResponse($response, 200);
        $this->assertInternalType('array', $content);
        $first = $content[0];
        $this->assertArrayHasKey('personId', $first);
    }

    /**
     * Tests search of Person
     */
    public function testSearch()
    {
        $this->clientAuthenticated->request('GET', '/person/search/name');
        $response = $this->clientAuthenticated->getResponse();
        $content = $this->assertJsonResponse($response, 200);
        $this->assertInternalType('array', $content);
        $first = $content[0];
        $this->assertArrayHasKey('personId', $first);
    }

    /**
     * Tests delete Person AND physically deletes it
     */
    public function testDelete()
    {
        $this->clientAuthenticated->request('DELETE', '/person/delete/' . self::$objectId);
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);

        //Deletes physically the entity created by test
        $this->deleteEntity('Person', 'personId', self::$objectId);
    }
}

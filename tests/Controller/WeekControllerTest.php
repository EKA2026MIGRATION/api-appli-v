<?php

namespace App\Tests\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\Week;
use App\Tests\TestTrait;

class WeekControllerTest extends WebTestCase
{
    use TestTrait;

    /**
     * Tests creation Week
     */
    public function testCreate()
    {
        $this->clientAuthenticated->request(
            'POST',
            '/week/create',
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"season": "1", "kind": "stage", "name": "Nom semaine", "dateStart": "1999-01-20"}'
        );
        $response = $this->clientAuthenticated->getResponse();
        $content = $this->assertJsonResponse($response, 200);
        $this->assertArrayHasKey('weekId', $content['week']);

        self::$objectId = $content['week']['weekId'];
    }

    /**
     * Tests display Week
     */
    public function testDisplay()
    {
        $this->clientAuthenticated->request('GET', '/week/display/' . self::$objectId);
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);
    }

    /**
     * Tests modify Week
     */
    public function testModify()
    {
        //Tests with full data array
        $this->clientAuthenticated->request(
            'PUT',
            '/week/modify/' . self::$objectId,
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"season": "1", "kind": "ecole", "name": "Nom semaine modifié", "dateStart": "1999-01-20"}'
        );
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);

        //Tests with partial data array
        $this->clientAuthenticated->request(
            'PUT',
            '/week/modify/' . self::$objectId,
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"kind": "stage"}'
        );
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);
    }

    /**
     * Tests list of Week
     */
    public function testList()
    {
        //Tests list
        $this->clientAuthenticated->request('GET', '/week/list');
        $response = $this->clientAuthenticated->getResponse();
        $content = $this->assertJsonResponse($response, 200);
        $this->assertInternalType('array', $content);
        $first = $content[0];
        $this->assertArrayHasKey('weekId', $first);
    }

    /**
     * Tests search of Child
     */
    public function testSearch()
    {
        $this->clientAuthenticated->request('GET', '/week/search/semain');
        $response = $this->clientAuthenticated->getResponse();
        $content = $this->assertJsonResponse($response, 200);
        $this->assertInternalType('array', $content);
        $first = $content[0];
        $this->assertArrayHasKey('weekId', $first);
    }

    /**
     * Tests delete Week AND physically deletes it
     */
    public function testDelete()
    {
        $this->clientAuthenticated->request('DELETE', '/week/delete/' . self::$objectId);
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);

        //Deletes physically the entity created by test
        $this->deleteEntity('Week', 'weekId', self::$objectId);
    }
}

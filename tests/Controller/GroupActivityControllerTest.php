<?php

namespace App\Tests\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\GroupActivity;
use App\Tests\TestTrait;

class GroupActivityControllerTest extends WebTestCase
{
    use TestTrait;

    /**
     * Tests creation GroupActivity
     */
    public function testCreate()
    {
        $this->clientAuthenticated->request(
            'POST',
            '/group-activity/create',
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"date": "2018-11-20", "name": "Name", "locked": "true", "start": "08:00:00", "end": "09:00:00", "lunch": true, "comment": "Comment", "location": 1, "area": "Terrain 1", "sport": 1, "links": [{"pickupActivityId": 1}], "staff": [{"staffId": 1}]}'
        );
        $response = $this->clientAuthenticated->getResponse();
        $content = $this->assertJsonResponse($response, 200);
        $this->assertArrayHasKey('groupActivityId', $content['groupActivity']);

        self::$objectId = $content['groupActivity']['groupActivityId'];

        //Tests multiple creation
        $this->clientAuthenticated->request(
            'POST',
            '/group-activity/create-multiple',
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '[{"date": "2018-11-20", "name": "Name", "locked": "true", "start": "08:00:00", "end": "09:00:00", "lunch": true, "comment": "Comment", "location": 1, "area": "Terrain 1", "sport": 1, "links": [{"pickupActivityId": 1}], "staff": [{"staffId": 1}]}, {"date": "2018-11-20", "name": "Name", "locked": "true", "start": "08:00:00", "end": "09:00:00", "lunch": true, "comment": "Comment", "location": 1, "area": "Terrain 1", "sport": 1, "links": [{"pickupActivityId": 1}], "staff": [{"staffId": 1}]}]'
        );

        $response = $this->clientAuthenticated->getResponse();
        $content = $this->assertJsonResponse($response, 200);
    }

    /**
     * Tests display GroupActivity
     */
    public function testDisplay()
    {
        //Tests by id
        $this->clientAuthenticated->request('GET', '/group-activity/display/' . self::$objectId);
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);

        //Tests by date and staffId
        $this->clientAuthenticated->request('GET', '/group-activity/display/2018-11-20/1');
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);
    }

    /**
     * Tests modify GroupActivity
     */
    public function testModify()
    {
        //Tests with full data array
        $this->clientAuthenticated->request(
            'PUT',
            '/group-activity/modify/' . self::$objectId,
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"date": "2018-11-20", "name": "Name modifié", "locked": "true", "start": "09:00:00", "end": "10:00:00", "lunch": true, "comment": "Comment modifié", "location": 1, "sport": 1}'
        );
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);

        //Tests with partial data array
        $this->clientAuthenticated->request(
            'PUT',
            '/group-activity/modify/' . self::$objectId,
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"name": "Name modifié 2"}'
        );
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);
    }

    /**
     * Tests list of GroupActivity
     */
    public function testList()
    {
        //Tests with date
        $this->clientAuthenticated->request('GET', '/group-activity/list/2018-11-20');
        $response = $this->clientAuthenticated->getResponse();
        $content = $this->assertJsonResponse($response, 200);
        $this->assertInternalType('array', $content);
        $first = $content[0];
        $this->assertArrayHasKey('groupActivityId', $first);
    }

    /**
     * Tests delete GroupActivity AND physically deletes it
     */
    public function testDelete()
    {
        $this->clientAuthenticated->request('DELETE', '/group-activity/delete/' . self::$objectId);
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);

        //Deletes physically the entity created by test
        $this->deleteEntity('GroupActivity', 'groupActivityId', self::$objectId);
    }
}

<?php

namespace App\Tests\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\PickupActivity;
use App\Tests\TestTrait;

class PickupActivityControllerTest extends WebTestCase
{
    use TestTrait;

    /**
     * Tests creation PickupActivity
     */
    public function testCreate()
    {
        $this->clientAuthenticated->request(
            'POST',
            '/pickup-activity/create',
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"registration": 1, "date": "2018-11-20", "start": "08:00:00", "end": "09:00:00", "status": "null", "statusChange": "2019-01-29 09:00:00", "validated": "validated", "child": "1", "sport": "1", "links": [{"groupActivityId": 1}]}'
        );

        $response = $this->clientAuthenticated->getResponse();
        $content = $this->assertJsonResponse($response, 200);
        $this->assertArrayHasKey('pickupActivityId', $content['pickupActivity']);

        self::$objectId = $content['pickupActivity']['pickupActivityId'];

        //Tests multiple creation
        $this->clientAuthenticated->request(
            'POST',
            '/pickup-activity/create-multiple',
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '[{"registration": 1, "date": "2018-11-20", "start": "08:00:00", "end": "09:00:00", "status": "null", "statusChange": "2019-01-29 09:00:00", "validated": "validated", "child": "1", "sport": "1"}, {"registration": 1, "date": "2018-11-20", "start": "08:00:00", "end": "09:00:00", "status": "supported", "statusChange": "2019-01-29 09:00:00", "validated": "validated", "child": "1", "sport": "1"}]'
        );

        $response = $this->clientAuthenticated->getResponse();
        $content = $this->assertJsonResponse($response, 200);
    }

    /**
     * Tests display PickupActivity
     */
    public function testDisplay()
    {
        $this->clientAuthenticated->request('GET', '/pickup-activity/display/' . self::$objectId);
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);
    }

    /**
     * Tests modify PickupActivity
     */
    public function testModify()
    {
        //Tests with full data array
        $this->clientAuthenticated->request(
            'PUT',
            '/pickup-activity/modify/' . self::$objectId,
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"registration": 1, "date": "2018-11-20", "start": "08:00:00", "end": "09:00:00", "status": "null", "statusChange": "2019-01-29 10:00:00", "validated": "validated", "child": "1", "sport": "1"}'
        );

        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);

        //Tests with partial data array
        $this->clientAuthenticated->request(
            'PUT',
            '/pickup-activity/modify/' . self::$objectId,
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"status": "absent"}'
        );

        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);
    }

    /**
     * Tests list of PickupAcitvity
     */
    public function testList()
    {
        //Tests with date
        $this->clientAuthenticated->request('GET', '/pickup-activity/list/2018-11-20');
        $response = $this->clientAuthenticated->getResponse();
        $content = $this->assertJsonResponse($response, 200);
        $this->assertInternalType('array', $content);
        $first = $content[0];
        $this->assertArrayHasKey('pickupActivityId', $first);

        //Tests with date and status
        $this->clientAuthenticated->request('GET', '/pickup-activity/list/2018-11-20/absent');
        $response = $this->clientAuthenticated->getResponse();
        $content = $this->assertJsonResponse($response, 200);
        $this->assertInternalType('array', $content);
        $first = $content[0];
        $this->assertArrayHasKey('pickupActivityId', $first);

        //Tests with date and status
        $this->clientAuthenticated->request('GET', '/pickup-activity/list/2018-11-20/supported');
        $response = $this->clientAuthenticated->getResponse();
        $content = $this->assertJsonResponse($response, 200);
        $this->assertInternalType('array', $content);
        $first = $content[0];
        $this->assertArrayHasKey('pickupActivityId', $first);

        //Tests with child and date
        $this->clientAuthenticated->request('GET', '/pickup-activity/list/1/2018-11-20');
        $response = $this->clientAuthenticated->getResponse();
        $content = $this->assertJsonResponse($response, 200);
        $this->assertInternalType('array', $content);
        $first = $content[0];
        $this->assertArrayHasKey('pickupActivityId', $first);
    }

    /**
     * Tests affect
     */
    public function testAffect()
    {
        $this->clientAuthenticated->request('PUT', '/pickup-activity/affect/2018-11-20');
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);
        $this->clientAuthenticated->request('PUT', '/pickup-activity/affect/2018-11-20/true');
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);
    }

    /**
     * Tests unaffect
     */
    public function testUnaffect()
    {
        $this->clientAuthenticated->request('PUT', '/pickup-activity/unaffect/2018-11-20');
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);
    }

    /**
     * Tests delete PickupActivity AND physically deletes it
     */
    public function testDelete()
    {
        //Tests by id
        $this->clientAuthenticated->request('DELETE', '/pickup-activity/delete/' . self::$objectId);
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);

        //Tests by registrationId
        $this->clientAuthenticated->request('DELETE', '/pickup-activity/delete-registration/1');
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);

        //Deletes physically the entity created by test
        $this->deleteEntity('PickupActivity', 'pickupActivityId', self::$objectId);
        $this->deleteEntity('PickupActivity', 'pickupActivityId', self::$objectId + 1);
        $this->deleteEntity('PickupActivity', 'pickupActivityId', self::$objectId + 2);
    }
}

<?php

namespace App\Tests\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\Pickup;
use App\Tests\TestTrait;

class PickupControllerTest extends WebTestCase
{
    use TestTrait;

    /**
     * Tests creation Pickup
     */
    public function testCreate()
    {
        $this->clientAuthenticated->request(
            'POST',
            '/pickup/create',
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"registration": 1, "kind": "dropin", "start": "2018-11-20 08:00:00", "phone": "0123456789", "postal": "75016", "address": "Address", "sortOrder": 1, "status": "null", "statusChange": "2018-11-20 08:00:01", "places": 1, "comment": "Comment", "validated": "validated", "child": "1", "ride": "1"}'
        );

        $response = $this->clientAuthenticated->getResponse();
        $content = $this->assertJsonResponse($response, 200);
        $this->assertArrayHasKey('pickupId', $content['pickup']);

        self::$objectId = $content['pickup']['pickupId'];

        //Tests multiple creation
        $this->clientAuthenticated->request(
            'POST',
            '/pickup/create-multiple',
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '[{"registration": 1, "kind": "dropin", "start": "2018-11-20 08:00:00", "phone": "0123456789", "postal": "75016", "address": "Address", "sortOrder": 1, "status": "absent", "statusChange": "2018-11-20 08:00:01", "places": 1, "comment": "Comment", "validated": "validated", "child": "1", "ride": "1"}, {"registration": 1, "kind": "dropin", "start": "2018-11-20 08:00:00", "phone": "0123456789", "postal": "75016", "address": "Address", "sortOrder": 1, "status": "supported", "statusChange": "2018-11-20 08:00:01", "places": 1, "comment": "Comment", "validated": "validated", "child": "1", "ride": "1"}, {"registration": 1, "kind": "dropin", "start": "2018-11-20 08:00:00", "phone": "0123456789", "postal": "75016", "address": "Address", "sortOrder": 1, "status": "supported", "statusChange": "2018-11-20 08:00:01", "places": 1, "comment": "Comment", "validated": "validated", "child": "1"}, {"registration": 1, "kind": "dropoff", "start": "2018-11-20 08:00:00", "phone": "0123456789", "postal": "75016", "address": "Address", "sortOrder": 1, "status": "supported", "statusChange": "2018-11-20 08:00:01", "places": 1, "comment": "Comment", "validated": "validated", "child": "1"}]'
        );

        $response = $this->clientAuthenticated->getResponse();
        $content = $this->assertJsonResponse($response, 200);
    }

    /**
     * Tests display Pickup
     */
    public function testDisplay()
    {
        $this->clientAuthenticated->request('GET', '/pickup/display/' . self::$objectId);
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);
    }

    /**
     * Tests modify Pickup
     */
    public function testModify()
    {
        //Tests with full data array
        $this->clientAuthenticated->request(
            'PUT',
            '/pickup/modify/' . self::$objectId,
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"registration": 1, "kind": "dropin", "start": "2018-11-20 08:00:00", "phone": "0123456789", "postal": "75016", "address": "Address modifiée", "sortOrder": 1, "status": "null", "statusChange": "2018-11-20 08:00:01", "places": 1, "comment": "Comment", "validated": "validated", "child": "1", "ride": "1"}'
        );

        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);

        //Tests with partial data array
        $this->clientAuthenticated->request(
            'PUT',
            '/pickup/modify/' . self::$objectId,
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"address": "Address modifiée 2"}'
        );

        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);
    }

    /**
     * Tests list of Ride
     */
    public function testList()
    {
        //Tests with date
        $this->clientAuthenticated->request('GET', '/pickup/list/2018-11-20');
        $response = $this->clientAuthenticated->getResponse();
        $content = $this->assertJsonResponse($response, 200);
        $this->assertInternalType('array', $content);
        $first = $content[0];
        $this->assertArrayHasKey('pickupId', $first);

        //Tests with date and status
        $this->clientAuthenticated->request('GET', '/pickup/list/2018-11-20/absent');
        $response = $this->clientAuthenticated->getResponse();
        $content = $this->assertJsonResponse($response, 200);
        $this->assertInternalType('array', $content);
        $first = $content[0];
        $this->assertArrayHasKey('pickupId', $first);

        //Tests with date and status
        $this->clientAuthenticated->request('GET', '/pickup/list/2018-11-20/supported');
        $response = $this->clientAuthenticated->getResponse();
        $content = $this->assertJsonResponse($response, 200);
        $this->assertInternalType('array', $content);
        $first = $content[0];
        $this->assertArrayHasKey('pickupId', $first);

        //Tests with date for unaffected dropin
        $this->clientAuthenticated->request('GET', '/pickup/list/2018-11-20/unaffected/dropin');
        $response = $this->clientAuthenticated->getResponse();
        $content = $this->assertJsonResponse($response, 200);
        $this->assertInternalType('array', $content);
        $first = $content[0];
        $this->assertArrayHasKey('pickupId', $first);

        //Tests with date for unaffected dropoff
        $this->clientAuthenticated->request('GET', '/pickup/list/2018-11-20/unaffected/dropoff');
        $response = $this->clientAuthenticated->getResponse();
        $content = $this->assertJsonResponse($response, 200);
        $this->assertInternalType('array', $content);
        $first = $content[0];
        $this->assertArrayHasKey('pickupId', $first);
    }

    /**
     * Tests affect
     */
    public function testAffect()
    {
        //Tests with dropin
        $this->clientAuthenticated->request('PUT', '/pickup/affect/2018-10-22/dropin');
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);
        $this->clientAuthenticated->request('PUT', '/pickup/affect/2018-10-22/dropin/true');
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);

        //Tests with dropoff
        $this->clientAuthenticated->request('PUT', '/pickup/affect/2018-10-22/dropoff');
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);
        $this->clientAuthenticated->request('PUT', '/pickup/affect/2018-10-22/dropoff/true');
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);

        //Tests with all
        $this->clientAuthenticated->request('PUT', '/pickup/affect/2018-10-22/all');
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);
        $this->clientAuthenticated->request('PUT', '/pickup/affect/2018-10-22/all/true');
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);
    }

    /**
     * Tests unaffect
     */
    public function testUnaffect()
    {
        //Tests with dropin
        $this->clientAuthenticated->request('PUT', '/pickup/unaffect/2018-10-22/dropin');
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);

        //Tests with dropoff
        $this->clientAuthenticated->request('PUT', '/pickup/unaffect/2018-10-22/dropoff');
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);

        //Tests with all
        $this->clientAuthenticated->request('PUT', '/pickup/unaffect/2018-10-22/all');
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);
    }

    /**
     * Tests affect Pickups to linkedRide
     */
    public function testLinkedRide()
    {
        $this->clientAuthenticated->request('PUT', '/pickup/affect-linked-ride/10');
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);
    }

    /**
     * Tests Dispatch Pickups
     */
    public function testDispatch()
    {
        $this->clientAuthenticated->request(
            'PUT',
            '/pickup/dispatch',
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '[{"pickupId": 1, "rideId": 1, "sortOrder": 1, "validated": "validated", "start": "2018-10-22 09:00:00"}]'
        );
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);
    }

    /**
     * Tests delete Pickup AND physically deletes it
     */
    public function testDelete()
    {
        //Tests by id
        $this->clientAuthenticated->request('DELETE', '/pickup/delete/' . self::$objectId);
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);

        //Tests by registrationId
        $this->clientAuthenticated->request('DELETE', '/pickup/delete-registration/1');
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);

        //Deletes physically the entity created by test
        $this->deleteEntity('Pickup', 'pickupId', self::$objectId);
        $this->deleteEntity('Pickup', 'pickupId', self::$objectId + 1);
        $this->deleteEntity('Pickup', 'pickupId', self::$objectId + 2);
        $this->deleteEntity('Pickup', 'pickupId', self::$objectId + 3);
        $this->deleteEntity('Pickup', 'pickupId', self::$objectId + 4);
    }
}

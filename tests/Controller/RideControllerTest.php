<?php

namespace App\Tests\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\Ride;
use App\Tests\TestTrait;

class RideControllerTest extends WebTestCase
{
    use TestTrait;

    /**
     * Tests creation Ride
     */
    public function testCreate()
    {
        $this->clientAuthenticated->request(
            'POST',
            '/ride/create',
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"locked": true, "kind": "dropin", "linkedRide": 2, "date": "2030-01-31", "name": "Name", "places": 8, "start": "08:00:00", "arrival": "09:00:00", "startPoint": "Start point", "endPoint": "End point", "staff": "1", "vehicle": "1"}'
        );
        $response = $this->clientAuthenticated->getResponse();
        $content = $this->assertJsonResponse($response, 200);
        $this->assertArrayHasKey('rideId', $content['ride']);

        self::$objectId = $content['ride']['rideId'];

        //Tests multiple creation
        $this->clientAuthenticated->request(
            'POST',
            '/ride/create-multiple',
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '[{"locked": true, "kind": "dropin", "linkedRide": 2, "date": "2030-01-31", "name": "Name", "places": 8, "start": "08:00:00", "arrival": "09:00:00", "startPoint": "Start point", "endPoint": "End point", "staff": "1", "vehicle": "1"}, {"locked": true, "kind": "dropin", "linkedRide": 2, "date": "2030-01-31", "name": "Name", "places": 8, "start": "08:00:00", "arrival": "09:00:00", "startPoint": "Start point", "endPoint": "End point", "staff": "1", "vehicle": "1"}]'
        );

        $response = $this->clientAuthenticated->getResponse();
        $content = $this->assertJsonResponse($response, 200);
    }

    /**
     * Tests display Ride
     */
    public function testDisplay()
    {
        $this->clientAuthenticated->request('GET', '/ride/display/' . self::$objectId);
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);
    }

    /**
     * Tests modify Ride
     */
    public function testModify()
    {
        //Tests with full data array
        $this->clientAuthenticated->request(
            'PUT',
            '/ride/modify/' . self::$objectId,
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"locked": false, "kind": "dropff", "linkedRide": 2, "date": "2030-01-31", "name": "Name", "places": 8, "start": "08:00:00", "arrival": "09:00:00", "startPoint": "Start point", "endPoint": "End point", "staff": "1", "vehicle": "1"}'
        );
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);

        //Tests with partial data array
        $this->clientAuthenticated->request(
            'PUT',
            '/ride/modify/' . self::$objectId,
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"name": "Name modifié 2"}'
        );
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);
    }

    /**
     * Tests list of Ride
     */
    public function testList()
    {
        //Tests list
        $this->clientAuthenticated->request('GET', '/ride/list');
        $response = $this->clientAuthenticated->getResponse();
        $content = $this->assertJsonResponse($response, 200);
        $this->assertInternalType('array', $content);
        $first = $content[0];
        $this->assertArrayHasKey('rideId', $first);

        //Tests with status coming
        $this->clientAuthenticated->request('GET', '/ride/list/coming');
        $response = $this->clientAuthenticated->getResponse();
        $content = $this->assertJsonResponse($response, 200);
        $this->assertInternalType('array', $content);
        $first = $content[0];
        $this->assertArrayHasKey('rideId', $first);

        //Tests with status finished
        $this->clientAuthenticated->request('GET', '/ride/list/finished');
        $response = $this->clientAuthenticated->getResponse();
        $content = $this->assertJsonResponse($response, 200);
        $this->assertInternalType('array', $content);
        $first = $content[0];
        $this->assertArrayHasKey('rideId', $first);

        //Tests with date
        $this->clientAuthenticated->request('GET', '/ride/list/2030-01-31');
        $response = $this->clientAuthenticated->getResponse();
        $content = $this->assertJsonResponse($response, 200);
        $this->assertInternalType('array', $content);
        $first = $content[0];
        $this->assertArrayHasKey('rideId', $first);
    }

    /**
     * Tests delete Ride AND physically deletes it
     */
    public function testDelete()
    {
        $this->clientAuthenticated->request('DELETE', '/ride/delete/' . self::$objectId);
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);

        //Deletes physically the entity created by test
        $this->deleteEntity('Ride', 'rideId', self::$objectId);
    }
}

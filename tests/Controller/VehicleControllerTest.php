<?php

namespace App\Tests\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\Vehicle;
use App\Tests\TestTrait;

class VehicleControllerTest extends WebTestCase
{
    use TestTrait;

    /**
     * Tests creation Vehicle
     */
    public function testCreate()
    {
        $this->clientAuthenticated->request(
            'POST',
            '/vehicle/create',
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"name": "Name", "matriculation": "XX-111-XX", "combustible": "diesel", "places": 8, "photo": "/url/photo"}'
        );
        $response = $this->clientAuthenticated->getResponse();
        $content = $this->assertJsonResponse($response, 200);
        $this->assertArrayHasKey('vehicleId', $content['vehicle']);

        self::$objectId = $content['vehicle']['vehicleId'];
    }

    /**
     * Tests display Vehicle
     */
    public function testDisplay()
    {
        $this->clientAuthenticated->request('GET', '/vehicle/display/' . self::$objectId);
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);
    }

    /**
     * Tests modify Vehicle
     */
    public function testModify()
    {
        //Tests with full data array
        $this->clientAuthenticated->request(
            'PUT',
            '/vehicle/modify/' . self::$objectId,
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"name": "Name modified", "matriculation": "XX-111-XX", "combustible": "diesel", "places": 8, "photo": "/url/photo"}'
        );
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);

        //Tests with partial data array
        $this->clientAuthenticated->request(
            'PUT',
            '/vehicle/modify/' . self::$objectId,
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"name": "Name modified 2"}'
        );
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);
    }

    /**
     * Tests list of Vehicle
     */
    public function testList()
    {
        //Tests list
        $this->clientAuthenticated->request('GET', '/vehicle/list');
        $response = $this->clientAuthenticated->getResponse();
        $content = $this->assertJsonResponse($response, 200);
        $this->assertInternalType('array', $content);
        $first = $content[0];
        $this->assertArrayHasKey('vehicleId', $first);
    }

    /**
     * Tests delete Vehicle AND physically deletes it
     */
    public function testDelete()
    {
        $this->clientAuthenticated->request('DELETE', '/vehicle/delete/' . self::$objectId);
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);

        //Deletes physically the entity created by test
        $this->deleteEntity('Vehicle', 'vehicleId', self::$objectId);
    }
}

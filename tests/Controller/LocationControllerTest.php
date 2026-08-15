<?php

namespace App\Tests\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\Location;
use App\Tests\TestTrait;

class LocationControllerTest extends WebTestCase
{
    use TestTrait;

    /**
     * Tests creation Location
     */
    public function testCreate()
    {
        $this->clientAuthenticated->request(
            'POST',
            '/location/create',
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"name": "Name", "address": "address"}'
        );
        $response = $this->clientAuthenticated->getResponse();
        $content = $this->assertJsonResponse($response, 200);
        $this->assertArrayHasKey('locationId', $content['location']);

        self::$objectId = $content['location']['locationId'];
    }

    /**
     * Tests display Location
     */
    public function testDisplay()
    {
        $this->clientAuthenticated->request('GET', '/location/display/' . self::$objectId);
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);
    }

    /**
     * Tests modify Location
     */
    public function testModify()
    {
        //Tests with full data array
        $this->clientAuthenticated->request(
            'PUT',
            '/location/modify/' . self::$objectId,
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"name": "Name modified", "address": "address 1"}'
        );
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);

        //Tests with partial data array
        $this->clientAuthenticated->request(
            'PUT',
            '/location/modify/' . self::$objectId,
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"name": "Name modified 2"}'
        );
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);
    }

    /**
     * Tests list of Location
     */
    public function testList()
    {
        //Tests list
        $this->clientAuthenticated->request('GET', '/location/list');
        $response = $this->clientAuthenticated->getResponse();
        $content = $this->assertJsonResponse($response, 200);
        $this->assertInternalType('array', $content);
        $first = $content[0];
        $this->assertArrayHasKey('locationId', $first);
    }

    /**
     * Tests delete Location AND physically deletes it
     */
    public function testDelete()
    {
        $this->clientAuthenticated->request('DELETE', '/location/delete/' . self::$objectId);
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);

        //Deletes physically the entity created by test
        $this->deleteEntity('Location', 'locationId', self::$objectId);
    }
}

<?php

namespace App\Tests\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\Staff;
use App\Tests\TestTrait;

class StaffControllerTest extends WebTestCase
{
    use TestTrait;

    /**
     * Tests creation Staff
     */
    public function testCreate()
    {
        $this->clientAuthenticated->request(
            'POST',
            '/staff/create',
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"kind": "driver", "person": "9999", "maxChildren": 8, "priority": 10, "vehicle": 1, "address": 1}'
        );
        $response = $this->clientAuthenticated->getResponse();
        $content = $this->assertJsonResponse($response, 200);
        $this->assertArrayHasKey('staffId', $content['staff']);

        self::$objectId = $content['staff']['staffId'];
    }

    /**
     * Tests display Staff
     */
    public function testDisplay()
    {
        $this->clientAuthenticated->request('GET', '/staff/display/' . self::$objectId);
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);
    }

    /**
     * Tests modify Staff
     */
    public function testModify()
    {
        //Tests with full data array
        $this->clientAuthenticated->request(
            'PUT',
            '/staff/modify/' . self::$objectId,
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"kind": "staff", "person": "9999", "maxChildren": 8, "priority": 10, "vehicle": 1, "address": 1}'
        );
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);

        //Tests with partial data array
        $this->clientAuthenticated->request(
            'PUT',
            '/staff/modify/' . self::$objectId,
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"kind": "driver"}'
        );
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);
    }

    /**
     * Tests add priority
     */
    public function testPriority()
    {
        //Tests with full data array
        $this->clientAuthenticated->request(
            'PUT',
            '/staff/priority',
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '[{"staff": "9999", "priority": 10}]'
        );
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);
    }

    /**
     * Tests list of Staff
     */
    public function testList()
    {
        //Tests list
        $this->clientAuthenticated->request('GET', '/staff/list');
        $response = $this->clientAuthenticated->getResponse();
        $content = $this->assertJsonResponse($response, 200);
        $this->assertInternalType('array', $content);
        $first = $content[0];
        $this->assertArrayHasKey('staffId', $first);

        //Test with driver
        $this->clientAuthenticated->request('GET', '/staff/list/driver');
        $response = $this->clientAuthenticated->getResponse();
        $content = $this->assertJsonResponse($response, 200);
        $this->assertInternalType('array', $content);
        $first = $content[0];
        $this->assertArrayHasKey('staffId', $first);
    }

    /**
     * Tests delete Staff AND physically deletes it
     */
    public function testDelete()
    {
        $this->clientAuthenticated->request('DELETE', '/staff/delete/' . self::$objectId);
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);

        //Deletes physically the entity created by test
        $this->deleteEntity('Staff', 'staffId', self::$objectId);
    }
}

<?php

namespace App\Tests\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\Television;
use App\Tests\TestTrait;

class TelevisionControllerTest extends WebTestCase
{
    use TestTrait;

    /**
     * Tests creation Television
     */
    public function testCreate()
    {
        $this->clientAuthenticated->request(
            'POST',
            '/television/create',
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"start": "08:00:00", "end": "12:00:00", "module": "Module"}'
        );
        $response = $this->clientAuthenticated->getResponse();
        $content = $this->assertJsonResponse($response, 200);
        $this->assertArrayHasKey('televisionId', $content['television']);

        self::$objectId = $content['television']['televisionId'];
    }

    /**
     * Tests display Television
     */
    public function testDisplay()
    {
        $this->clientAuthenticated->request('GET', '/television/display/' . self::$objectId);
        $response = $this->clientAuthenticated->getResponse();
        $content = $this->assertJsonResponse($response, 200);
        $this->assertInternalType('array', $content);
        $first = $content[0];
        $this->assertArrayHasKey('televisionId', $first);
    }

    /**
     * Tests modify Television
     */
    public function testModify()
    {
        //Tests with full data array
        $this->clientAuthenticated->request(
            'PUT',
            '/television/modify/' . self::$objectId,
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"start": "09:00:00", "end": "12:00:00", "module": "Module"}'
        );
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);

        //Tests with partial data array
        $this->clientAuthenticated->request(
            'PUT',
            '/television/modify/' . self::$objectId,
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"start": "09:00:00"}'
        );
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);
    }

    /**
     * Tests list of Television
     */
    public function testList()
    {
        //Tests list
        $this->clientAuthenticated->request('GET', '/television/list');
        $response = $this->clientAuthenticated->getResponse();
        $content = $this->assertJsonResponse($response, 200);
        $this->assertInternalType('array', $content);
        $first = $content[0];
        $this->assertArrayHasKey('televisionId', $first);
    }

    /**
     * Tests delete Television AND physically deletes it
     */
    public function testDelete()
    {
        $this->clientAuthenticated->request('DELETE', '/television/delete/' . self::$objectId);
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);

        //Deletes physically the entity created by test
        $this->deleteEntity('Television', 'televisionId', self::$objectId);
    }
}

<?php

namespace App\Tests\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\Component;
use App\Tests\TestTrait;

class ComponentControllerTest extends WebTestCase
{
    use TestTrait;

    /**
     * Tests creation Component
     */
    public function testCreate()
    {
        $this->clientAuthenticated->request(
            'POST',
            '/component/create',
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"nameFr": "name fr", "nameEn": "name en", "vat": 5.5}'
        );
        $response = $this->clientAuthenticated->getResponse();
        $content = $this->assertJsonResponse($response, 200);
        $this->assertArrayHasKey('componentId', $content['component']);

        self::$objectId = $content['component']['componentId'];
    }

    /**
     * Tests display Component
     */
    public function testDisplay()
    {
        $this->clientAuthenticated->request('GET', '/component/display/' . self::$objectId);
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);
    }

    /**
     * Tests modify Component
     */
    public function testModify()
    {
        //Tests with full data array
        $this->clientAuthenticated->request(
            'PUT',
            '/component/modify/' . self::$objectId,
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"nameFr": "name fr modifié", "nameEn": "name en", "vat": 5.5}'
        );
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);

        //Tests with partial data array
        $this->clientAuthenticated->request(
            'PUT',
            '/component/modify/' . self::$objectId,
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"nameFr": "name fr modifié 2"}'
        );
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);
    }

    /**
     * Tests list of Component
     */
    public function testList()
    {
        //Tests list
        $this->clientAuthenticated->request('GET', '/component/list');
        $response = $this->clientAuthenticated->getResponse();
        $content = $this->assertJsonResponse($response, 200);
        $this->assertInternalType('array', $content);
        $first = $content[0];
        $this->assertArrayHasKey('componentId', $first);
    }

    /**
     * Tests delete Component AND physically deletes it
     */
    public function testDelete()
    {
        $this->clientAuthenticated->request('DELETE', '/component/delete/' . self::$objectId);
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);

        //Deletes physically the entity created by test
        $this->deleteEntity('Component', 'componentId', self::$objectId);
    }
}

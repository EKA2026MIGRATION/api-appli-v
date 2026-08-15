<?php

namespace App\Tests\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\Food;
use App\Tests\TestTrait;

class FoodControllerTest extends WebTestCase
{
    use TestTrait;

    /**
     * Tests creation of Food
     */
    public function testCreate()
    {
        $this->clientAuthenticated->request(
            'POST',
            '/food/create',
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"name": "Name", "description": "Description", "kind": "Kind", "status": "active", "photo": "/url/photo"}'
        );
        $response = $this->clientAuthenticated->getResponse();
        $content = $this->assertJsonResponse($response, 200);
        $this->assertArrayHasKey('foodId', $content['food']);

        self::$objectId = $content['food']['foodId'];
    }

    /**
     * Tests display of Food
     */
    public function testDisplay()
    {
        $this->clientAuthenticated->request('GET', '/food/display/' . self::$objectId);
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);
    }

    /**
     * Tests modify of Food
     */
    public function testModify()
    {
        //Tests with full data array
        $this->clientAuthenticated->request(
            'PUT',
            '/food/modify/' . self::$objectId,
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"name": "Name modifié", "description": "Description", "kind": "Kind", "status": "active", "photo": "/url/photo"}'
        );
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);

        //Tests with partial data array
        $this->clientAuthenticated->request(
            'PUT',
            '/food/modify/' . self::$objectId,
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"name": "Name modifié 2"}'
        );
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);
    }

    /**
     * Tests list of Food
     */
    public function testList()
    {
        //Tests list
        $this->clientAuthenticated->request('GET', '/food/list');
        $response = $this->clientAuthenticated->getResponse();
        $content = $this->assertJsonResponse($response, 200);
        $this->assertInternalType('array', $content);
        $first = $content[0];
        $this->assertArrayHasKey('foodId', $first);
    }

    /**
     * Tests delete Food AND physically deletes it
     */
    public function testDelete()
    {
        $this->clientAuthenticated->request('DELETE', '/food/delete/' . self::$objectId);
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);

        //Deletes physically the entity created by test
        $this->deleteEntity('Food', 'foodId', self::$objectId);
    }
}

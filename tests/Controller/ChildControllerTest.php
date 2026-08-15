<?php

namespace App\Tests\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\Child;
use App\Tests\TestTrait;

class ChildControllerTest extends WebTestCase
{
    use TestTrait;

    /**
     * Tests creation Child
     */
    public function testCreate()
    {
        $this->clientAuthenticated->request(
            'POST',
            '/child/create',
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"gender": "m", "firstname": "Melvin", "lastname": "Marquet", "phone": "0123456789", "birthdate": "2018-01-01", "medical": "medical", "photo": "/url/photo", "school": "1", "franceResident": true, "links": [{"personId": "2", "relation": "Relation"}], "siblings": [{"siblingId": "1", "relation": "Relation"}]}'
        );
        $response = $this->clientAuthenticated->getResponse();
        $content = $this->assertJsonResponse($response, 200);
        $this->assertArrayHasKey('childId', $content['child']);

        self::$objectId = $content['child']['childId'];
    }

    /**
     * Tests display Child
     */
    public function testDisplay()
    {
        $this->clientAuthenticated->request('GET', '/child/display/' . self::$objectId);
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);
    }

    /**
     * Tests modify Child
     */
    public function testModify()
    {
        //Tests with full data array
        $this->clientAuthenticated->request(
            'PUT',
            '/child/modify/' . self::$objectId,
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"gender": "m", "firstname": "Melvin", "lastname": "Marquet", "phone": "1234567890", "birthdate": "2018-01-01", "medical": "medical", "photo": "/url/photo", "school": "1", "franceResident": true, "links": [{"personId": "2", "relation": "Relation"}], "siblings": [{"siblingId": "1", "relation": "Relation"}]}'
        );
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);

        //Tests with partial data array
        $this->clientAuthenticated->request(
            'PUT',
            '/child/modify/' . self::$objectId,
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"phone": "0123456789"}'
        );
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);
    }

    /**
     * Tests list of Child
     */
    public function testList()
    {
        //Tests list
        $this->clientAuthenticated->request('GET', '/child/list');
        $response = $this->clientAuthenticated->getResponse();
        $content = $this->assertJsonResponse($response, 200);
        $this->assertInternalType('array', $content);
        $first = $content[0];
        $this->assertArrayHasKey('childId', $first);
    }

    /**
     * Tests search of Child
     */
    public function testSearch()
    {
        $this->clientAuthenticated->request('GET', '/child/search/melvin');
        $response = $this->clientAuthenticated->getResponse();
        $content = $this->assertJsonResponse($response, 200);
        $this->assertInternalType('array', $content);
        $first = $content[0];
        $this->assertArrayHasKey('childId', $first);
    }

    /**
     * Tests delete Child AND physically deletes it
     */
    public function testDelete()
    {
        $this->clientAuthenticated->request('DELETE', '/child/delete/' . self::$objectId);
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);

        //Deletes physically the entity created by test
        $this->deleteEntity('Child', 'childId', self::$objectId);
    }
}

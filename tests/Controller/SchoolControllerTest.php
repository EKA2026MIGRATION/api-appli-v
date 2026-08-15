<?php

namespace App\Tests\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\School;
use App\Tests\TestTrait;

class SchoolControllerTest extends WebTestCase
{
    use TestTrait;

    /**
     * Tests creation School
     */
    public function testCreate()
    {
        $this->clientAuthenticated->request(
            'POST',
            '/school/create',
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"name": "Name", "address": "address 1", "postal": "11111", "town": "Town", "country": "Country", "googlePlaceId": "1234", "photo": "url/de/la/photo"}'
        );
        $response = $this->clientAuthenticated->getResponse();
        $content = $this->assertJsonResponse($response, 200);
        $this->assertArrayHasKey('schoolId', $content['school']);

        self::$objectId = $content['school']['schoolId'];
    }

    /**
     * Tests display School
     */
    public function testDisplay()
    {
        $this->clientAuthenticated->request('GET', '/school/display/' . self::$objectId);
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);
    }

    /**
     * Tests list of School
     */
    public function testList()
    {
        //Tests list
        $this->clientAuthenticated->request('GET', '/school/list');
        $response = $this->clientAuthenticated->getResponse();
        $content = $this->assertJsonResponse($response, 200);
        $this->assertInternalType('array', $content);
        $first = $content[0];
        $this->assertArrayHasKey('schoolId', $first);
    }

    /**
     * Tests search of School
     */
    public function testSearch()
    {
        $this->clientAuthenticated->request('GET', '/school/search/name');
        $response = $this->clientAuthenticated->getResponse();
        $content = $this->assertJsonResponse($response, 200);
        $this->assertInternalType('array', $content);
        $first = $content[0];
        $this->assertArrayHasKey('schoolId', $first);
    }

    /**
     * Tests modify School
     */
    public function testModify()
    {
        //Tests with full data array
        $this->clientAuthenticated->request(
            'PUT',
            '/school/modify/' . self::$objectId,
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"name": "Name", "address": "address modifiée 1", "postal": "11111", "town": "Town", "country": "Country", "googlePlaceId": "1234", "photo": "url/de/la/photo"}'
        );
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);

        //Tests with partial data array
        $this->clientAuthenticated->request(
            'PUT',
            '/school/modify/' . self::$objectId,
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"name": "Name modifié 2"}'
        );
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);
    }

    /**
     * Tests delete School AND physically deletes it
     */
    public function testDelete()
    {
        $this->clientAuthenticated->request('DELETE', '/school/delete/' . self::$objectId);
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);

        //Deletes physically the entity created by test
        $this->deleteEntity('School', 'schoolId', self::$objectId);
    }
}

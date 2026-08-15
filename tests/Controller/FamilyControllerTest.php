<?php

namespace App\Tests\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\Family;
use App\Tests\TestTrait;

class FamilyControllerTest extends WebTestCase
{
    use TestTrait;

    /**
     * Tests creation Family
     */
    public function testCreate()
    {
        $this->clientAuthenticated->request(
            'POST',
            '/family/create',
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"name": "Famille"}'
        );
        $response = $this->clientAuthenticated->getResponse();
        $content = $this->assertJsonResponse($response, 200);
        $this->assertArrayHasKey('familyId', $content['family']);

        self::$objectId = $content['family']['familyId'];
    }

    /**
     * Tests display Family
     */
    public function testDisplay()
    {
        $this->clientAuthenticated->request('GET', '/family/display/' . self::$objectId);
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);
    }

    /**
     * Tests modify Family
     */
    public function testModify()
    {
        //Tests with full data array
        $this->clientAuthenticated->request(
            'PUT',
            '/family/modify/' . self::$objectId,
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"name": "Famille modifiée"}'
        );
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);
    }

    /**
     * Tests list of Family
     */
    public function testList()
    {
        //Tests list
        $this->clientAuthenticated->request('GET', '/family/list');
        $response = $this->clientAuthenticated->getResponse();
        $content = $this->assertJsonResponse($response, 200);
        $this->assertInternalType('array', $content);
        $first = $content[0];
        $this->assertArrayHasKey('familyId', $first);
    }

    /**
     * Tests search of Child
     */
    public function testSearch()
    {
        $this->clientAuthenticated->request('GET', '/family/search/amil');
        $response = $this->clientAuthenticated->getResponse();
        $content = $this->assertJsonResponse($response, 200);
        $this->assertInternalType('array', $content);
        $first = $content[0];
        $this->assertArrayHasKey('familyId', $first);
    }

    /**
     * Tests delete Family AND physically deletes it
     */
    public function testDelete()
    {
        $this->clientAuthenticated->request('DELETE', '/family/delete/' . self::$objectId);
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);

        //Deletes physically the entity created by test
        $this->deleteEntity('Family', 'familyId', self::$objectId);
    }
}

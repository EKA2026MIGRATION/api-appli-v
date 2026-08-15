<?php

namespace App\Tests\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\Phone;
use App\Tests\TestTrait;

class PhoneControllerTest extends WebTestCase
{
    use TestTrait;

    /**
     * Tests creation Phone
     */
    public function testCreate()
    {
        $this->clientAuthenticated->request(
            'POST',
            '/phone/create',
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"name": "Name", "phone": "0123456789", "links": {"personId": "1"}}'
        );
        $response = $this->clientAuthenticated->getResponse();
        $content = $this->assertJsonResponse($response, 200);
        $this->assertArrayHasKey('phoneId', $content['phone']);

        self::$objectId = $content['phone']['phoneId'];
    }

    /**
     * Tests display Phone
     */
    public function testDisplay()
    {
        $this->clientAuthenticated->request('GET', '/phone/display/' . self::$objectId);
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);
    }

    /**
     * Tests modify Phone
     */
    public function testModify()
    {
        //Tests with full data array
        $this->clientAuthenticated->request(
            'PUT',
            '/phone/modify/' . self::$objectId,
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"name": "Name modifié", "phone": "0123456789", "links": {"personId": "1"}}'
        );
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);

        //Tests with partial data array
        $this->clientAuthenticated->request(
            'PUT',
            '/phone/modify/' . self::$objectId,
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"name": "Name modifié 2"}'
        );
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);
    }

    /**
     * Tests delete Phone AND physically deletes it
     */
    public function testDelete()
    {
        $this->clientAuthenticated->request('DELETE', '/phone/delete/' . self::$objectId);
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);

        //Deletes physically the entity created by test
        $this->deleteEntity('Phone', 'phoneId', self::$objectId);
    }
}

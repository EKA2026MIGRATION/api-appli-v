<?php

namespace App\Tests\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\Address;
use App\Tests\TestTrait;

class AddressControllerTest extends WebTestCase
{
    use TestTrait;

    /**
     * Tests creation Address
     */
    public function testCreate()
    {
        $this->clientAuthenticated->request(
            'POST',
            '/address/create',
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"name": "Name", "address": "address 1", "address2": "address 2", "postal": "11111", "town": "Town", "country": "Country", "links": {"personId": "1"}}'
        );
        $response = $this->clientAuthenticated->getResponse();
        $content = $this->assertJsonResponse($response, 200);
        $this->assertArrayHasKey('addressId', $content['address']);

        self::$objectId = $content['address']['addressId'];
    }

    /**
     * Tests display Address
     */
    public function testDisplay()
    {
        $this->clientAuthenticated->request('GET', '/address/display/' . self::$objectId);
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);
    }

    /**
     * Tests modify Address
     */
    public function testModify()
    {
        //Tests with full data array
        $this->clientAuthenticated->request(
            'PUT',
            '/address/modify/' . self::$objectId,
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"name": "Name modifié", "address": "address 1", "address2": "address 2", "postal": "11111", "town": "Town", "country": "Country", "links": {"person": "1"}}'
        );
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);

        //Tests with partial data array
        $this->clientAuthenticated->request(
            'PUT',
            '/address/modify/' . self::$objectId,
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"name": "Name modifié 2"}'
        );
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);
    }

    /**
     * Tests delete Address AND physically deletes it
     */
    public function testDelete()
    {
        $this->clientAuthenticated->request('DELETE', '/address/delete/' . self::$objectId);
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);

        //Deletes physically the entity created by test
        $this->deleteEntity('Address', 'addressId', self::$objectId);
    }
}

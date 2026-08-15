<?php

namespace App\Tests\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\Registration;
use App\Tests\TestTrait;

class RegistrationControllerTest extends WebTestCase
{
    use TestTrait;

    /**
     * Tests creation Registration
     */
    public function testCreate()
    {
        $this->clientAuthenticated->request(
            'POST',
            '/registration/create',
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"child": "1", "person": "1", "product": "1", "invoice": 1, "payed": "100.50", "status": "cart", "preferences": [{"addressId": 1, "phoneId": 1}], "sessions": [{"date": "2018-01-20", "start": "09:00:00", "end": "18:00:00"}, {"date": "2018-01-20", "start": "09:00:00", "end": "18:00:00"}], "location": 1, "sports": [{"sportId": 1}, {"sportId": 2}]}'
        );
        $response = $this->clientAuthenticated->getResponse();
        $content = $this->assertJsonResponse($response, 200);
        $this->assertArrayHasKey('registrationId', $content['registration']);

        self::$objectId = $content['registration']['registrationId'];
    }

    /**
     * Tests display Registration
     */
    public function testDisplay()
    {
        $this->clientAuthenticated->request('GET', '/registration/display/' . self::$objectId);
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);
    }

    /**
     * Tests modify Registration
     */
    public function testModify()
    {
        //Tests with full data array
        $this->clientAuthenticated->request(
            'PUT',
            '/registration/modify/' . self::$objectId,
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"child": "1", "person": "1", "product": "1", "invoice": 1, "payed": "100.50", "status": "in-progress", "preferences": [{"addressId": 1, "phoneId": 1}], "sessions": [{"date": "2018-01-20", "start": "09:00:00", "end": "18:00:00"}, {"date": "2018-01-20", "start": "09:00:00", "end": "18:00:00"}], "location": 1, "sports": [{"sportId": 1}, {"sportId": 2}]}'
        );
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);

        //Tests with partial data array
        $this->clientAuthenticated->request(
            'PUT',
            '/registration/modify/' . self::$objectId,
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"status": "payed"}'
        );
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);
    }

    /**
     * Tests list of Registration
     */
    public function testList()
    {
        //Tests list
        $this->clientAuthenticated->request('GET', '/registration/list');
        $response = $this->clientAuthenticated->getResponse();
        $content = $this->assertJsonResponse($response, 200);
        $this->assertInternalType('array', $content);
        $first = $content[0];
        $this->assertArrayHasKey('registrationId', $first);

        //Tests list with status
        $this->clientAuthenticated->request('GET', '/registration/list/payed');
        $response = $this->clientAuthenticated->getResponse();
        $content = $this->assertJsonResponse($response, 200);
        $this->assertInternalType('array', $content);
        $first = $content[0];
        $this->assertArrayHasKey('registrationId', $first);

        //Tests list with personId and status
        $this->clientAuthenticated->request('GET', '/registration/list/1/payed');
        $response = $this->clientAuthenticated->getResponse();
        $content = $this->assertJsonResponse($response, 200);
        $this->assertInternalType('array', $content);
        $first = $content[0];
        $this->assertArrayHasKey('registrationId', $first);

        //Tests list without cart
        $this->clientAuthenticated->request('GET', '/registration/list/without-cart');
        $response = $this->clientAuthenticated->getResponse();
        $content = $this->assertJsonResponse($response, 200);
        $this->assertInternalType('array', $content);
        $first = $content[0];
        $this->assertArrayHasKey('registrationId', $first);
    }

    /**
     * Tests delete Registration AND physically deletes it
     */
    public function testDelete()
    {
        $this->clientAuthenticated->request('DELETE', '/registration/delete/' . self::$objectId);
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);

        //Deletes physically the entity created by test
        $this->deleteEntity('Registration', 'registrationId', self::$objectId);
    }
}

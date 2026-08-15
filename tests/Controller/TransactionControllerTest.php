<?php

namespace App\Tests\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\Transaction;
use App\Tests\TestTrait;

class TransactionControllerTest extends WebTestCase
{
    use TestTrait;

    /**
     * Tests creation Transaction
     */
    public function testCreate()
    {
        $this->clientAuthenticated->request(
            'POST',
            '/transaction/create',
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"date": "2019-03-06 10:00:00", "internalOrder": "123456789XYZ", "status": "in-progress", "number": "Number", "amount": "152.25", "person": "1", "invoice": "1", "registrations": [{"registrationId": "1"}, {"registrationId": "2"}]}'
        );
        $response = $this->clientAuthenticated->getResponse();
        $content = $this->assertJsonResponse($response, 200);
        $this->assertArrayHasKey('transactionId', $content['transaction']);

        self::$objectId = $content['transaction']['transactionId'];
    }

    /**
     * Tests display Transaction
     */
    public function testDisplay()
    {
        //Tests with id
        $this->clientAuthenticated->request('GET', '/transaction/display/' . self::$objectId);
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);

        //Tests with internalOrder
        $this->clientAuthenticated->request('GET', '/transaction/display/123456789XYZ');
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);
    }

    /**
     * Tests modify Transaction
     */
    public function testModify()
    {
        //Tests with full data array
        $this->clientAuthenticated->request(
            'PUT',
            '/transaction/modify/123456789XYZ',
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"status": "unpayed", "invoice": "1"}'
        );
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);

        //Tests with partial data array
        $this->clientAuthenticated->request(
            'PUT',
            '/transaction/modify/123456789XYZ',
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"status": "payed"}'
        );
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);
    }

    /**
     * Tests list of Transaction
     */
    public function testList()
    {
        //Tests list by date
        $this->clientAuthenticated->request('GET', '/transaction/list/2019-03-06');
        $response = $this->clientAuthenticated->getResponse();
        $content = $this->assertJsonResponse($response, 200);
        $this->assertInternalType('array', $content);
        $first = $content[0];
        $this->assertArrayHasKey('transactionId', $first);

        //Tests list by month
        $this->clientAuthenticated->request('GET', '/transaction/list/2019-03');
        $response = $this->clientAuthenticated->getResponse();
        $content = $this->assertJsonResponse($response, 200);
        $this->assertInternalType('array', $content);
        $first = $content[0];
        $this->assertArrayHasKey('transactionId', $first);

        //Tests list by date and status
        $this->clientAuthenticated->request('GET', '/transaction/list/2019-03-06/payed');
        $response = $this->clientAuthenticated->getResponse();
        $content = $this->assertJsonResponse($response, 200);
        $this->assertInternalType('array', $content);
        $first = $content[0];
        $this->assertArrayHasKey('transactionId', $first);

        //Tests list by month and status
        $this->clientAuthenticated->request('GET', '/transaction/list/2019-03/payed');
        $response = $this->clientAuthenticated->getResponse();
        $content = $this->assertJsonResponse($response, 200);
        $this->assertInternalType('array', $content);
        $first = $content[0];
        $this->assertArrayHasKey('transactionId', $first);

        //Tests list by year and status
        $this->clientAuthenticated->request('GET', '/transaction/list/2019/payed');
        $response = $this->clientAuthenticated->getResponse();
        $content = $this->assertJsonResponse($response, 200);
        $this->assertInternalType('array', $content);
        $first = $content[0];
        $this->assertArrayHasKey('transactionId', $first);

        //Tests list by date and person
        $this->clientAuthenticated->request('GET', '/transaction/list/2019-03-06/1');
        $response = $this->clientAuthenticated->getResponse();
        $content = $this->assertJsonResponse($response, 200);
        $this->assertInternalType('array', $content);
        $first = $content[0];
        $this->assertArrayHasKey('transactionId', $first);

        //Tests list by month and person
        $this->clientAuthenticated->request('GET', '/transaction/list/2019-03/1');
        $response = $this->clientAuthenticated->getResponse();
        $content = $this->assertJsonResponse($response, 200);
        $this->assertInternalType('array', $content);
        $first = $content[0];
        $this->assertArrayHasKey('transactionId', $first);

        //Tests list by year and person
        $this->clientAuthenticated->request('GET', '/transaction/list/2019/1');
        $response = $this->clientAuthenticated->getResponse();
        $content = $this->assertJsonResponse($response, 200);
        $this->assertInternalType('array', $content);
        $first = $content[0];
        $this->assertArrayHasKey('transactionId', $first);

        //Tests list by status and person
        $this->clientAuthenticated->request('GET', '/transaction/list/payed/1');
        $response = $this->clientAuthenticated->getResponse();
        $content = $this->assertJsonResponse($response, 200);
        $this->assertInternalType('array', $content);
        $first = $content[0];
        $this->assertArrayHasKey('transactionId', $first);
    }

    /**
     * Tests delete Transaction AND physically deletes it
     */
    public function testDelete()
    {
        $this->clientAuthenticated->request('DELETE', '/transaction/delete/' . self::$objectId);
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);

        //Deletes physically the entity created by test
        $this->deleteEntity('Transaction', 'transactionId', self::$objectId);
    }
}

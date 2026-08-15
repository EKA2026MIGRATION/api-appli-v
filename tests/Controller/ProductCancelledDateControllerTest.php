<?php

namespace App\Tests\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\ProductCancelledDate;
use App\Tests\TestTrait;

class ProductCancelledDateControllerTest extends WebTestCase
{
    use TestTrait;

    /**
     * Tests creation ProductCancelledDate
     */
    public function testCreate()
    {
        $this->clientAuthenticated->request(
            'POST',
            '/product-cancelled-date/create',
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"date": "2019-03-06", "category": "1", "product": "1", "messageFr": "Message FR", "messageEn": "Message EN"}'
        );
        $response = $this->clientAuthenticated->getResponse();
        $content = $this->assertJsonResponse($response, 200);
        $this->assertArrayHasKey('productCancelledDateId', $content['productCancelledDate']);

        self::$objectId = $content['productCancelledDate']['productCancelledDateId'];
    }

    /**
     * Tests display ProductCancelledDate
     */
    public function testDisplay()
    {
        $this->clientAuthenticated->request('GET', '/product-cancelled-date/display/' . self::$objectId);
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);
    }

    /**
     * Tests modify Television
     */
    public function testModify()
    {
        //Tests with full data array
        $this->clientAuthenticated->request(
            'PUT',
            '/product-cancelled-date/modify/' . self::$objectId,
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"date": "2019-03-07", "category": "1", "product": "1", "messageFr": "Message FR", "messageEn": "Message EN"}'
        );
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);

        //Tests with partial data array
        $this->clientAuthenticated->request(
            'PUT',
            '/product-cancelled-date/modify/' . self::$objectId,
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"date": "2019-03-06"}'
        );
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);
    }

    /**
     * Tests list of ProductCancelledDate
     */
    public function testList()
    {
        //Tests list by date
        $this->clientAuthenticated->request('GET', '/product-cancelled-date/list/2019-03-06');
        $response = $this->clientAuthenticated->getResponse();
        $content = $this->assertJsonResponse($response, 200);
        $this->assertInternalType('array', $content);
        $first = $content[0];
        $this->assertArrayHasKey('productCancelledDateId', $first);

        //Tests list by month
        $this->clientAuthenticated->request('GET', '/product-cancelled-date/list/2019-03');
        $response = $this->clientAuthenticated->getResponse();
        $content = $this->assertJsonResponse($response, 200);
        $this->assertInternalType('array', $content);
        $first = $content[0];
        $this->assertArrayHasKey('productCancelledDateId', $first);

        //Tests list by year
        $this->clientAuthenticated->request('GET', '/product-cancelled-date/list/2019');
        $response = $this->clientAuthenticated->getResponse();
        $content = $this->assertJsonResponse($response, 200);
        $this->assertInternalType('array', $content);
        $first = $content[0];
        $this->assertArrayHasKey('productCancelledDateId', $first);

        //Tests list all
        $this->clientAuthenticated->request('GET', '/product-cancelled-date/list');
        $response = $this->clientAuthenticated->getResponse();
        $content = $this->assertJsonResponse($response, 200);
        $this->assertInternalType('array', $content);
        $first = $content[0];
        $this->assertArrayHasKey('productCancelledDateId', $first);

        //Tests list by date and category
        $this->clientAuthenticated->request('GET', '/product-cancelled-date/list-category/1/2019-03-06');
        $response = $this->clientAuthenticated->getResponse();
        $content = $this->assertJsonResponse($response, 200);
        $this->assertInternalType('array', $content);
        $first = $content[0];
        $this->assertArrayHasKey('productCancelledDateId', $first);

        //Tests list by month and category
        $this->clientAuthenticated->request('GET', '/product-cancelled-date/list-category/1/2019-03');
        $response = $this->clientAuthenticated->getResponse();
        $content = $this->assertJsonResponse($response, 200);
        $this->assertInternalType('array', $content);
        $first = $content[0];
        $this->assertArrayHasKey('productCancelledDateId', $first);

        //Tests list by date and product
        $this->clientAuthenticated->request('GET', '/product-cancelled-date/list-product/1/2019-03-06');
        $response = $this->clientAuthenticated->getResponse();
        $content = $this->assertJsonResponse($response, 200);
        $this->assertInternalType('array', $content);
        $first = $content[0];
        $this->assertArrayHasKey('productCancelledDateId', $first);

        //Tests list by month and product
        $this->clientAuthenticated->request('GET', '/product-cancelled-date/list-product/1/2019-03');
        $response = $this->clientAuthenticated->getResponse();
        $content = $this->assertJsonResponse($response, 200);
        $this->assertInternalType('array', $content);
        $first = $content[0];
        $this->assertArrayHasKey('productCancelledDateId', $first);
    }

    /**
     * Tests delete ProductCancelledDate AND physically deletes it
     */
    public function testDelete()
    {
        $this->clientAuthenticated->request('DELETE', '/product-cancelled-date/delete/' . self::$objectId);
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);

        //Deletes physically the entity created by test
        $this->deleteEntity('ProductCancelledDate', 'productCancelledDateId', self::$objectId);
    }
}

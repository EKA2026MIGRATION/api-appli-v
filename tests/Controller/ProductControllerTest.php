<?php

namespace App\Tests\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\Product;
use App\Tests\TestTrait;

class ProductControllerTest extends WebTestCase
{
    use TestTrait;

    /**
     * Tests creation Product
     */
    public function testCreate()
    {
        $this->clientAuthenticated->request(
            'POST',
            '/product/create',
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"family": "1", "season": "1", "nameFr": "name Fr", "nameEn": "Name En", "descriptionFr": "Description Fr", "priceTtc": "415.50", "descriptionEn": "Description En", "transport": "true", "lunch": "true", "child": 1, "photo": "/path/to/picture.jpg", "mail": 1, "isLocationSelectable": "true", "isDateSelectable": "true", "isHourSelectable": "true", "isSportSelectable": "true", "visibility": "visible", "hourDropin": "10:40:00", "hourDropoff": "11:40:00", "categories": [{"category": "1"}], "components": [{"nameFr": "string", "nameEn": "string", "vat": 5.5, "priceHt": 150, "priceVat": 8.25, "priceTtc": 158.25, "quantity": 10, "totalHt": 1500.00, "totalVat": 82.50, "totalTtc": 1582.50}, {"nameFr": "string", "nameEn": "string", "vat": 5.5, "priceHt": 150, "priceVat": 8.25, "priceTtc": 158.25, "quantity": 10, "totalHt": 1500.00, "totalVat": 82.50, "totalTtc": 1582.50}], "dates": [{"date": "2018-04-06"}], "hours": [{"start": "09:00:00", "end": "10:00:00"}], "locations": [{"location": "1"}], "sports": [{"sport": "1"}]}'
        );
        $response = $this->clientAuthenticated->getResponse();
        $content = $this->assertJsonResponse($response, 200);
        $this->assertArrayHasKey('productId', $content['product']);

        self::$objectId = $content['product']['productId'];
    }

    /**
     * Tests display Product
     */
    public function testDisplay()
    {
        $this->clientAuthenticated->request('GET', '/product/display/' . self::$objectId);
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);
    }

    /**
     * Tests modify Product
     */
    public function testModify()
    {
        //Tests with full data array
        $this->clientAuthenticated->request(
            'PUT',
            '/product/modify/' . self::$objectId,
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"family": "1", "season": "1", "nameFr": "name Fr modifié", "nameEn": "Name En modified", "descriptionFr": "Description Fr modifiée", "descriptionEn": "Description En modified", "priceTtc": "425.50", "transport": "false", "lunch": "false", "child": 1, "photo": "/path/to/picture-modified.jpg", "mail": 1, "isLocationSelectable": "false", "isDateSelectable": "false", "isHourSelectable": "false", "isSportSelectable": "false", "visibility": "invisible", "hourDropin": "11:40:00", "hourDropoff": "12:40:00", "categories": [{"category": 1}], "components": [{"nameFr": "string", "nameEn": "string", "vat": 5.5, "priceHt": 150, "priceVat": 8.25, "priceTtc": 158.25, "quantity": 10, "totalHt": 1500.00, "totalVat": 82.50, "totalTtc": 1582.50}], "dates": [{"date": "2018-04-06"}], "hours": [{"start": "09:00:00", "end": "10:00:00"}], "locations": [{"location": "1"}], "sports": [{"sport": "1"}]}'
        );
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);

        //Tests with partial data array
        $this->clientAuthenticated->request(
            'PUT',
            '/product/modify/' . self::$objectId,
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"nameFr": "name Fr modifié 2"}'
        );
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);
    }

    /**
     * Tests list of Product
     */
    public function testList()
    {
        //Tests all products
        $this->clientAuthenticated->request('GET', '/product/list');
        $response = $this->clientAuthenticated->getResponse();
        $content = $this->assertJsonResponse($response, 200);
        $this->assertInternalType('array', $content);
        $first = $content[0];
        $this->assertArrayHasKey('productId', $first);

        //Tests all products linked to a child
        $this->clientAuthenticated->request('GET', '/product/list/child/1');
        $response = $this->clientAuthenticated->getResponse();
        $content = $this->assertJsonResponse($response, 200);
        $this->assertInternalType('array', $content);
        $first = $content[0];
        $this->assertArrayHasKey('productId', $first);
    }

    /**
     * Tests search of Product
     */
    public function testSearch()
    {
        $this->clientAuthenticated->request('GET', '/product/search/name');
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);
    }

    /**
     * Tests delete Product AND physically deletes it
     */
    public function testDelete()
    {
        $this->clientAuthenticated->request('DELETE', '/product/delete/' . self::$objectId);
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);

        //Deletes physically the entity created by test
        $this->deleteEntity('Product', 'productId', self::$objectId);
    }
}

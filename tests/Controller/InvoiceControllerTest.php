<?php

namespace App\Tests\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\Invoice;
use App\Tests\TestTrait;

class InvoiceControllerTest extends WebTestCase
{
    use TestTrait;

    /**
     * Tests creation Invoice
     */
    public function testCreate()
    {
        $this->clientAuthenticated->request(
            'POST',
            '/invoice/create',
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"child": 1, "nameFr": "nom français", "nameEn": "name english", "descriptionFr": "Description fr", "descriptionEn": "Description english", "date": "2018-01-20 18:00:00", "number": "1", "paymentMethod": "card", "priceTtc": 980.00, "address": "adresse", "postal": "74800", "town": "Amancy",
            "invoiceProducts": [
                {"nameFr": "nom produit fr", "nameEn": "name product en", "descriptionFr": "description fr", "descriptionEn": "description en", "priceHt": 100.00, "priceTtc": 120.00, "quantity": 1, "totalHt": 200.00, "totalTtc": 240.00,
                    "invoiceComponents": [
                        {"nameFr": "nom composant fr", "nameEn": "name component en", "vat": "10.00", "priceHt": 100.00, "priceVat": 20.00, "priceTtc": 120.00, "quantity": 1, "totalHt": 200.00, "totalVat": 40.00, "totalTtc": 240.00},
                        {"nameFr": "nom composant 2 fr", "nameEn": "name component 2 en", "vat": "20.00", "priceHt": 100.00, "priceVat": 20.00, "priceTtc": 120.00, "quantity": 1, "totalHt": 200.00, "totalVat": 40.00, "totalTtc": 240.00}
                    ]},
                {"nameFr": "nom produit 2 fr", "nameEn": "name product 2 en", "descriptionFr": "description fr", "descriptionEn": "description en", "priceHt": 100.00, "priceTtc": 120.00, "quantity": 1, "totalHt": 200.00, "totalTtc": 240.00}
            ]}'
        );
        $response = $this->clientAuthenticated->getResponse();
        $content = $this->assertJsonResponse($response, 200);
        $this->assertArrayHasKey('invoiceId', $content['invoice']);

        self::$objectId = $content['invoice']['invoiceId'];
    }

    /**
     * Tests display Invoice
     */
    public function testDisplay()
    {
        $this->clientAuthenticated->request('GET', '/invoice/display/' . self::$objectId);
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);
    }

    /**
     * Tests modify Invoice
     */
    public function testModify()
    {
        //Tests with full data array
        $this->clientAuthenticated->request(
            'PUT',
            '/invoice/modify/' . self::$objectId,
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"child": 1, "nameFr": "nom français modifié", "nameEn": "name english modified", "descriptionFr": "Description fr modifiée", "descriptionEn": "Description english modified", "date": "2018-01-20 19:00:00", "number": "1", "paymentMethod": "check", "priceTtc": 980.00, "address": "adresse", "postal": "74800", "town": "Amancy",
            "invoiceProducts": [
                {"nameFr": "nom produit fr", "nameEn": "name product en", "descriptionFr": "description fr", "descriptionEn": "description en", "priceHt": 100.00, "priceTtc": 120.00, "quantity": 1, "totalHt": 200.00, "totalTtc": 240.00,
                    "invoiceComponents": [
                        {"nameFr": "nom composant fr", "nameEn": "name component en", "vat": "10.00", "priceHt": 100.00, "priceVat": 20.00, "priceTtc": 120.00, "quantity": 1, "totalHt": 200.00, "totalVat": 40.00, "totalTtc": 240.00},
                        {"nameFr": "nom composant 2 fr", "nameEn": "name component 2 en", "vat": "20.00", "priceHt": 100.00, "priceVat": 20.00, "priceTtc": 120.00, "quantity": 1, "totalHt": 200.00, "totalVat": 40.00, "totalTtc": 240.00}
                    ]},
                {"nameFr": "nom produit 2 fr", "nameEn": "name product 2 en", "descriptionFr": "description fr", "descriptionEn": "description en", "priceHt": 100.00, "priceTtc": 120.00, "quantity": 1, "totalHt": 200.00, "totalTtc": 240.00}
            ]}'
        );
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);

        //Tests with partial data array
        $this->clientAuthenticated->request(
            'PUT',
            '/invoice/modify/' . self::$objectId,
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"nameFr": "name Fr modifié 2"}'
        );
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);
    }

    /**
     * Tests list of Invoice
     */
    public function testList()
    {
        //Tests all invoices
        $this->clientAuthenticated->request('GET', '/invoice/list');
        $response = $this->clientAuthenticated->getResponse();
        $content = $this->assertJsonResponse($response, 200);
        $this->assertInternalType('array', $content);
        $first = $content[0];
        $this->assertArrayHasKey('invoiceId', $first);
    }

    /**
     * Tests search of Invoice
     */
    public function testSearch()
    {
        //Search on name
        $this->clientAuthenticated->request('GET', '/invoice/search/name');
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);

        //Search with dateStart
        $this->clientAuthenticated->request('GET', '/invoice/search/2018-01-20');
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);

        //Search with dateStart & dateEnd
        $this->clientAuthenticated->request('GET', '/invoice/search/2018-01-20/2018-01-21');
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);
    }

    /**
     * Tests delete Invoice AND physically deletes it
     */
    public function testDelete()
    {
        $this->clientAuthenticated->request('DELETE', '/invoice/delete/' . self::$objectId);
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);

        //Deletes physically the entity created by test
        $this->deleteEntity('Invoice', 'invoiceId', self::$objectId);
    }
}

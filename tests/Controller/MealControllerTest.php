<?php

namespace App\Tests\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\Meal;
use App\Tests\TestTrait;

class MealControllerTest extends WebTestCase
{
    use TestTrait;

    /**
     * Tests creation Meal
     */
    public function testCreate()
    {
        $this->clientAuthenticated->request(
            'POST',
            '/meal/create',
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"date": "2018-11-20", "child": "1", "person": "1", "freeName": "Free name", "links": [{"foodId": "1"}]}'
        );
        $response = $this->clientAuthenticated->getResponse();
        $content = $this->assertJsonResponse($response, 200);
        $this->assertArrayHasKey('mealId', $content['meal']);

        self::$objectId = $content['meal']['mealId'];
    }

    /**
     * Tests display Meal
     */
    public function testDisplay()
    {
        $this->clientAuthenticated->request('GET', '/meal/display/' . self::$objectId);
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);
    }

    /**
     * Tests modify Meal
     */
    public function testModify()
    {
        //Tests with full data array
        $this->clientAuthenticated->request(
            'PUT',
            '/meal/modify/' . self::$objectId,
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"date": "2018-11-20", "child": "1", "person": "1", "freeName": "Free name modifié", "links": [{"foodId": "1"}]}'
        );
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);

        //Tests with partial data array
        $this->clientAuthenticated->request(
            'PUT',
            '/meal/modify/' . self::$objectId,
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"freeName": "Free name modifié 2"}'
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
        $this->clientAuthenticated->request('GET', '/meal/list/2018-11-20');
        $response = $this->clientAuthenticated->getResponse();
        $content = $this->assertJsonResponse($response, 200);
        $this->assertInternalType('array', $content);
        $first = $content[0];
        $this->assertArrayHasKey('mealId', $first);
    }

    /**
     * Tests delete Meal AND physically deletes it
     */
    public function testDelete()
    {
        $this->clientAuthenticated->request('DELETE', '/meal/delete/' . self::$objectId);
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);

        //Deletes physically the entity created by test
        $this->deleteEntity('Meal', 'mealId', self::$objectId);
    }
}

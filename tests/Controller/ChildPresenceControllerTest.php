<?php

namespace App\Tests\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\ChildPresence;
use App\Tests\TestTrait;

class ChildPresenceControllerTest extends WebTestCase
{
    use TestTrait;

    /**
     * Tests creation ChildPresence
     */
    public function testCreate()
    {
        $this->clientAuthenticated->request(
            'POST',
            '/child/presence/create',
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '[{"registration": 1, "child": 1, "person": 1, "location": 1, "date": "2019-01-20", "start": "08:00:00", "end": "12:00:00"}, {"registration": 1, "child": 1, "person": 1, "location": 1, "date": "2019-01-20", "start": "13:00:00", "end": "17:00:00"}]'
        );
        $response = $this->clientAuthenticated->getResponse();
        $content = $this->assertJsonResponse($response, 200);
    }

    /**
     * Tests display ChildPresence
     */
    public function testDisplay()
    {
        //Tests for a day
        $this->clientAuthenticated->request('GET', '/child/presence/display/1/2019-01-20');
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);

        //Tests for a month
        $this->clientAuthenticated->request('GET', '/child/presence/display/1/2019-01');
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);

        //Tests for a year
        $this->clientAuthenticated->request('GET', '/child/presence/display/1/2019');
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);

        //Tests for whole
        $this->clientAuthenticated->request('GET', '/child/presence/display/1/all');
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);

        $this->clientAuthenticated->request('GET', '/child/presence/display/1');
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);
    }

    /**
     * Tests list of ChildPresence
     */
    public function testList()
    {
        //Tests for a day
        $this->clientAuthenticated->request('GET', '/child/presence/list/2019-01-20');
        $response = $this->clientAuthenticated->getResponse();
        $content = $this->assertJsonResponse($response, 200);
        $this->assertInternalType('array', $content);
        $first = $content[0];
        $this->assertArrayHasKey('childPresenceId', $first);

        //Tests for a month
        $this->clientAuthenticated->request('GET', '/child/presence/list/2019-01');
        $response = $this->clientAuthenticated->getResponse();
        $content = $this->assertJsonResponse($response, 200);
        $this->assertInternalType('array', $content);
        $first = $content[0];
        $this->assertArrayHasKey('childPresenceId', $first);
    }

    /**
     * Tests delete ChildPresence AND physically deletes it
     */
    public function testDelete()
    {
        //Tests by id
        $data = array(
            'child' => '1',
            'date' => '2019-01-20',
            'start' => '08:00:00',
            'end' => '12:00:00',
        );
        self::$objectId = $this->em->getRepository('App:ChildPresence')->findByData($data)->getChildPresenceId();
        $this->clientAuthenticated->request('DELETE', '/child/presence/delete/' . self::$objectId);
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);

        //Tests by registrationId
        $this->clientAuthenticated->request('DELETE', '/child/presence/delete-registration/1');
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);

        //Tests by array of id
        $data = array(
            array(
                'child' => '1',
                'date' => '2019-01-20',
                'start' => '08:00:00',
                'end' => '12:00:00',
            ),
            array(
                'child' => '1',
                'date' => '2019-01-20',
                'start' => '13:00:00',
                'end' => '17:00:00',
            ),
        );
        $this->clientAuthenticated->request(
            'DELETE',
            '/child/presence/delete',
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            json_encode($data)
        );
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);

        //Deletes physically the entities created by test
        $this->deleteEntity('ChildPresence', 'childPresenceId', self::$objectId);
        $this->deleteEntity('ChildPresence', 'childPresenceId', self::$objectId + 1);
    }
}

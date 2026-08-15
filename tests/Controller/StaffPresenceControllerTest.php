<?php

namespace App\Tests\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\StaffPresence;
use App\Tests\TestTrait;

class StaffPresenceControllerTest extends WebTestCase
{
    use TestTrait;

    /**
     * Tests creation StaffPresence
     */
    public function testCreate()
    {
        $this->clientAuthenticated->request(
            'POST',
            '/staff/presence/create',
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '[{"staff": "1", "date": "2019-01-20", "start": "08:00:00", "end": "12:00:00"}, {"staff": "1", "date": "2019-01-20", "start": "13:00:00", "end": "17:00:00"}]'
        );
        $response = $this->clientAuthenticated->getResponse();
        $content = $this->assertJsonResponse($response, 200);
    }

    /**
     * Tests display StaffPresence
     */
    public function testDisplay()
    {
        //Tests for a staff for a day
        $this->clientAuthenticated->request('GET', '/staff/presence/display/1/2019-01-20');
        $response = $this->clientAuthenticated->getResponse();
        $content = $this->assertJsonResponse($response, 200);
        $this->assertInternalType('array', $content);
        $first = $content[0];
        $this->assertArrayHasKey('staffPresenceId', $first);

        //Tests for a staff for a month
        $this->clientAuthenticated->request('GET', '/staff/presence/display/1/2019-01');
        $response = $this->clientAuthenticated->getResponse();
        $content = $this->assertJsonResponse($response, 200);
        $this->assertInternalType('array', $content);
        $first = $content[0];
        $this->assertArrayHasKey('staffPresenceId', $first);

        //Tests for a staff for a year
        $this->clientAuthenticated->request('GET', '/staff/presence/display/1/2019');
        $response = $this->clientAuthenticated->getResponse();
        $content = $this->assertJsonResponse($response, 200);
        $this->assertInternalType('array', $content);
        $first = $content[0];
        $this->assertArrayHasKey('staffPresenceId', $first);

        //Tests for a staff for all
        $this->clientAuthenticated->request('GET', '/staff/presence/display/1');
        $response = $this->clientAuthenticated->getResponse();
        $content = $this->assertJsonResponse($response, 200);
        $this->assertInternalType('array', $content);
        $first = $content[0];
        $this->assertArrayHasKey('staffPresenceId', $first);
    }

    /**
     * Tests list of StaffPresence
     */
    public function testList()
    {
        //Tests for a day
        $this->clientAuthenticated->request('GET', '/staff/presence/list/driver/2019-01-20');
        $response = $this->clientAuthenticated->getResponse();
        $content = $this->assertJsonResponse($response, 200);
        $this->assertInternalType('array', $content);
        $first = $content[0];
        $this->assertArrayHasKey('staffPresenceId', $first);

        //Tests for a month
        $this->clientAuthenticated->request('GET', '/staff/presence/list/driver/2019-01');
        $response = $this->clientAuthenticated->getResponse();
        $content = $this->assertJsonResponse($response, 200);
        $this->assertInternalType('array', $content);
        $first = $content[0];
        $this->assertArrayHasKey('staffPresenceId', $first);

        //Tests for totals
        $this->clientAuthenticated->request('GET', '/staff/presence/total/1');
        $response = $this->clientAuthenticated->getResponse();
        $content = $this->assertJsonResponse($response, 200);
        $this->assertInternalType('array', $content);
        $first = $content[0];
        $this->assertArrayHasKey('staffId', $first);
    }

    /**
     * Tests delete StaffPresence AND physically deletes it
     */
    public function testDelete()
    {
        //Tests by id
        $data = array(
            'staff' => '1',
            'date' => '2019-01-20',
            'start' => '08:00:00',
            'end' => '12:00:00',
        );
        self::$objectId = $this->em->getRepository('App:StaffPresence')->findByData($data)->getStaffPresenceId();
        $this->clientAuthenticated->request('DELETE', '/staff/presence/delete/' . self::$objectId);
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);

        //Test by array of id
        $data = array(
            array(
                'staff' => '1',
                'date' => '2019-01-20',
                'start' => '08:00:00',
                'end' => '12:00:00',
            ),
            array(
                'staff' => '1',
                'date' => '2019-01-20',
                'start' => '13:00:00',
                'end' => '17:00:00',
            ),
        );
        $this->clientAuthenticated->request(
            'DELETE',
            '/staff/presence/delete',
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            json_encode($data)
        );
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);

        //Deletes physically the entities created by test
        $this->deleteEntity('StaffPresence', 'staffPresenceId', self::$objectId);
        $this->deleteEntity('StaffPresence', 'staffPresenceId', self::$objectId + 1);
    }
}

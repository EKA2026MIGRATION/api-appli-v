<?php

namespace App\Tests\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\Mail;
use App\Tests\TestTrait;

class MailControllerTest extends WebTestCase
{
    use TestTrait;

    /**
     * Tests creation Mail
     */
    public function testCreate()
    {
        $this->clientAuthenticated->request(
            'POST',
            '/mail/create',
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"title": "Titre", "content": "Contenu du mail"}'
        );
        $response = $this->clientAuthenticated->getResponse();
        $content = $this->assertJsonResponse($response, 200);
        $this->assertArrayHasKey('mailId', $content['mail']);

        self::$objectId = $content['mail']['mailId'];
    }

    /**
     * Tests display Mail
     */
    public function testDisplay()
    {
        $this->clientAuthenticated->request('GET', '/mail/display/' . self::$objectId);
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);
    }

    /**
     * Tests modify Mail
     */
    public function testModify()
    {
        //Tests with full data array
        $this->clientAuthenticated->request(
            'PUT',
            '/mail/modify/' . self::$objectId,
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"title": "Titre modifié", "content": "Contenu du mail modifié"}'
        );
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);
    }

    /**
     * Tests list of Mail
     */
    public function testList()
    {
        $this->clientAuthenticated->request('GET', '/mail/list');
        $response = $this->clientAuthenticated->getResponse();
        $content = $this->assertJsonResponse($response, 200);
        $this->assertInternalType('array', $content);
        $first = $content[0];
        $this->assertArrayHasKey('mailId', $first);
    }

    /**
     * Tests search of Child
     */
    public function testSearch()
    {
        $this->clientAuthenticated->request('GET', '/mail/search/titr');
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);
    }

    /**
     * Tests delete Mail AND physically deletes it
     */
    public function testDelete()
    {
        $this->clientAuthenticated->request('DELETE', '/mail/delete/' . self::$objectId);
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);

        //Deletes physically the entity created by test
        $this->deleteEntity('Mail', 'mailId', self::$objectId);
    }
}

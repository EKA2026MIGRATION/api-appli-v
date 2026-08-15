<?php

namespace App\Tests\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\Blog;
use App\Tests\TestTrait;

class BlogControllerTest extends WebTestCase
{
    use TestTrait;

    /**
     * Tests creation Blog
     */
    public function testCreate()
    {
        $this->clientAuthenticated->request(
            'POST',
            '/blog/create',
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"title": "Titre", "content": "Contenu du blog", "photo": "url_de_la_photo", "author": "Auteur du post"}'
        );
        $response = $this->clientAuthenticated->getResponse();
        $content = $this->assertJsonResponse($response, 200);
        $this->assertArrayHasKey('blogId', $content['blog']);

        self::$objectId = $content['blog']['blogId'];
    }

    /**
     * Tests display Blog
     */
    public function testDisplay()
    {
        $this->clientAuthenticated->request('GET', '/blog/display/' . self::$objectId);
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);
    }

    /**
     * Tests modify Blog
     */
    public function testModify()
    {
        //Tests with full data array
        $this->clientAuthenticated->request(
            'PUT',
            '/blog/modify/' . self::$objectId,
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"title": "Titre modifié", "content": "Contenu du blog modifié", "photo": "url_de_la_photo", "author": "Auteur du post"}'
        );
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);
    }

    /**
     * Tests list of Blog
     */
    public function testList()
    {
        $this->clientAuthenticated->request('GET', '/blog/list');
        $response = $this->clientAuthenticated->getResponse();
        $content = $this->assertJsonResponse($response, 200);
        $this->assertInternalType('array', $content);
        $first = $content[0];
        $this->assertArrayHasKey('blogId', $first);
    }

    /**
     * Tests search of Child
     */
    public function testSearch()
    {
        $this->clientAuthenticated->request('GET', '/blog/search/titr');
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);
    }

    /**
     * Tests delete Blog AND physically deletes it
     */
    public function testDelete()
    {
        $this->clientAuthenticated->request('DELETE', '/blog/delete/' . self::$objectId);
        $response = $this->clientAuthenticated->getResponse();
        $this->assertJsonResponse($response, 200);

        //Deletes physically the entity created by test
        $this->deleteEntity('Blog', 'blogId', self::$objectId);
    }
}

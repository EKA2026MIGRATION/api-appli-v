<?php

namespace App\Tests\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Tests\TestTrait;

class HomeControllerTest extends WebTestCase
{
    use TestTrait;

    /**
     * Tests home
     */
    public function testHome()
    {
        $this->clientAuthenticated->request('GET', '/');
        $response = $this->clientAuthenticated->getResponse();
        $content = $this->assertJsonResponse($response, 200);
    }
}

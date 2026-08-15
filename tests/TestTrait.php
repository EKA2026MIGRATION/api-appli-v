<?php

namespace App\Tests;

trait TestTrait
{
    private static $objectId;
    private $clientAuthenticated;
    private $em;

    public function setUp(): void
    {
        $this->clientAuthenticated = $this->createAuthenticatedClient($_ENV['ADMIN_USER'], $_ENV['ADMIN_PASSWORD']);

        $this->em = self::getContainer()
            ->get('doctrine')
            ->getManager();
    }

    /***
     * Creates the authenticated client
     */
    public function createAuthenticatedClient($user, $password)
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/user/api/authenticate',
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"username": "' . $user . '", "password": "' . $password . '"}'
        );
        $data = json_decode($client->getResponse()->getContent(), true);

        $client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $data['token']));

        return $client;
    }

    /**
     * Asserts that a Response is in json
     */
    public function assertJsonResponse($response, int $statusCode = 200)
    {
        $this->assertEquals(
            $statusCode,
            $response->getStatusCode()
        );

        $this->assertTrue(
            $response->headers->contains('Content-Type', 'application/json'),
            $response->headers
        );
        $content = json_decode($response->getContent(), true, 50);

        return $content;
    }

    /**
     * Deletes physically the created entity in the db
     */
    private function deleteEntity($class, $idName, $objectId)
    {
        $object = $this->em->getRepository('App:' . $class)->find(array($idName => $objectId));

        $this->em->remove($object);
        $this->em->flush();
    }
}

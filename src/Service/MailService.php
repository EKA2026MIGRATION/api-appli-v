<?php

namespace App\Service;

use App\Entity\Mail;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

/**
 * MailService class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class MailService implements MailServiceInterface
{
    private $em;

    private $mainService;

    public function __construct(
        EntityManagerInterface $em,
        MainServiceInterface $mainService
    )
    {
        $this->em = $em;
        $this->mainService = $mainService;
    }

    /**
     * {@inheritdoc}
     */
    public function create(string $data)
    {
        //Submits data
        $object = new Mail();
        $this->mainService->create($object);
        $data = $this->mainService->submit($object, 'mail-create', $data);

        //Checks if entity has been filled
        $this->isEntityFilled($object);

        //Persists data
        $this->mainService->persist($object);

        //Returns data
        return array(
            'status' => true,
            'message' => 'Mail ajouté',
            'mail' => $this->toArray($object),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function delete(Mail $object)
    {
        //Persists data
        $this->mainService->delete($object);
        $this->mainService->persist($object);

        return array(
            'status' => true,
            'message' => 'Mail supprimé',
        );
    }

    /**
     * Returns the list of all families in the array format
     * @return array
     */
    public function findAll()
    {
        return $this->em
            ->getRepository('App\Entity\Mail')
            ->findBy(['suppressed' => 0], ['subjectFr' => 'ASC'])
        ;
    }

    /**
     * Searches the term in the Mail collection
     * @return array
     */
    public function findAllSearch(string $term)
    {
        return $this->em
            ->getRepository('App\Entity\Mail')
            ->findAllSearch($term)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function isEntityFilled(Mail $object)
    {
        if (null === $object->getSubjectFr() ||
            null === $object->getContentFr()) {
            throw new UnprocessableEntityHttpException('Missing data for Mail -> ' . json_encode($object->toArray()));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function modify(Mail $object, string $data)
    {
        //Submits data
        $data = $this->mainService->submit($object, 'mail-modify', $data);

        //Checks if entity has been filled
        $this->isEntityFilled($object);

        //Persists data
        $this->mainService->modify($object);
        $this->mainService->persist($object);

        //Returns data
        return array(
            'status' => true,
            'message' => 'Mail modifié',
            'mail' => $this->toArray($object),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(Mail $object)
    {
        //Main data
        $objectArray = $this->mainService->toArray($object->toArray());

        return $objectArray;
    }
}

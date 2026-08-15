<?php

namespace App\Service;

use App\Entity\Person;
use App\Entity\PersonPhoneLink;
use App\Entity\Phone;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

/**
 * PhoneService class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class PhoneService implements PhoneServiceInterface
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
     * Adds link between Person and Phone
     */
    public function addLink(int $personId, Phone $object)
    {
        $person = $this->em->getRepository('App\Entity\Person')->findOneById($personId);
        if ($person instanceof Person && !$person->getSuppressed()) {
            $personPhoneLink = new PersonPhoneLink();
            $personPhoneLink
                ->setPerson($person)
                ->setPhone($object)
            ;
            $this->em->persist($personPhoneLink);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function create(string $data)
    {
        //Submits data
        $object = new Phone();
        $this->mainService->create($object);
        $data = $this->mainService->submit($object, 'phone-create', $data);

        //Checks if entity has been filled
        $this->isEntityFilled($object);

        //Adds links from person/s to phone
        if (array_key_exists('links', $data)) {
            $links = $data['links'];
            if (null !== $links && is_array($links) && !empty($links)) {
                $this->addLink((int) $links['personId'], $object);
            }
        }

        //Persists data
        $this->mainService->persist($object);

        //Returns data
        return array(
            'status' => true,
            'message' => 'Téléphone ajouté',
            'phone' => $this->toArray($object),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function delete(Phone $object)
    {
        //Removes links from person/s to phone
        if (!$object->getPersons()->isEmpty()) {
            foreach ($object->getPersons() as $link) {
                $personPhoneLink = $this->em->getRepository('App\Entity\PersonPhoneLink')->findOneBy(array('person' => $link->getPerson(), 'phone' => $object));
                $this->em->remove($personPhoneLink);
            }
        }

        //Persists data
        $this->mainService->delete($object);
        $this->mainService->persist($object);

        return array(
            'status' => true,
            'message' => 'Téléphone supprimé',
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isEntityFilled(Phone $object)
    {
        if (null === $object->getName() ||
            null === $object->getPhone()) {
            throw new UnprocessableEntityHttpException('Missing data for Phone -> ' . json_encode($object->toArray()));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function modify(Phone $object, string $data)
    {
        //Submits data
        $data = $this->mainService->submit($object, 'phone-modify', $data);

        //Checks if entity has been filled
        $this->isEntityFilled($object);

        //Persists data
        $this->mainService->modify($object);
        $this->mainService->persist($object);

        //Returns data
        return array(
            'status' => true,
            'message' => 'Téléphone modifié',
            'phone' => $this->toArray($object),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(Phone $object)
    {
        //Main data
        $objectArray = $this->mainService->toArray($object->toArray());

        //Gets related persons
        if (null !== $object->getPersons()) {
            $persons = array();
            foreach($object->getPersons() as $personLink) {
                if (!$personLink->getPerson()->getSuppressed()) {
                    $persons[] = $this->mainService->toArray($personLink->getPerson()->toArray());
                }
            }
            $objectArray['persons'] = $persons;
        }

        return $objectArray;
    }

    public function updateIsPrefered($phone, $type) {
        $currentIsPrefered = array_map('trim', explode(',', $phone->getIsPrefered()));
        // is i need to add
        if(!in_array($type, $currentIsPrefered)) {
            $currentIsPrefered[] = $type;
            $action = "add";
        } else {
            $key = array_search($type, $currentIsPrefered);
            unset($currentIsPrefered[$key]);
            $action = "delete";
        }

        $phone->setIsPrefered(implode(',' , $currentIsPrefered));

        $this->mainService->persist($phone);

        return ['phoneId' => $phone->getPhoneId(), 'action' => $action];

    }
}

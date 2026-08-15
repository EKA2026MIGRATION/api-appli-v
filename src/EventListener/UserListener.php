<?php

namespace App\EventListener;

use App\Service\MainServiceInterface;
use App\Service\PersonServiceInterface;
use c975L\UserBundle\Event\UserEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

class UserListener implements EventSubscriberInterface
{
    private $mainService;

    private $personService;

    public function __construct(
        MainServiceInterface $mainService,
        PersonServiceInterface $personService
    )
    {
        $this->mainService = $mainService;
        $this->personService = $personService;
    }

    public static function getSubscribedEvents()
    {
        return array(
            UserEvent::API_USER_DELETE => 'userApiDelete',
            UserEvent::API_USER_EXPORT => 'userApiExport',
        );
    }

    /**
     * Deletes User and related entities
     */
    public function userApiDelete($event)
    {
        $user = $event->getUser();
        if ($user instanceof UserInterface) {
            //Marks the related Person + Entities as deleted BUT deletes the links
            $personLink = $user->getUserPersonLink();
            if (null !== $personLink) {
                $this->personService->delete($personLink->getPerson());
            }

            //Marks the User as deleted
            $user
                ->setUserPersonLink(null)
                ->setEnabled(false)
                ->setAllowUse(false)
            ;
            $this->mainService->delete($user);
            $this->mainService->persist($user);
        }

        $event->stopPropagation();
    }

    /**
     * Sets the data to be exported for the user
     */
    public function userApiExport($event)
    {
        $response = null;
        $user = $event->getUser();
        if ($user instanceof UserInterface) {
            $personLink = $user->getUserPersonLink();
            if (null !== $personLink) {
                //Gets the Person
                $person = $personLink->getPerson();
                $personToArray = $this->personService->toArray($person);

                //Removes uneeded data
                foreach ($this->getRemovedData() as $data) {
                    unset($personToArray[$data]);
                }
                $uneededData = array(
                    'addresses',
                    'children',
                    'phones',
                    'relations',
                );
                foreach ($uneededData as $data) {
                    if (array_key_exists($data, $personToArray)) {
                        $method = 'clean' . ucfirst($data);
                        if (method_exists($this, $method)) {
                            $personToArray[$data] = $this->$method($personToArray[$data]);
                        }
                    }
                }

                $response = new Response(json_encode($personToArray));
                $response->headers->set('Content-Type', 'application/json');
            }
        }

        $event
            ->setResponse($response)
            ->stopPropagation()
        ;
    }

    /**
     * Cleans uneeded data for addresses
     */
    public function cleanAddresses($addresses)
    {
        foreach ($addresses as $key => $value) {
            unset($addresses[$key]['addressId']);
            unset($addresses[$key]['persons']);
            foreach ($this->getRemovedData() as $data) {
                unset($addresses[$key][$data]);
            }
        }

        return $addresses;
    }

    /**
     * Cleans uneeded data for children
     */
    public function cleanChildren($children)
    {
        foreach ($children as $key => $value) {
            unset($children[$key]['childId']);
            unset($children[$key]['persons']);
            unset($children[$key]['siblings']);
            foreach ($this->getRemovedData() as $data) {
                unset($children[$key][$data]);
            }
        }

        return $children;
    }

    /**
     * Cleans uneeded data for phones
     */
    public function cleanPhones($phones)
    {
        foreach ($phones as $key => $value) {
            unset($phones[$key]['phoneId']);
            unset($phones[$key]['persons']);
            foreach ($this->getRemovedData() as $data) {
                unset($phones[$key][$data]);
            }
        }

        return $phones;
    }

    /**
     * Cleans uneeded data for relations
     */
    public function cleanRelations($relations)
    {
        foreach ($relations as $key => $value) {
            unset($relations[$key]['staff']);
            unset($relations[$key]['relations']);
            unset($relations[$key]['userPersonLink']);
            foreach ($this->getRemovedData() as $data) {
                unset($relations[$key][$data]);
            }

            //Removes uneeded data set for each relation
            $uneededData = array(
                'addresses',
                'children',
                'phones',
            );
            foreach ($uneededData as $data) {
                if (isset($relations[$key][$data])) {
                    $method = 'clean' . ucfirst($data);
                    if (method_exists($this, $method)) {
                        $relations[$key][$data] = $this->$method($relations[$key][$data]);
                    }
                }
            }
        }

        return $relations;
    }

    /*
     * Returns data that should not be displayed
     */
    public function getRemovedData()
    {
        return array(
            'createdAt',
            'createdBy',
            'personId',
            'updatedAt',
            'updatedBy',
            'suppressed',
            'suppressedAt',
            'suppressedBy',
        );
    }
}

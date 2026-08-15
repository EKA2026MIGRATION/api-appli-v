<?php

namespace App\Service;

use App\Form\AppFormFactoryInterface;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use LogicException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Security;

/**
 * MainService class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class MainService implements MainServiceInterface
{
    private $em;

    private $formFactory;

    private $security;

    private $user;

    public function __construct(
        EntityManagerInterface $em,
        AppFormFactoryInterface $formFactory,
        Security $security,
        TokenStorageInterface $tokenStorage
    )
    {
        $this->em = $em;
        $this->formFactory = $formFactory;
        $this->security = $security;
        $token = $tokenStorage->getToken();
        $this->user = null !== $token ? $token->getUser() : null;
    }

    /**
     * Adds the gps coordinates + postal to object
     */
    public function addCoordinates($object)
    {
        //Gets data from API
        $address = strtolower(str_replace(array('   ', '  ', ' ', '+-+', ',', '++'), '+', $object->getAddressGeocoding()));
        $url = 'https://api-adresse.data.gouv.fr/search/?autocomplete=0&type=street&limit=1&q=' . $address;
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_URL, $url);
        $result = curl_exec($curl);
        curl_close($curl);
        $coordinatesJson = json_decode($result, true, 10);
        $latitude = $coordinatesJson['features'][0]['geometry']['coordinates'][1] ?? null;
        $longitude = $coordinatesJson['features'][0]['geometry']['coordinates'][0] ?? null;

        //Updates object
        if (null !== $latitude) {
            $object
                ->setLatitude($latitude)
                ->setLongitude($longitude)
            ;
            if (null === $object->getPostal() || 5 != strlen($object->getPostal())) {
                $object->setPostal($coordinatesJson['features'][0]['properties']['postcode'] ?? $object->getPostal());
            }

            return true;
        }

        return false;
    }

    /**
     * Checks if DateStart is a monday and changes to next monday if not
     * @return DateTime
     */
    public function checkDateStartIsMonday($object)
    {
        $dateStart = $object->getDateStart();
        if (1 !== (int) $dateStart->format('N')) {
            $dateStart = new DateTime($dateStart->format('Y-m-d') . ' next Monday');
            $object->setDateStart($dateStart);
        }

        return $dateStart;
    }

    /**
     * {@inheritdoc}
     */
    public function create($object, $user = null)
    {

        if($this->user != "anon.") {
          $userId = null !== $user ? $user->getId() : $this->user->getId();
        } else {
          $userId = 99;
        }

        $object
            ->setCreatedAt(new DateTime())
            ->setCreatedBy($userId)
            ->setUpdatedAt(new DateTime())
            ->setUpdatedBy($userId)
        ;
        if(method_exists($object, "setSuppressed")) {
          $object->setSuppressed(false);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function delete($object)
    {
        (!isset($this->user)) ? $id = 99 : $id = $this->user->getId();
        $object
            ->setSuppressed(true)
            ->setSuppressedAt(new DateTime())
            ->setSuppressedBy($id)
        ;
    }

    /**
     * Returns the user
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * {@inheritdoc}
     */
    public function modify($object)
    {
      if($this->user != "anon.") {
        $userId = $this->user->getId();
      } else {
        $userId = 99;
      }


        $object
            ->setUpdatedAt(new DateTime())
            ->setUpdatedBy($userId)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function persist($object)
    {
        $this->em->persist($object);
        $this->em->flush();
        $this->em->refresh($object);
    }

    /**
     * {@inheritdoc}
     */
    public function submit($object, $formName, $data)
    {
        $dataArray = is_array($data) ? $data : json_decode($data, true);

        //Bad array
        if (null !== $data && !is_array($dataArray)) {
            throw new UnprocessableEntityHttpException('Submitted data is not an array -> ' . $data);
        }

        //Submits form
        $form = $this->formFactory->create($formName, $object);
        $form->submit($dataArray, false);

        //Gets errors
        $errors = $form->getErrors();
        foreach ($errors as $error) {
            throw new LogicException('Error ' . get_class($error->getCause()) . ' --> ' . $error->getMessageTemplate() . ' ' . json_encode($error->getMessageParameters()));
        }

        //Sets fields to null
        if (is_array($dataArray)) {
            foreach ($dataArray as $key => $value) {
                if (null === $value || 'null' === $value) {
                    $method = 'set' . ucfirst($key);
                    if (method_exists($object, $method)) {
                        $object->$method(null);
                    }
                }
            }
        }

        return $dataArray;
    }

    /**
     * {@inheritdoc}
     */
    public function hydrate($object, $data)
    {
        foreach ($data as $key => $value)
        {
            $elements = explode('_', $key);
            $new_key = '';
            foreach ($elements as $element)
            {
                $new_key .= ucfirst($element);
            }
            $method = 'set'.$new_key;
            if (method_exists($object, $method))
            {
                $object->$method($value);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function toArray($objectArray)
    {
        //Main dates
        $dates = array(
            'createdAt',
            'updatedAt',
            'suppressedAt',
        );
        foreach ($dates as $date) {
            if (null !== $objectArray[$date]) {
                $objectArray[$date] = $objectArray[$date]->format('Y-m-d H:i:s');
            }
        }

        $globalData = array();

        //Global data
        $globalData = array(
            '__initializer__',
            '__cloner__',
            '__isInitialized__',
        );

        //User's role linked data
        $specificData = array();
        if (!$this->security->isGranted('ROLE_ADMIN')) {
            $specificData = array_merge(
                $specificData,
                array(
                  //  'createdAt',
                    'createdBy',
                    //'updatedAt',
                    'updatedBy',
                    'suppressed',
                    'suppressedAt',
                    'suppressedBy',
                )
            );
        }

        if ($this->security->isGranted('ROLE_TRAINEE') || $this->security->isGranted('ROLE_COACH')) {
            $specificData = array_merge(
                $specificData,
                array(
                    'addresses',
                )
            );

        }

        //Deletes unwanted data
        foreach (array_merge($globalData, $specificData) as $unsetData) {
            unset($objectArray[$unsetData]);
        }

        return $objectArray;
    }
}

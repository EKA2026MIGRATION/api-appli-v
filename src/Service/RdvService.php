<?php

namespace App\Service;

use App\Entity\Rdv;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\StaffServiceInterface;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

/**
 * RdvService class
 * @author Sandy Razafitrimo <sandy@etsik.com>
 */
class RdvService implements RdvServiceInterface
{
    private $em;

    private $mainService;
    private $staffService;


    public function __construct(
        EntityManagerInterface $em,
        MainServiceInterface $mainService,
        StaffServiceInterface $staffService

    )
    {
        $this->em = $em;
        $this->mainService = $mainService;
        $this->staffService = $staffService;

    }


    public function findByDate($date)
    {

        $rdvs = $this->em->getRepository('App\Entity\Rdv')->findByDate($date);
        return $rdvs;

    }

    /**
     * {@inheritdoc}
     */
    public function toArray(Rdv $object)
    {
        //Main data
        $objectArray = $this->mainService->toArray($object->toArray());

        //Gets related staff
        if (null !== $object->getStaff() && !$object->getStaff()->getSuppressed()) {
            $objectArray['staff'] = $this->staffService->toArray($object->getStaff());
        }

        return $objectArray;
    }


    /**
     * {@inheritdoc}
     */
    public function create(string $data)
    {
        $values = json_decode($data, true);

        $name        = $values['name'];
        $staff_id    = $values['staff_id'];
        $description = $values['description'];
        $date        = $values['date'];

        if (is_array($values) && !empty($values)) {

            ($staff_id) ? $staff = $this->em->getRepository('App\Entity\Staff')->find($staff_id) : $staff = null;

            $dateRdv = new DateTime($date);

            $object = new Rdv();
            $object->setName($name);
            $object->setDescription($description);
            $object->setStaff($staff);
            $object->setDateRdv($dateRdv);
            $object->setStep('SCHEDULED');

            $this->mainService->create($object);

            //Persists data
            $this->mainService->persist($object);

            $rdvArray = $object->toArray();

            //Returns data
            return array(
                'status' => true,
                'message' => 'Rdv ajouté',
                'rdv'     => $rdvArray
            );
        }

        throw new UnprocessableEntityHttpException('Submitted data is not an array -> ' . json_encode($data));
    }


}

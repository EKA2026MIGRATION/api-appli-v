<?php

namespace App\Service;

use App\Entity\Vehicle;
use App\Entity\VehicleItem;
use App\Entity\VehicleCheckup;
use App\Entity\VehicleCheckupItem;

use App\Entity\Staff;
use DateTime;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

/**
 * VehicleItemService class
 * @author Sandy Razafitrimo <sandy@etsik.com>
 */
class VehicleItemService implements VehicleItemServiceInterface
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

    public function list()
    {
        $items = $this->em->getRepository('App\Entity\VehicleItem')->findBy(['suppressed' => 0], ['name' => 'asc']);
        foreach($items as $item)
        {
            $result[] = $item->toArray();
        }
        return $result;
    }


    public function validCheckup(string $data)
    {

        $data = json_decode($data, true);

        $staff = $this->em->getRepository('App\Entity\Staff')->find($data['staff']);

        $vehicle = $this->em->getRepository('App\Entity\Vehicle')->find($data['vehicle_id']);
        if($staff == null || $vehicle == null) {
        return ["message" => "Staff et/ou véhicule non trouvé"];
        }

        (isset($data['photo'])) ? $link = $data['photo'] : $link = null;

        // create checkup or update checkup if the same day
        //if(!$checkup = $this->em->getRepository('App\Entity\VehicleCheckup')->findByDateStaffVehicle($data['date_checkup'], $staff, $vehicle)) {
            $checkup = new VehicleCheckup();
       /* } else {
            foreach($checkup->getItems() as $linkItem) {
                $checkup->removeItem($linkItem);
                $this->em->flush();
            }

        }*/
        
        $checkup->setStaff($staff);
        $checkup->setVehicle($vehicle);
        $checkup->setDateCheckup($data['date_checkup']);
        $checkup->setComment($data['comment']);
        $checkup->setIsOk($data['is_ok']);
        $checkup->setPhoto($link);

        $this->mainService->create($checkup);
        $this->mainService->persist($checkup);

        foreach($data['items'] as $constant_key => $value)
        {
            if($item = $this->em->getRepository('App\Entity\VehicleItem')->findOneBy(['constantKey' => array_key_first($value)])) {
                $object = new VehicleCheckupItem();
                $object->setCheckup($checkup);
                $object->setItem($item);
                $object->setIsOk($value[array_key_first($value)]);
                $this->em->persist($object);
                $this->em->flush();

            }
        }
        $this->em->flush();

        //Returns data
    return array(
        'status' => true,
        'message' => 'checkup créé',
        'checkup' => $checkup->toArray("light"),
            );

    }

    public function checkupVehicleList($vehicle_id)
    {
        $vehicle = $this->em->getRepository('App\Entity\Vehicle')->find($vehicle_id);
        $checkups = $this->em->getRepository('App\Entity\VehicleCheckup')->findBy(['vehicle' => $vehicle], ['dateCheckup' => 'desc']);
        $results = [];
        foreach($checkups as $checkup) {
            $results[] = $checkup->toArray("light");
        }
        return array(
            'status' => true,
            'checkups' => $results
                );

    }


    public function vehicleNeedCheckup() {

        // latest checkup
        $currentDate = date('Y-m-d');
        $searchDate  = date('Y-m-d', strtotime($currentDate. ' - 15 days'));


        // list of vehicle
        $vehicles = $this->em->getRepository('App\Entity\Vehicle')->findBy(['suppressed' => 0], ['matriculation' => 'asc']);



        foreach($vehicles as $vehicle) {
            
            // check if chekcup > $searchdate
            $check = $this->em->getRepository('App\Entity\VehicleCheckup')->checkIfExistAfterDate($searchDate, $vehicle);

            if(!$check) {

                $last_check =  $this->em->getRepository('App\Entity\VehicleCheckup')->lastCheckup($vehicle);

                if($last_check) {
                    $latestDate = $last_check->getDateCheckup()->format('Y-m-d');
                } else {
                    $latestDate = "";
                }

                $result[$vehicle->getMatriculation()] = [
                            'latest_checkup' => $latestDate,    
                            'vehicle' => $vehicle->toArray()
                ];

            }
        }

        return $result;


    }




}

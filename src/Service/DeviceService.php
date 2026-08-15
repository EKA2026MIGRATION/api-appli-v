<?php

namespace App\Service;

use App\Entity\Device;
use App\Entity\User;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

/**
 * DeviceService class
 * @author Sandy Razafitrimo <sandy@etsik.com>
 */
class DeviceService implements DeviceServiceInterface
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

    public function addToUser($data)
    {
            $values = json_decode($data, true);
            if(!$user = $this->em->getRepository('App\Entity\User')->find($values['user_id'])) return ['message' => 'user inexistant'];

            if($device = $this->em->getRepository('App\Entity\Device')->findOneByUserAndIdent( $user, $values['device_id'])) {
                return [
                                'message' => 'device déjà existant - déjà associé à ce user',
                                'device' => $device->toArray()
                        ];
            } else {

                $device = new Device();
                $device->setUser($user);
                $device->setIdentifier($values['device_id']);
                $device->setDatas($values['datas']);

                $this->mainService->create($device);

                //Persists data
                $this->mainService->persist($device);

                //Returns data
                return array(
                    'status' => true,
                    'message' => 'Device ajouté et créé' ,
                    'device' => $device->toArray()
                );

            }

    }




    /**
     * {@inheritdoc}
     */
    public function toArray(Device $object)
    {
        //Main data
        $objectArray = $this->mainService->toArray($object->toArray());
        return $objectArray;
    }
}

<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;
use App\Entity\Reminder;

/**
 * ReminderRepository class
 * @author Sandy Razafitrimo
 */
class ReminderRepository extends EntityRepository
{
    
    public function findByStatusVehicle($status, $vehicle)
    {


        $qb = $this->createQueryBuilder('r')->where('1=1');


        if($status != "all") {
            $qb->andwhere('r.status = :status')
            ->setParameter('status', $status);
        }

        if($vehicle) {
            $qb->andwhere('r.vehicle = :vehicle')
            ->setParameter('vehicle', $vehicle);
        }
        return $qb->orderBy('r.dateReminder', 'DESC')
        ->getQuery()
        ->getResult();

    }
}

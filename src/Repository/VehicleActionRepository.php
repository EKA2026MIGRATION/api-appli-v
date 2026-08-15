<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * VehicleFuelRepository class
 * @author Sandy Razafitrimo <sandy@etsik.com>
 */
class VehicleActionRepository extends EntityRepository
{
  /**
   * Returns the vehicle if not suppressed
   */
  public function findBetweenDate($from, $to, $vehicle, $limit)
  {
      $qb = $this->createQueryBuilder('v')->where('1=1');
      if($vehicle) {
        $qb->where('v.vehicle = :vehicle')
           ->setParameter('vehicle', $vehicle);
      }

      $qb->orderBy('v.dateAction', 'DESC')
        ->andwhere('v.dateAction >= :from')
        ->andWhere('v.dateAction <= :to')
        ->setParameter('from', $from)
        ->setParameter('to', $to);

      return $qb->getQuery()->getResult();
  }
}

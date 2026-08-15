<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * VehicleCheckupRepository class
 * @author Sandy Razafitrimo <sandy@etsik.com>
 */
class VehicleCheckupRepository extends EntityRepository
{
  /**
   * Return a checkup by date, staff & vehicle
   */
  public function findByDateStaffVehicle($date, $staff, $vehicle)
  {
      $date = explode(' ', $date);

      return $this->createQueryBuilder('v')
          ->where('v.dateCheckup LIKE :date')
          ->andWhere('v.staff = :staff')
          ->andWhere('v.vehicle = :vehicle')
          ->andWhere('v.suppressed = 0')
          ->setParameter('date', $date[0] . '%')
          ->setParameter('staff', $staff)
          ->setParameter('vehicle', $vehicle)
          ->getQuery()
          ->getOneOrNullResult()
      ;
  }


  public function checkIfExistAfterDate($searchDate, $vehicle) {
    return $this->createQueryBuilder('c')
    ->where('c.dateCheckup > :date')
    ->andWhere('c.vehicle = :vehicle')
    ->orderBy('c.dateCheckup', 'desc')
    ->setParameter('date', $searchDate)
    ->setParameter('vehicle', $vehicle)
    ->setMaxResults(1)
    ->getQuery()
    ->getOneOrNullResult()
;
  }

  public function lastCheckup($vehicle) {
    return $this->createQueryBuilder('c')
    ->andWhere('c.vehicle = :vehicle')
    ->orderBy('c.dateCheckup', 'desc')
    ->setParameter('vehicle', $vehicle)
    ->setMaxResults(1)
    ->getQuery()
    ->getOneOrNullResult()
;
  }
}

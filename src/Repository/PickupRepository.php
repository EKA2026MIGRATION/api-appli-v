<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;
use App\Entity\Staff;

/**
 * PickupRepository class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class PickupRepository extends EntityRepository
{
    /**
     * Counts all the pickups that are not affected to a ride by postal
     */
    public function countAllUnaffected($date, $kind)
    {
        return $this->createQueryBuilder('p')
            ->select('p.postal, COUNT(p.postal) as countPostal')
            ->where('p.start LIKE :date')
            ->andWhere('p.kind = :kind')
            ->andWhere('p.ride IS NULL')
            ->andWhere('p.suppressed = 0')
            ->groupBy('p.postal')
            ->distinct()
            ->setParameter('date', $date . '%')
            ->setParameter('kind', $kind)
            ->orderBy('countPostal', 'DESC')
            ->addOrderBy('p.postal', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Returns all the pickups by date
     */
    public function findAllByDate($date, $kind)
    {
        return $this->createQueryBuilder('p')
            ->where('p.start LIKE :date')
            ->andWhere('p.kind = :kind')
            ->andWhere('p.suppressed = 0')
            ->setParameter('date', $date . '%')
            ->setParameter('kind', $kind)
            ->orderBy('p.postal', 'ASC')
            ->addOrderBy('p.start', 'ASC')
            ->addOrderBy('p.address', 'ASC')
            ->addOrderBy('p.pickupId', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }


    /**
     * Returns all the pickups by date
     */
    public function findByRegistrationAndDate($date, $registrationId)
    {
        return $this->createQueryBuilder('p')
            ->where('p.start LIKE :date')
            ->andWhere('p.registration = :registrationId')
            ->andWhere('p.suppressed = 0')
            ->setParameter('date', $date . '%')
            ->setParameter('registrationId', $registrationId)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Returns all the pickups by status
     */
    public function findOneByStaffAndDate(Staff $staff, $date, $which)
    {
        if($which == "first") $kind = "dropin";
        if($which == "last") $kind = "dropoff";

        $qb = $this->createQueryBuilder('p')
            ->leftJoin('p.ride', 'r')
            ->where('r.staff = :staff')
            ->andWhere('p.start LIKE :date')
            ->andWhere("p.status = 'PEC' or p.status = 'pec' ")
            ->andWhere("p.statusChange is not null or p.statusChange <> '' ")

            ->andWhere('p.kind = :kind');

        if($which == 'first') {
          $qb->orderBy('p.statusChange', 'ASC');
        }
        if($which == 'last') {
          $qb->orderBy('p.statusChange', 'DESC');
        }

        $qb ->setParameter('date', $date . '%')
           ->setParameter('kind', $kind)
           ->setParameter('staff', $staff )
           ->setMaxResults(1);

        return $qb
          ->getQuery()
          ->getOneOrNullResult();
    }




    /**
     * Returns all the pickups by status
     */
    public function findAllByStatus($date, $status)
    {
        $statusCondition = 'null' === $status ? 'p.status IS NULL' : 'p.status = :status';

        $qb = $this->createQueryBuilder('p')
            ->where($statusCondition)
            ->andWhere('p.start LIKE :date')
            ->andWhere('p.suppressed = 0')
            ->andWhere('p.ride IS NOT NULL')
            ->orderBy('p.ride', 'ASC')
            ->addOrderBy('p.sortOrder', 'ASC')
            ->setParameter('date', $date . '%')
        ;

        if ('null' !== $status) {
            $qb->setParameter('status', $status);
        }

        return $qb
            ->getQuery()
            ->getResult()
        ;
    }

    public function findByChildAndFromToDate($child, $from, $to) {
        return $this->createQueryBuilder('p')
            ->where('p.child = :child')
            ->andWhere('p.start > :from')
            ->andWhere('p.start < :to')
            ->andWhere('p.suppressed = 0')
            //->andWhere('p.kind = :kind')
            ->setParameter('from', $from)
            ->setParameter('to', $to)
            ->setParameter('child', $child)
            //->setParameter('kind', $kind)
            ->orderBy('p.start')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findByChildAndDate($child, $date) {
        return $this->createQueryBuilder('p')
            ->where('p.child = :child')
            ->andWhere('p.start like :date')
            ->andWhere('p.suppressed = 0')
            //->andWhere('p.kind = :kind')
            ->setParameter('date', $date . '%')
            ->setParameter('child', $child)
            //->setParameter('kind', $kind)
            ->orderBy('p.start')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Returns all the pickups that are not affected to a ride
     */
    public function findAllUnaffected($date, $kind)
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.child', 'c')
            ->where('p.start LIKE :date')
            ->andWhere('p.kind = :kind')
            ->andWhere('p.ride IS NULL')
            ->andWhere('p.suppressed = 0')
            ->setParameter('date', $date . '%')
            ->setParameter('kind', $kind)
            ->orderBy('c.lastname')
            ->addOrderBy('p.start', 'ASC')
            ->addOrderBy('p.postal', 'ASC')
            ->addOrderBy('p.address', 'ASC')
            ->addOrderBy('p.pickupId', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Returns all the pickup using the registrationId linked
     */
    public function findByRegistrationId($registrationId)
    {
        return $this->createQueryBuilder('p')
            ->where('p.registration = :registrationId')
            ->setParameter('registrationId', $registrationId)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Returns the pickups to geocode
     */
    public function findGeocode()
    {
        return $this->createQueryBuilder('p')
            ->where('p.suppressed = 0')
            ->andWhere('p.address IS NOT NULL')
            ->andWhere('p.address != :empty')
            ->andWhere('p.latitude IS NULL OR p.longitude IS NULL')
            ->setParameter('empty', '')
            ->orderBy('p.pickupId', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Returns the pickup if not suppressed
     */
    public function findOneById($pickupId)
    {
        return $this->createQueryBuilder('p')
            ->addSelect('c')
            ->leftJoin('p.child', 'c')
            ->where('p.pickupId = :pickupId')
            ->andWhere('p.suppressed = 0')
            ->setParameter('pickupId', $pickupId)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * Returns the pickup that correspond to date, kind and child
     */
    public function findOneByDateKindChild($date, $kind, $child)
    {
        return $this->createQueryBuilder('p')
            ->where('p.start LIKE :date')
            ->andWhere('p.kind = :kind')
            ->andWhere('p.child = :child')
            ->andWhere('p.ride IS NULL')
            ->andWhere('p.suppressed = 0')
            ->setParameter('date', $date . '%')
            ->setParameter('kind', $kind)
            ->setParameter('child', $child)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

     /**
     * Returns the pickup that correspond to date, and child
     */
    public function findAllByChildAndDate($child, $date)
    {
        return $this->createQueryBuilder('p')
            ->where('p.start LIKE :date')
            ->andWhere('p.child = :child')
            ->andWhere('p.suppressed = 0')
            ->setParameter('date', $date . '%')
            ->setParameter('child', $child)
            ->getQuery()
            ->getResult()
        ;
    }
}

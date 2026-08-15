<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * RideRepository class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class RideRepository extends EntityRepository
{
    /**
     * Returns all the rides by date
     */
    public function findAllByDate($date)
    {
        return $this->createQueryBuilder('r')
            ->addSelect('s', 'p', 'v', 'pi', 'z')
            ->leftJoin('r.staff', 's')
            ->leftJoin('s.person', 'p')
            ->leftJoin('s.driverZones', 'z')
            ->leftJoin('r.vehicle', 'v')
            ->leftJoin('r.pickups', 'pi')
            ->where('r.date LIKE :date')
            ->andWhere('r.suppressed = 0')
            ->setParameter('date', $date . '%')
            ->orderBy('r.date', 'ASC')
            ->addOrderBy('r.start', 'ASC')
            ->addOrderBy('pi.sortOrder', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Returns all the rides by date with params
     */
    public function findAllByDateAndParams($date, $kind, $moment)
    {
        if($moment == "am") {
            if($kind == "dropin") $timeRef = $date.' 12:31:00';
            if($kind == "dropoff") $timeRef = $date.' 15:01:00';

        } 
        if($moment == "pm") {
            if($kind == "dropin") $timeRef = $date. ' 12:29:00';
            if($kind == "dropoff") $timeRef = $date. ' 15:00:00';
        }

        $qb = $this->createQueryBuilder('r')
            ->addSelect('s', 'p', 'v', 'pi', 'z')
            ->leftJoin('r.staff', 's')
            ->leftJoin('s.person', 'p')
            ->leftJoin('s.driverZones', 'z')
            ->leftJoin('r.vehicle', 'v')
            ->leftJoin('r.pickups', 'pi')
            ->where('r.date LIKE :date')
            ->andWhere('pi.kind = :kind')
            ->setParameter('kind', $kind);

        if($moment == "am") {
            $qb->andWhere('pi.start < :timeRef');
        } else {
            $qb->andWhere('pi.start > :timeRef');
        }

        $qb->setParameter('timeRef', $timeRef)
            ->andWhere('r.suppressed = 0')
            ->setParameter('date', $date . '%')
            ->orderBy('r.date', 'ASC')
            ->addOrderBy('r.start', 'ASC')
            ->addOrderBy('pi.sortOrder', 'ASC');

        return $qb
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Returns all the rides by date and kind
     */
    public function findAllByDateAndKind($date, $kind)
    {
        return $this->createQueryBuilder('r')
            ->addSelect('s', 'p', 'v', 'pi', 'z')
            ->leftJoin('r.staff', 's')
            ->leftJoin('s.person', 'p')
            ->leftJoin('s.driverZones', 'z')
            ->leftJoin('r.vehicle', 'v')
            ->leftJoin('r.pickups', 'pi')
            ->where('r.date LIKE :date')
            ->andWhere('r.kind = :kind')
            ->andWhere('r.suppressed = 0')
            ->setParameter('date', $date . '%')
            ->setParameter('kind', $kind)
            ->orderBy('r.date', 'ASC')
            ->addOrderBy('r.start', 'ASC')
            ->addOrderBy('s.priority', 'ASC')
            ->addOrderBy('pi.sortOrder', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Returns all the rides by status
     */
    public function findAllByStatus(string $status)
    {
        $operator = 'finished' === $status ? '<' : '>=';
        return $this->createQueryBuilder('r')
            ->addSelect('s', 'p', 'v', 'pi', 'z')
            ->leftJoin('r.staff', 's')
            ->leftJoin('s.person', 'p')
            ->leftJoin('s.driverZones', 'z')
            ->leftJoin('r.vehicle', 'v')
            ->leftJoin('r.pickups', 'pi')
            ->where('r.date ' . $operator . ' :date')
            ->andWhere('r.suppressed = 0')
            ->orderBy('r.date', 'ASC')
            ->addOrderBy('r.start', 'ASC')
            ->addOrderBy('pi.sortOrder', 'ASC')
            ->setParameter('date', date('Y-m-d', time()))
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Returns the rides that are linked to another one for date
     */
    public function findAllLinked(string $date)
    {
        return $this->createQueryBuilder('r')
            ->where('r.date LIKE :date')
            ->andWhere('r.linkedRide IS NOT NULL')
            ->andWhere('r.suppressed = 0')
            ->setParameter('date', $date . '%')
            ->orderBy('r.start', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findAllByDateAndBetween($date, $from, $to) {
        return $this->createQueryBuilder('r')
            ->leftJoin('r.pickups', 'pi')
            ->where('r.date LIKE :date')
            ->andWhere(' (r.start >= :from AND r.start <= :to)')
            ->andWhere('r.suppressed = 0')
            ->setParameter('date', $date . '%')
            ->setParameter('from', $from)
            ->setParameter('to', $to)
            ->orderBy('r.start', 'ASC')
            ->addOrderBy('pi.sortOrder', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Returns the rides by date and staff
     */
    public function findOneRideByDateStaffKind($date, $staff, $kind)
    {
        
        $qb = $this->createQueryBuilder('r')
            ->addSelect('pi')
            ->leftJoin('r.staff', 's')
            ->leftJoin('r.pickups', 'pi')
            ->where('r.date = :date')
            ->andWhere('r.staff = :staff')
            ->andWhere('r.suppressed = 0')
            ->andWhere('r.kind = :kind')
            ->orderBy('r.date', 'ASC');
        if($kind = "dropin") {
            $qb->addOrderBy('r.start', 'ASC');
        } else {
            $qb->addOrderBy('r.start', 'DESC');
        }
        return $qb->addOrderBy('pi.sortOrder', 'ASC')
            ->setParameter('date', $date)
            ->setParameter('kind', $kind)
            ->setParameter('staff', $staff)
            ->getQuery()
            ->getResult()
        ;
    }


    /**
     * Returns the rides by date and staff
     */
    public function findAllByDateByStaff($date, $staff)
    {
        return $this->createQueryBuilder('r')
            ->addSelect('s', 'p', 'v', 'pi', 'z')
            ->leftJoin('r.staff', 's')
            ->leftJoin('s.person', 'p')
            ->leftJoin('s.driverZones', 'z')
            ->leftJoin('r.vehicle', 'v')
            ->leftJoin('r.pickups', 'pi')
            ->where('r.date = :date')
            ->andWhere('r.staff = :staff')
            ->andWhere('r.suppressed = 0')
            ->setParameter('date', $date)
            ->setParameter('staff', $staff->getStaffId())
            ->orderBy('r.date', 'ASC')
            ->addOrderBy('r.start', 'ASC')
            ->addOrderBy('pi.sortOrder', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Returns the ride by date and staff
     */
    public function findOneByDateByStaffId($date, $staff)
    {
        return $this->createQueryBuilder('r')
            ->addSelect('s', 'p', 'v', 'pi', 'z')
            ->leftJoin('r.staff', 's')
            ->leftJoin('s.person', 'p')
            ->leftJoin('s.driverZones', 'z')
            ->leftJoin('r.vehicle', 'v')
            ->leftJoin('r.pickups', 'pi')
            ->where('r.date = :date')
            ->andWhere('s.person = :person')
            ->andWhere('r.suppressed = 0')
            ->setParameter('date', $date)
            ->setParameter('staff', $staff)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * Returns the ride if not suppressed
     */
    public function findOneById($rideId)
    {
        return $this->createQueryBuilder('r')
            ->addSelect('s', 'p', 'v', 'pi', 'z')
            ->leftJoin('r.staff', 's')
            ->leftJoin('s.person', 'p')
            ->leftJoin('s.driverZones', 'z')
            ->leftJoin('r.vehicle', 'v')
            ->leftJoin('r.pickups', 'pi')
            ->where('r.rideId = :rideId')
            ->andWhere('r.suppressed = 0')
           // ->andWhere('pi.suppressed = 0 OR pi.suppressed IS NULL')
            ->setParameter('rideId', $rideId)
            ->orderBy('pi.sortOrder', 'ASC')
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}

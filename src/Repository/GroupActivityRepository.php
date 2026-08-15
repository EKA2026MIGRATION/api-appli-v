<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * GroupActivityRepository class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class GroupActivityRepository extends EntityRepository
{
    /**
     * Returns all the groupActivities by date
     */
    public function findAllByDate($date)
    {
        return $this->createQueryBuilder('ga')
            ->where('ga.date = :date')
            ->andWhere('ga.suppressed = 0')
            ->setParameter('date', $date)
            ->addOrderBy('ga.start', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findByDateBetween($date, $from, $to) {
        return $this->createQueryBuilder('ga')
            ->where('ga.date = :date')
            ->andWhere(' (ga.start >= :from AND ga.start <= :to) OR (ga.start <= :from AND ga.end >= :to)')
            ->andWhere('ga.suppressed = 0')
            ->setParameter('date', $date)
            ->setParameter('from', $from)
            ->setParameter('to', $to)
            ->orderBy('ga.start', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Returns the groupActivities by date and staff
     */
    public function findAllByDateByStaff($date, $staff)
    {
        return $this->createQueryBuilder('ga')
            ->addSelect('gas')
            ->leftJoin('ga.staff', 'gas')
            ->where('ga.date = :date')
            ->andWhere('gas.staff = :staff')
            ->andWhere('ga.suppressed = 0')
            ->setParameter('date', $date)
            ->setParameter('staff', $staff->getStaffId())
            ->orderBy('ga.date', 'ASC')
            ->addOrderBy('ga.start', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

     /**
     * Returns the groupActivities by date and sport and start/end time
     */
    public function findAllBySportAndDateTime($date, $sport, $start, $end)
    {
        return $this->createQueryBuilder('ga')
            ->where('ga.date = :date')
            ->andWhere('ga.sport = :sport')
            ->andWhere('ga.start >= :start')
            ->andWhere('ga.end <= :end')
            ->andWhere('ga.suppressed = 0')
            ->setParameter('date', $date)
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->setParameter('sport', $sport)
            ->orderBy('ga.date', 'ASC')
            ->addOrderBy('ga.start', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findOneGroupByDateChild($child, $date, $kind) {

        $qb = $this->createQueryBuilder('ga')
                ->leftJoin('ga.pickupActivities', 'link')
                ->leftJoin('link.pickupActivity', 'p')
                ->where('ga.date = :date')
                ->andWhere('p.child = :child')
                ->setParameter('date', $date)
                ->setParameter('child', $child);
            
        if($kind == "dropin") {
            $qb->orderBy('ga.start', 'ASC');
        } else {
            $qb->orderBy('ga.start', 'DESC');
        }
               
            return $qb->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();

    }

    public function findLunchgroup($date) {
      return $this->createQueryBuilder('ga')
          ->where('ga.date = :date')
          ->andWhere('ga.lunch = 1')
          ->andWhere('ga.suppressed = 0')
          ->setParameter('date', $date)
          ->orderBy('ga.date', 'ASC')
          ->getQuery()
          ->getResult()
      ;
    }

    /**
     * Returns the groupActivity if not suppressed
     */
    public function findOneById($groupActivityId)
    {
        return $this->createQueryBuilder('ga')
            ->where('ga.groupActivityId = :groupActivityId')
            ->andWhere('ga.suppressed = 0')
            ->setParameter('groupActivityId', $groupActivityId)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * Returns all the groupActivity non-locked for a date
     */
    public function getAllNonLocked($date)
    {
        return $this->createQueryBuilder('ga')
            ->where('ga.date = :date')
            ->andWhere('ga.locked = :false OR ga.locked IS NULL')
            ->andWhere('ga.suppressed = 0')
            ->setParameter('date', $date)
            ->setParameter('false', false)
            ->getQuery()
            ->getResult()
        ;
    }
}

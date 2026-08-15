<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * PickupActivityRepository class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class PickupActivityRepository extends EntityRepository
{
    /**
     * Returns all the pickupActivities by child and date
     */
    public function findAllByChildDate($childId, $date)
    {
        return $this->createQueryBuilder('pa')
            ->where('pa.child = :child')
            ->andWhere('pa.date = :date')
            ->andWhere('pa.suppressed = 0')
            ->setParameter('child', $childId)
            ->setParameter('date', $date)
            ->orderBy('pa.start', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findByCreatedAtAndChild($date, $child) {
        return $this->createQueryBuilder('pa')
        ->where('pa.child = :child')
        ->andWhere('pa.createdAt like :date')
        ->andWhere('pa.suppressed = 0')
        ->setParameter('date', $date.'%')
        ->setParameter('child', $child)
        ->orderBy('pa.start', 'ASC')
        ->getQuery()
        ->getResult();
    }

    /**
     * Returns all the pickupActivities by date
     */
    public function findAllByDate($date)
    {
        return $this->createQueryBuilder('pa')
            ->leftJoin('pa.child', 'c')
            ->where('pa.date = :date')
            ->andWhere('pa.suppressed = 0')
            ->setParameter('date', $date)
            ->orderBy('pa.start', 'ASC')
            ->addOrderBy('pa.pickupActivityId', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

        /**
     * Returns all the pickupActivities by date
     */
    public function findAllByDateGroup($date)
    {
        return $this->createQueryBuilder('pa')
            ->leftJoin('pa.child', 'c')
            ->where('pa.date = :date')
            ->andWhere('pa.suppressed = 0')
            ->andWhere('pa.sport <> 10')
            ->setParameter('date', $date)
            ->addOrderBy('c.birthdate', 'DESC')

            ->addOrderBy('c.gender', 'ASC')

          /*  ->addOrderBy('c.lastname', 'ASC')
            ->addOrderBy('c.firstname', 'ASC')*/
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Returns all the pickupActivities by status
     */
    public function findAllByStatus($date, $status)
    {
        //$statusCondition = 'null' === $status ? 'pa.status IS NULL' : 'pa.status = :status';

        $qb = $this->createQueryBuilder('pa')
            ->leftJoin('pa.child', 'c')
            ->where('pa.date LIKE :date')
            ->andWhere('pa.suppressed = 0')
            ->orderBy('pa.date', 'ASC')
            ->addOrderBy('c.child_id', 'ASC')
            ->addOrderBy('pa.start', 'ASC')
            ->addOrderBy('c.birthdate', 'DESC')
            ->setParameter('date', $date . '%')
        ;

        if ('null' !== $status) {

            $qb->andWhere('pa.status = :status')
            ->setParameter('status', $status);
        }

        return $qb
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Returns all the pickupActivity using the registrationId linked
     */
    public function findByRegistrationId($registrationId)
    {
        return $this->createQueryBuilder('pa')
            ->where('pa.registration = :registrationId')
            ->setParameter('registrationId', $registrationId)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Returns the pickupActivity if not suppressed
     */
    public function findOneById($pickupActivityId)
    {
        return $this->createQueryBuilder('pa')
            ->addSelect('c')
            ->leftJoin('pa.child', 'c')
            ->where('pa.pickupActivityId = :pickupActivityId')
            ->andWhere('pa.suppressed = 0')
            ->setParameter('pickupActivityId', $pickupActivityId)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}

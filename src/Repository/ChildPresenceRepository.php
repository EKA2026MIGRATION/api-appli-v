<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * ChildPresenceRepository class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class ChildPresenceRepository extends EntityRepository
{
    /**
     * Returns all the childPresence linked to the data provided
     */
    public function findByData($data)
    {
        $startCondition = array_key_exists('start', $data) ? 'pr.start = :start' : '1 = 1';
        $endCondition = array_key_exists('end', $data) ? 'pr.end = :end' : '1 = 1';

        $qb = $this->createQueryBuilder('pr')
            ->addSelect('c')
            ->leftJoin('pr.child', 'c')
            ->where('pr.suppressed = 0')
            ->andWhere('pr.child = :childId')
            ->andWhere('pr.date LIKE :date')
            ->andWhere($startCondition)
            ->andWhere($endCondition)
            ->setParameter('childId', $data['child'])
            ->setParameter('date', $data['date'] . '%')
        ;

        if (array_key_exists('start', $data)) {
            $qb->setParameter('start', $data['start']);
        }
        if (array_key_exists('end', $data)) {
            $qb->setParameter('end', $data['end']);
        }

        return $qb
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findPresenceBetween($child, $from, $to){

        return $this->createQueryBuilder('pr')
            ->where('pr.suppressed = 0')
            ->andWhere('pr.child = :child')
            ->andWhere('pr.date >= :from')
            ->andWhere('pr.date <= :to')
            ->orderBy('pr.date', 'DESC')
            ->setParameter('child', $child)
            ->setParameter('from', $from)
            ->setParameter('to', $to)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * Returns all the childPresence by date
     */
    public function findAllByDate($date)
    {
        return $this->createQueryBuilder('pr')
            ->addSelect('c')
            ->leftJoin('pr.child', 'c')
            ->where('pr.suppressed = 0')
            ->andWhere('pr.date LIKE :date')
        //    ->andWhere('pr.status <> :status')
            ->orderBy('pr.date', 'ASC')
            ->addOrderby('c.lastname')
            ->addOrderBy('pr.start', 'ASC')
            ->setParameter('date', $date . '%')
       //     ->setParameter('status', 'npec')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findByChildBetweenDates($child, $from, $to) {
       
        $qb = $this->createQueryBuilder('pr')
            ->where('pr.child = :child')
            ->andWhere('pr.suppressed = 0')
            ->andWhere('pr.date >= :from')
            ->andWhere('pr.date <= :to')
            ->orderBy('pr.date', 'ASC')
            ->addOrderBy('pr.start', 'ASC')
            ->setParameter(':from', $from)
            ->setParameter(':to', $to)
            ->setParameter('child', $child)
        ;

        return $qb
            ->getQuery()
            ->getResult()
        ;
    }


    /**
     * Returns all the childPresence by child
     */
    public function findLatestCreatedByChildId($childId, $limit = 10)
    {

        $qb = $this->createQueryBuilder('pr')
            ->addSelect('c')
            ->leftJoin('pr.child', 'c')
            ->where('pr.child = :childId')
            ->andWhere('pr.suppressed = 0')
            ->orderBy('pr.updatedAt', 'DESC')
            ->addOrderBy('pr.start', 'ASC')
            ->setParameter('childId', $childId)
            ->setMaxResults(10)
        ;

        return $qb
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Returns all the childPresence by child
     */
    public function findByChild($childId, $date)
    {
        $dateCriteria = 'all' !== $date ? 'pr.date LIKE :date' : ' 1 = 1';

        $qb = $this->createQueryBuilder('pr')
            ->addSelect('c')
            ->leftJoin('pr.child', 'c')
            ->where('pr.child = :childId')
            ->andWhere('pr.suppressed = 0')
            ->andWhere($dateCriteria)
            ->orderBy('pr.date', 'ASC')
            ->addOrderBy('pr.start', 'ASC')
            ->setParameter('childId', $childId)
        ;


        if ('all' !== $date) {
            $qb->setParameter('date', $date . '%');
        }

        return $qb
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Returns all the childPresence using the registrationId linked
     */
    public function findByRegistrationId($registrationId)
    {
        return $this->createQueryBuilder('pr')
            ->where('pr.registration = :registrationId')
            ->setParameter('registrationId', $registrationId)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Returns the pickup if not suppressed
     */
    public function findOneById($childPresenceId)
    {
        return $this->createQueryBuilder('pr')
            ->where('pr.childPresenceId = :childPresenceId')
            ->andWhere('pr.suppressed = 0')
            ->setParameter('childPresenceId', $childPresenceId)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}

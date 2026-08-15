<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * StaffRepository class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class StaffRepository extends EntityRepository
{
    /**
     * Returns all the staffs in an array
     */
    public function findAllByKind(string $kind, $isActive)
    {
        $kindCondition = 'all' !== $kind ? 's.kind = :kind' : '1 = 1';

        $qb = $this->createQueryBuilder('s')
            ->addSelect('p')
            ->leftJoin('s.person', 'p')
            ->where($kindCondition)
            ->andWhere('s.suppressed = 0')
            ->orderBy('p.firstname', 'ASC')
            ->addOrderBy('p.lastname', 'ASC')
        ;

        if ('all' !== $kind) {
            $qb->setParameter('kind', $kind);
        }

        if($isActive != "all") {
            $qb->andWhere('s.isActive = :isActive')
            ->setParameter('isActive', $isActive);
        } 

        return $qb
            ->getQuery()
            ->getResult()
        ;
    }

    public function retrieveCurrentBirthdates($start, $n)
    {

        $start_sql = date("m-d", strtotime($start));

        $qb = $this->createQueryBuilder('s')
           ->leftJoin('s.person', 'p')
           ->where('s.suppressed = 0');

         $orModule = $qb->expr()->orx();
         for($i = 0; $i < $n; $i++) {
             $orModule->add($qb->expr()->like('p.birthdate', ':mydate'.$i));
         }

         $qb->andWhere($orModule);

         $current_date = $start;
         for($i = 0; $i < $n; $i++) {
             $current_date_sql = date("m-d", strtotime($current_date));
             $qb->setParameter('mydate'.$i, '%-'.$current_date_sql);
             $current_date = date('Y-m-d', strtotime($current_date.", +1 day"));
         }

         return $qb->orderBy('p.birthdate', 'ASC')
           ->getQuery()
           ->getResult();

    }

    /**
     * Returns all the staff corresponding to the searched term
     */
    public function findAllSearch(string $term, $isActive)
    {
        $qb = $this->createQueryBuilder('s')
            ->addSelect('p')
            ->leftJoin('s.person', 'p')
            ->where('LOWER(p.firstname) LIKE :term OR LOWER(p.lastname) LIKE :term')
            ->andWhere('s.suppressed = 0')
            ->andWhere('p.suppressed = 0');

        if($isActive != "all") {
            $qb->andWhere('s.isActive = :isActive')
            ->setParameter('isActive', $isActive);
        } 
        return $qb->andWhere('s.isActive = :isActive')
            ->orderBy('p.firstname', 'ASC')
            ->addOrderBy('p.lastname', 'ASC')
            ->setParameter('term', '%' . strtolower($term) . '%')
            ->setParameter('isActive', $isActive)            
            ->getQuery()
        ;
    }

    /**
     * Returns the staff if not suppressed
     */
    public function findOneById(int $staffId)
    {
        return $this->createQueryBuilder('s')
            ->addSelect('p, z')
            ->leftJoin('s.person', 'p')
            ->leftJoin('s.driverZones', 'z')
            ->where('s.staffId = :staffId')
            ->andWhere('s.suppressed = 0')
            ->andWhere('p.suppressed = 0')
            ->orderBy('z.priority', 'ASC')
            ->setParameter('staffId', $staffId)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * Returns the staff for deletion (including already suppressed ones)
     */
    public function findOneByIdForDelete(int $staffId)
    {
        return $this->createQueryBuilder('s')
            ->addSelect('p')
            ->leftJoin('s.person', 'p')
            ->where('s.staffId = :staffId')
            ->setParameter('staffId', $staffId)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}

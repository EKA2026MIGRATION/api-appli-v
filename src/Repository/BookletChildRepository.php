<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;


/**
 * BookletChildRepository class
 * @author Sandy Razafirimo <sandyrazafitrimo@gmail.com>
 */
class BookletChildRepository extends EntityRepository
{

    /**
     * Returns all bookletChilds
     */
    public function findAllBooklet($status = null, $staff = null, $from = null, $to = null) {

        $qb =   $this->createQueryBuilder('b')
                ->leftJoin('b.child', 'c')
                ->where('b.suppressed = 0');

        if($staff) {
            $qb->andWhere('b.staff = :staff')
            ->setParameter('staff', $staff);
        }

        if($from && $to) {

            $qb->andwhere('b.dateEvaluation >= :from')
                ->andWhere('b.dateEvaluation <= :to')
                ->setParameter('from', $from)
                ->setParameter('to', $to);
        }



        if($status) {

            if($status == "edition") {
                $qb->andWhere('b.status in (:status1, :status2, :status3)')
                ->setParameter('status1', "draft")
                ->setParameter('status2', "ready")
                ->setParameter('status3', "toreread");
            } else {
                $qb->andWhere('b.status = :status')
                ->setParameter('status', $status);
            }
        }

        return   $qb->orderBy('c.lastname', 'ASC')
                    ->addOrderBy('c.firstname', 'ASC')
                    ->getQuery()
                    ->getResult();
        ;
    }


    /**
     * Returns all bookletChilds
     */
    public function findLatest ($from = null) {

        $qb =   $this->createQueryBuilder('b');

        if($from) {
            $qb->andwhere('b.dateEvaluation >= :from')
                ->setParameter('from', $from);
        }

        $qb->andWhere('b.status in (:status1, :status2, :status3)')
            ->setParameter('status1', "draft")
            ->setParameter('status2', "toreread")
            ->setParameter('status3', "ready");

        return   $qb->orderBy('b.dateEvaluation', 'DESC')
            ->getQuery()
            ->getResult();
        ;
    }

    /**
     * Returns all bookletChilds
     */
    public function findAllByStaff($staff, $status = null) {

        $qb =   $this->createQueryBuilder('b')
                ->leftJoin('b.child', 'c')
                ->where('b.suppressed = 0')
                ->andWhere('b.staff = :staff')
                ->setParameter('staff', $staff);


        if($status) {

            if($status == "edition") {
                $qb->andWhere('b.status in (:status1, :status2, :status3)')
                ->setParameter('status1', "draft")
                ->setParameter('status2', "ready")
                ->setParameter('status3', "toreread");

            } else {
                $qb->andWhere('b.status = :status')
                ->setParameter('status', $status);
            }
        }

        return   $qb->orderBy('c.lastname', 'ASC')
                    ->addOrderBy('c.firstname', 'ASC')
                    ->getQuery()
                    ->getResult();
        ;
    }


     /**
     * Returns all bookletChilds
     */
    public function findAllBychild($child, $status) {

        return   $this->createQueryBuilder('b')
                ->leftJoin('b.child', 'c')
                ->where('b.suppressed = 0')
                ->andWhere('b.child = :child')
                ->setParameter('child', $child)
                ->andWhere('b.status = :status')
                ->setParameter('status', $status)
                ->orderBy('b.dateEvaluation', 'DESC')
                ->getQuery()
                ->getResult();
        ;
    }


    public function findPreviousBookletChild($child, $booklet, $bookletChildId) {
        return $this->createQueryBuilder('bc')
            ->where('bc.child = :child')
            ->andWhere('bc.booklet = :booklet')
            ->andWhere('bc.id < :bookletChildId')
            ->andWhere('bc.suppressed = 0')
            ->setParameter('child', $child)
            ->setParameter('booklet', $booklet)
            ->setParameter('bookletChildId', $bookletChildId)
            ->setMaxResults(1)
            ->orderBy('bc.dateEvaluation', 'DESC')
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }



}



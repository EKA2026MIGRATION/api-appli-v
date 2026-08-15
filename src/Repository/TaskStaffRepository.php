<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;
use App\Entity\Staff;

/**
 * TaskStaffRepository class
 * @author Sandy Razafirimo <sandyrazafitrimo@gmail.com>
 */
class TaskStaffRepository extends EntityRepository
{
  /**
   * Returns all the tasks for a staff at a date
   */
  public function findByStaffAndDate(Staff $staff, $date)
  {
      return $this->createQueryBuilder('s')
          ->where('s.dateTask LIKE :date')
          ->andWhere('s.staff = :staff')
          ->orderBy('s.dateTask', 'ASC')
          ->addOrderBy('s.criticity', 'ASC')
          ->setParameter('date', '%' . $date . '%')
          ->setParameter('staff', $staff)
          ->getQuery()
          ->getResult()
      ;
  }

  public function findTaskDone($date) {
        return $this->createQueryBuilder('s')
            ->where('s.dateTask LIKE :date')
            ->andWhere('s.task is not null')
            ->orderBy('s.name', 'ASC')
            ->addOrderBy('s.criticity', 'ASC')
            ->setParameter('date', $date . '%')
            ->getQuery()
            ->getResult()
        ;
  }


  /**
   * Returns the one task done by staff and date (first or last)
   */
  public function findOneByStaffAndDate(Staff $staff, $date, $which)
  {
      $qb = $this->createQueryBuilder('s')
          ->where('s.dateTask LIKE :date')
          ->andWhere('s.staff = :staff')
          ->andWhere("s.step = 'DONE' ");

      if($which == "first") $qb->orderBy('s.dateTask', 'ASC');
      if($which == "last") $qb->orderBy('s.dateTask', 'DESC');

      $qb->setParameter('date', '%' . $date . '%')
          ->setParameter('staff', $staff)
          ->setMaxResults(1);


      return $qb
          ->getQuery()
          ->getOneOrNullResult();
  }


  /**
   * Returns all the tasks by date
   */
  public function findByDate($date)
  {
      return $this->createQueryBuilder('s')
          ->where('s.dateTask LIKE :date')
          ->orderBy('s.staff', 'ASC')
          ->addOrderBy('s.dateTask', 'ASC')
          ->addOrderBy('s.criticity', 'ASC')
          ->setParameter('date', '%' . $date . '%')
          ->getQuery()
          ->getResult()
      ;
  }

  public function findWithLimit($step, $dateLimit, $from = null) {

        $qb = $this->createQueryBuilder('s')
            ->where('s.step = :step')
            ->setParameter('step', $step);


        if($from != null) {
          $qb->andWhere('s.dateLimit >= :from')
          ->setParameter('from', $from.' 00:00:00');
        }


        return $qb->andWhere('s.dateLimit <= :dateLimit')
            ->setParameter('dateLimit', $dateLimit.' 23:59:59')
            ->orderBy('s.staff', 'ASC')
            ->addOrderBy('s.dateTask', 'ASC')
            ->getQuery()
            ->getResult()
        ;
  }





  /**
   * Returns all the tasks by step
   */
  public function findByStep($step, $staff = null, $dateTask = null, $dateEnd = null)
  {
      $qb = $this->createQueryBuilder('s')
          ->where('s.step LIKE :step');
      $qb->setParameter('step', $step);

      if($step == "DONE") {
        $qb->orderby('s.dateTaskDone', 'ASC');
      } else {
        $qb->orderBy('s.dateTask', 'ASC');
      }

      if($staff != null ) {
        $qb->andWhere('s.staff = :staff')
          ->setParameter('staff', $staff);
      }

      if($dateTask) {
        if(!$dateEnd) {
          $qb->andWhere('s.dateTask like :dateTask')
            ->setParameter('dateTask', '%'.$dateTask.'%');
        } else {
          $qb->andWhere('s.dateTask > :dateTask')
            ->setParameter('dateTask', $dateTask.' 00:00:00')
            ->andWhere('s.dateTask < :dateEnd')
            ->setParameter('dateEnd', $dateEnd.' 23:59:59');
        }
      }

      return $qb
        ->getQuery()
        ->getResult();
  }


    /**
     * Returns all the tasks by step
     */
    public function findByStepAll($step)
    {
        return $this->createQueryBuilder('s')
              ->where('s.step LIKE :step')
              ->andWhere('s.type != \'basic\'')
              ->setParameter('step', $step)
              ->addOrderBy('s.staff', 'ASC')
              ->addOrderBy('s.dateTask', 'ASC')
              ->getQuery()
              ->getResult();
    }



}

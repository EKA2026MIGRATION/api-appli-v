<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * MealRepository class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class MealRepository extends EntityRepository
{
    /**
     * Returns all the meals by date
     */
    public function findAllByDate($date)
    {
        return $this->createQueryBuilder('m')
            ->addSelect('ml', 'f')
            ->leftJoin('m.foods', 'ml')
            ->leftJoin('ml.food', 'f')
            ->where('m.date LIKE :date')
            ->andWhere('m.suppressed = 0')
            ->setParameter('date', $date . '%')
            ->orderBy('m.mealId', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }


    /**
     * Returns by child and date
     */
    public function findByChildAndDate($child, $date, $result)
    {
        return $this->createQueryBuilder('m')
            ->addSelect('ml', 'f')
            ->leftJoin('m.foods', 'ml')
            ->leftJoin('ml.food', 'f')
            ->where('m.date LIKE :date')
            ->andWhere('m.child = :child')
            ->andWhere('m.suppressed = 0')
            ->setParameter('date', $date . '%')
            ->setParameter('child', $child)
            ->orderBy('m.mealId', 'ASC')
            ->getQuery()
            ->getOneOrNullResult()

        ;
    }

    /**
     * Returns by latest meal
     */
    public function findLatestByChild($child)
    {

      $date = date('Y-m-d');

      return $this->createQueryBuilder('m')
          ->addSelect('ml', 'f')
          ->leftJoin('m.foods', 'ml')
          ->leftJoin('ml.food', 'f')
          ->where('m.date < :date')
          ->andWhere('m.child = :child')
          ->andWhere('m.suppressed = 0')
          ->setParameter('date', $date . '%')
          ->setParameter('child', $child)
          ->orderBy('m.date', 'DESC')
           ->setMaxResults(1)
          ->getQuery()
          ->getOneOrNullResult()

      ;
    }

    /**
     * Returns the ride if not suppressed
     */
    public function findOneById($mealId)
    {
        return $this->createQueryBuilder('m')
            ->addSelect('ml', 'f')
            ->leftJoin('m.foods', 'ml')
            ->leftJoin('ml.food', 'f')
            ->where('m.mealId LIKE :mealId')
            ->andWhere('m.suppressed = 0')
            ->setParameter('mealId', $mealId)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}

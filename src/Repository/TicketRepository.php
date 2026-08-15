<?php

namespace App\Repository;

use App\Entity\Ticket;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Ticket|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ticket|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ticket[]    findAll()
 * @method Ticket[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TicketRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ticket::class);
    }

    public function findNeedCall()
    {
      return $this->createQueryBuilder('t')
      ->andWhere('t.recall = 1')
      ->addOrderBy('t.hasBeenTreated', 'ASC')
      ->addOrderBy('t.createdAt', 'DESC')
      ->getQuery()
      ->getResult();
    }

    /*
    * persona (string),
    * category (object), location (objet),
    * type (string), origin (string)
    * date_from, date_to (Y-m-d)
    * has_been_treated (bool)
    * recall (bool)
    */
    public function findByCriteria($values) {

      $qb = $this->createQueryBuilder('t')
          ->where('1 = 1');

      if($values['persona'] != null || $values['persona'] != "" ) {
        $qb->andWhere('t.persona like :persona')
        ->setParameter('persona', '%'.$values['persona'].'%');
      };
      if($values['category'] != null || $values['category'] != "") {
        $qb->andWhere('t.category = :category')
        ->setParameter('category', $values['category']);
      };
      if($values['location'] != null || $values['location'] != "") {
        $qb->andWhere('t.location = :location')
        ->setParameter('location', $values['location']);
      };
      if($values['type'] != null || $values['type'] != "" ) {
        $qb->andWhere('t.type like :type')
        ->setParameter('type', '%'.$values['type'].'%');
      };
      if($values['origin'] != null || $values['origin'] != "" ) {
        $qb->andWhere('t.originCall like :origin')
        ->setParameter('origin', '%'.$values['origin'].'%');
      };
      if($values['date_from'] != null || $values['date_from'] != "" ) {
        $qb->andWhere('t.dateCall > :date_from')
        ->setParameter('date_from', $values['date_from'].' 00:00:00');
      };
      if($values['date_to'] != null|| $values['date_to'] != "" ) {
        $qb->andWhere('t.dateCall < :date_to')
        ->setParameter('date_to', $values['date_to'].' 00:00:00');
      };
      if($values['has_been_treated'] != null || $values['has_been_treated'] != "" ) {
        $qb->andWhere('t.hasBeenTreated = :has_been_treated')
        ->setParameter('has_been_treated', $values['has_been_treated']);
      };
      if($values['recall'] != null|| $values['recall'] != "" ) {
        $qb->andWhere('t.recall = :recall')
        ->setParameter('recall', $values['recall']);
      };

      $qb->orderBy('t.dateCall', 'ASC')
      ->setFirstResult(0)
      ->setMaxResults($values['limit']);

      return $qb
          ->getQuery()
          ->getResult()
      ;

    }

    // /**
    //  * @return Ticket[] Returns an array of Ticket objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Ticket
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

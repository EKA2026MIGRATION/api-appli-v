<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * RegistrationRepository class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class RegistrationRepository extends EntityRepository
{
    /**
     * Returns all the registrations related to status in an array
     */
    public function findAllByStatus($status)
    {
        $statusCondition = 'null' === $status ? 'r.status IS NULL' : 'r.status = :status';

        $qb = $this->createQueryBuilder('r')
            ->where($statusCondition)
            ->andWhere('r.suppressed = 0')
            ->orderBy('r.registration', 'DESC')
        ;

        if ('null' !== $status) {
            $qb->setParameter('status', $status);
        }

        return $qb
            ->getQuery()
        ;
    }

    public function findAwaiting() {
        return $this->createQueryBuilder('r')
        ->where('r.status = :cart')
        ->andWhere('r.suppressed = 0')
        ->orderBy('r.registration', 'DESC')
        ->setParameter('cart', 'waiting')
        ->getQuery()
        ->getResult();
    ;
    }

    public function findLatest($child, $hasSport = true) {
        $qb = $this->createQueryBuilder('r')
        ->innerJoin('r.sports', 'link')
        ->where('r.child = :child')
        ->orderBy('r.updatedAt', 'DESC')
        ->andWhere('r.suppressed = 0')
        ->andWhere('r.status <> :cart')
        ->setParameter('child', $child)
        ->setParameter('cart', "cart")
        ->setMaxResults(1)
       ;
        return $qb->getQuery()->getOneOrNullResult()
        ;
    }

    public function findRegistrationsByData($child, $date) {
       
        $qb = $this->createQueryBuilder('r')
            ->where('r.status IN (:cart, :payed)')
            ->andWhere('r.child = :child')
            ->andWhere('r.sessions LIKE :date')
            ->andWhere('r.suppressed = 0')
            ->orderBy('r.registration', 'DESC')
            ->setParameter('cart', 'cart')
            ->setParameter('payed', 'payed')
            ->setParameter('child', $child)
            ->setParameter('date', "%".$date."%")
        ;

  

        return $qb
            ->getQuery()
            ->getResult()
        ;
    }


    /**
     * Returns all the registrations related to person and status in an array
     */
    public function findAllByPersonAndStatus($personId, $status)
    {
       
        ($status == null) ? $statusCondition = 'r.status IS NULL' : $statusCondition = 'r.status = :status' ;
       
        $qb = $this->createQueryBuilder('r')
            ->where($statusCondition)
            ->andWhere('r.person = :personId')
            ->andWhere('r.suppressed = 0')
            ->orderBy('r.registration', 'DESC')
            ->setParameter('personId', $personId)
        ;

        if ('null' !== $status) {
            $qb->setParameter('status', $status);
        }

        return $qb
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Returns all the registrations related to person without cart status in an array
     */
    public function findAllWithoutCart()
    {
        return $this->createQueryBuilder('r')
            ->where('r.status != :cart')
            ->andWhere('r.suppressed = 0')
            ->orderBy('r.registrationId', 'DESC')
            ->setParameter('cart', 'cart')
            ->getQuery()
        ;
    }

    public function findByInvoice($invoice, $status = null) {
        $qb = $this->createQueryBuilder('r')
            ->where('r.invoice = :invoice')
            ->andWhere('r.suppressed = 0')
            ->setParameter('invoice', $invoice)
            ->orderBy('r.registration', 'DESC');

        if($status) {   
            $qb->andWhere('r.status = :status')
            ->setParameter('status', 'payed');
        };

        return $qb->getQuery()->getResult();
    }

    /**
     * Returns the registration if not suppressed
     */
    public function findOneById($registrationId)
    {
        return $this->createQueryBuilder('r')
            ->where('r.registrationId = :registrationId')
            ->andWhere('r.suppressed = 0')
            ->setParameter('registrationId', $registrationId)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * return all by child from a date to another
     */
    public function findAllByChild($child, $from, $to) {

        $qb = $this->createQueryBuilder('r')
                ->where('r.child = :child')
                ->orderBy('r.registration', 'DESC')
                ->andWhere('r.suppressed = 0')
                ->andWhere('r.registration >= :from')
                ->andWhere('r.registration <= :to')
                ->setParameter('child', $child)
                ->setParameter(':from', $from)
                ->setParameter(':to', $to)
               ;

        
        return $qb->getQuery()->getResult();

    }
}

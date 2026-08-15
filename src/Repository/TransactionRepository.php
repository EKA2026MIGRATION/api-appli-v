<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * TransactionRepository class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class TransactionRepository extends EntityRepository
{
    /**
     * Returns all the transactions
     */
    public function findAll()
    {
        return $this->createQueryBuilder('t')
            ->where('t.suppressed = 0')
            ->orderBy('t.internalOrder', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Returns all the transactions by date
     */
    public function findAllByDate($date)
    {
        return $this->createQueryBuilder('t')
            ->where('t.date LIKE :date')
            ->andWhere('t.suppressed = 0')
            ->setParameter('date', $date . '%')
            ->orderBy('t.date', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Returns all the transactions by date and status
     */
    public function findAllByDateStatus($date, $status)
    {
        return $this->createQueryBuilder('t')
            ->where('t.date LIKE :date')
            ->andWhere('t.status = :status')
            ->andWhere('t.suppressed = 0')
            ->setParameter('date', $date . '%')
            ->setParameter('status', strtolower($status))
            ->orderBy('t.date', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Returns all the transactions by date and person
     */
    public function findAllByDatePerson($date, $personId)
    {
        return $this->createQueryBuilder('t')
            ->where('t.date LIKE :date')
            ->andWhere('t.person = :personId')
            ->andWhere('t.suppressed = 0')
            ->setParameter('date', $date . '%')
            ->setParameter('personId', $personId)
            ->orderBy('t.date', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Returns all the transactions by status and person
     */
    public function findAllByStatusPerson($status, $personId)
    {
        return $this->createQueryBuilder('t')
            ->where('t.status = :status')
            ->andWhere('t.person = :personId')
            ->andWhere('t.suppressed = 0')
            ->setParameter('status', $status)
            ->setParameter('personId', $personId)
            ->orderBy('t.date', 'DESC')
            ->orderBy('t.status', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Returns the transaction if not suppressed
     */
    public function findOneById($transactionId)
    {
        return $this->createQueryBuilder('t')
            ->where('t.transactionId = :transactionId')
            ->andWhere('t.suppressed = 0')
            ->setParameter('transactionId', $transactionId)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findLatestByInvoice($invoice) {
        return $this->createQueryBuilder('t')
            ->where('t.invoice = :invoice')
            ->andWhere('t.suppressed = 0')
            ->andWhere("t.status = 'process' ")
            ->setParameter('invoice', $invoice)
            ->orderBy('t.date', 'DESC')
            ->getQuery()
            ->getOneOrNullResult()
        ;

    }

    /**
     * Returns the transaction if not suppressed by internalOrder
     */
    public function findOneByInternalOrder($internalOrder)
    {
        return $this->createQueryBuilder('t')
            ->where('t.internalOrder = :internalOrder')
            ->andWhere('t.suppressed = 0')
            ->setParameter('internalOrder', $internalOrder)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}

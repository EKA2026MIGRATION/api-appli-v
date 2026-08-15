<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * MailRepository class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class MailRepository extends EntityRepository
{
    /**
     * Returns all the mails corresponding to the searched term
     */
    public function findAllSearch(string $term)
    {
        return $this->createQueryBuilder('m')
            ->where('LOWER(m.title) LIKE :term')
            ->andWhere('m.suppressed = 0')
            ->orderBy('m.title', 'ASC')
            ->setParameter('term', '%' . strtolower($term) . '%')
            ->getQuery()
        ;
    }

    /**
     * Returns the mail if not suppressed and active
     */
    public function findOneById($mailId)
    {
        return $this->createQueryBuilder('m')
            ->where('m.mailId = :mailId')
            ->andWhere('m.suppressed = 0')
            ->setParameter('mailId', $mailId)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}

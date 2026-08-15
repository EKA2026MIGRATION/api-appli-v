<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * BlogRepository class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class BlogRepository extends EntityRepository
{
    /**
     * Returns all the blogs corresponding to the searched term
     */
    public function findAllSearch(string $term)
    {
        return $this->createQueryBuilder('b')
            ->where('LOWER(b.title) LIKE :term')
            ->andWhere('b.suppressed = 0')
            ->orderBy('b.title', 'ASC')
            ->setParameter('term', '%' . strtolower($term) . '%')
            ->getQuery()
        ;
    }

    /**
     * Returns the blog if not suppressed and active
     */
    public function findOneById($blogId)
    {
        return $this->createQueryBuilder('b')
            ->where('b.blogId = :blogId')
            ->andWhere('b.suppressed = 0')
            ->setParameter('blogId', $blogId)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}

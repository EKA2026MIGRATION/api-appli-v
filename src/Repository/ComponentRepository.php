<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * ComponentRepository class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class ComponentRepository extends EntityRepository
{
    /**
     * Returns all the components
     */
    public function findAll()
    {
        return $this->createQueryBuilder('c')
            ->where('c.suppressed = 0')
            ->orderBy('c.nameFr', 'ASC')
            ->getQuery()
        ;
    }

    /**
     * Returns all the components corresponding to the searched term
     */
    public function findAllSearch(string $term)
    {
        return $this->createQueryBuilder('c')
            ->where('LOWER(c.nameFr) LIKE :term')
            ->andWhere('c.suppressed = 0')
            ->orderBy('c.nameFr', 'ASC')
            ->setParameter('term', '%' . strtolower($term) . '%')
            ->getQuery()
        ;
    }

    /**
     * Returns the component if not suppressed
     */
    public function findOneById($componentId)
    {
        return $this->createQueryBuilder('c')
            ->where('c.componentId = :componentId')
            ->andWhere('c.suppressed = 0')
            ->setParameter('componentId', $componentId)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}

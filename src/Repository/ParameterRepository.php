<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * PersonRepository class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class ParameterRepository extends EntityRepository
{
    /**
     * Returns all the groups of age
     */
    public function findAllGroupAge()
    {
        return $this->createQueryBuilder('p')
            ->where('p.name = :name')
            ->andWhere('p.isActive = :true')
            ->setParameter('name', 'GroupAge')
            ->setParameter('true', true)
            ->orderBy('p.value', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Returns the requested parameter if active
     */
    public function findOneByName($name)
    {
        return $this->createQueryBuilder('p')
            ->where('p.name = :name')
            ->andWhere('p.isActive = :true')
            ->setParameter('name', $name)
            ->setParameter('true', true)
            ->orderBy('p.value', 'ASC')
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}

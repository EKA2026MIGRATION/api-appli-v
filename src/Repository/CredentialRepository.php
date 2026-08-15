<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * CredentialRepository class
 * @author Sandy Razafitrimo <sandyrazafitrimo@gmail.com>
 */
class CredentialRepository extends EntityRepository
{

    /**
     * Returns the requested crendtial if active
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


    /**
     * Returns the requested crendtial if active
     */
    public function list()
    {
        return $this->createQueryBuilder('p')
            ->where('p.isActive = :true')
            ->setParameter('true', true)
            ->orderBy('p.name', 'ASC')
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY); 
    }
}

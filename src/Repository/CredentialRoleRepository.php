<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * CredentialRoleRepository class
 * @author Sandy Razafitrimo <sandyrazafitrimo@gmail.com>
 */
class CredentialRoleRepository extends EntityRepository
{
    /**
     * Returns all the groups of age
     */
    public function listByRole($role)
    {
        return $this->createQueryBuilder('r')
            ->select('r.role, c.name, c.description')
            ->leftJoin('r.credential', 'c')
            ->where('r.role = :role')
            ->andWhere('c.isActive = :true')
            ->setParameter('role', strtoupper($role))
            ->setParameter('true', true)
            ->orderBy('c.name', 'ASC')
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY); 

        ;
    }

}

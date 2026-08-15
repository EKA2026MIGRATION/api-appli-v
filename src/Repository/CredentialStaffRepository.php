<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * CredentialStaffRepository class
 * @author Sandy Razafitrimo <sandyrazafitrimo@gmail.com>
 */
class CredentialStaffRepository extends EntityRepository
{
/**
     * Returns all credentials by staff
     */
    public function listByStaff($staff)
    {
        return $this->createQueryBuilder('r')
            ->select(' c.value as role ,c.name, c.description')
            ->leftJoin('r.credential', 'c')
            ->where('r.staff = :staff')
            ->andWhere('c.isActive = :true')
            ->setParameter('staff', $staff)
            ->setParameter('true', true)
            ->orderBy('c.name', 'ASC')
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY); 

        ;
    }
}

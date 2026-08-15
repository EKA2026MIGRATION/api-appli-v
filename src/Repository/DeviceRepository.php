<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * DeviceRepository class
 * @author Sandy Razafitrimo <sandy@etsik.com>
 */
class DeviceRepository extends EntityRepository
{

  /**
   * Returns a device by user and identifier
   */
  public function findOneByUserAndIdent($user, $identifier)
  {
      return $this->createQueryBuilder('d')
          ->where('d.user = :user')
          ->andWhere('d.identifier = :identifier')
          ->setParameter('user', $user)
          ->setParameter('identifier', $identifier)
          ->getQuery()
          ->getOneOrNullResult()
      ;
  }
}

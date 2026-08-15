<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;
use App\Entity\Notification;

/**
 * NotificationRepository class
 * @author Sandy Razafitrimo
 */
class NotificationRepository extends EntityRepository
{
   public function findByPerson($person) {
            return $this->createQueryBuilder('no')
            ->leftJoin('no.notificationPersonLinks', 'l')
            ->where('l.person = :person')
            ->orderBy('no.dateNotification', 'DESC')
            ->setParameter('person', $person)
            ->getQuery()
            ->getResult()
        ;
   }
}

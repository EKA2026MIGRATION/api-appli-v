<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * StaffPresenceRepository class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class StaffPresenceRepository extends EntityRepository
{
    /**
     * returns all the staffPresences between two dates
     */
    public function findAllBetweenDates($start, $end, $staff = null)
    {
        $qb = $this->createQueryBuilder('pr')
            ->leftJoin('pr.staff', 's')
            ->where('pr.date >= :start')
            ->andWhere('pr.date <= :end')
            ->andWhere('pr.suppressed = 0')
            ->andWhere('s.suppressed = 0');


        if($staff) {
          $qb->andWhere('pr.staff = :staff')
            ->setParameter('staff', $staff);
        };
          $qb->setParameter('start', $start)
            ->setParameter('end', $end)
            ->orderBy('pr.staff', 'ASC')
            ->addOrderBy('pr.date', 'ASC')
            ->groupBy('pr.staff, pr.date');
        ;
        return $qb->getQuery()->getResult();
    }

    /**
     * Returns all the presence linked to the data provided
     */
    public function findByData($data)
    {
        $startCondition = array_key_exists('start', $data) ? 'pr.start = :start' : '1 = 1';
        $endCondition = array_key_exists('end', $data) ? 'pr.end = :end' : '1 = 1';

        $qb = $this->createQueryBuilder('pr')
            ->addSelect('s, z')
            ->leftJoin('pr.staff', 's')
            ->leftJoin('s.person', 'p')
            ->leftJoin('s.driverZones', 'z')
            ->where('pr.suppressed = 0')
            ->andWhere('s.suppressed = 0')
            ->andWhere('pr.staff = :staffId')
            ->andWhere('pr.date LIKE :date')
            ->andWhere($startCondition)
            ->andWhere($endCondition)
            ->orderBy('z.priority', 'ASC')
            ->setParameter('staffId', $data['staff'])
            ->setParameter('date', $data['date'] . '%')

        ;

        if (array_key_exists('start', $data)) {
            $qb->setParameter('start', $data['start']);
        }
        if (array_key_exists('end', $data)) {
            $qb->setParameter('end', $data['end']);
        }

        return $qb
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * Returns all the presence by kind and date
     */
    public function findAllByKindAndDate($kind, $date, $status = null)
    {

        $kindCriteria = 'all' !== $kind ? 's.kind = :kind' : '1 = 1';
        $dateCriteria = 'all' !== $date ? 'pr.date LIKE :date' : ' 1 = 1';

        $qb = $this->createQueryBuilder('pr')
            ->addSelect('s, z')
            ->leftJoin('pr.staff', 's')
            ->leftJoin('s.person', 'p')
            ->leftJoin('s.driverZones', 'z')
            ->where('pr.suppressed = 0')
            ->andWhere('s.isActive = 1')
            ->andWhere($kindCriteria)
            ->andWhere($dateCriteria)
            ->andWhere('s.suppressed = 0')
            ->orderBy('pr.date', 'ASC')
            ->addOrderBy('p.firstname')
            ->addOrderBy('pr.start', 'ASC')
            ->addOrderBy('z.priority', 'ASC')
        ;

        if ('all' !== $kind) {
            $qb->setParameter('kind', $kind);
        }
        if ('all' !== $date) {
            $qb->setParameter('date', $date . '%');
        }

        if ($status != null) {

            if($status === "PRESENCE") {
                $statusCriteria = 'pr.typeName in (:status1, :status2, :status3)';
                $qb->andWhere($statusCriteria)
                    ->setParameter('status1', 'PRESENCE')
                    ->setParameter('status2', 'BONUS')
                    ->setParameter('status3', 'CATCHING');
            } else {
                $qb->andWhere('pr.typeName = :status')
                    ->setParameter('status', $status);
            }
        }

        return $qb
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Returns all the presence by staff
     */
    public function findByStaff($staffId, $date)
    {
        $dateCriteria = 'all' !== $date ? 'pr.date LIKE :date' : ' 1 = 1';

        $qb = $this->createQueryBuilder('pr')
            ->addSelect('s, z')
            ->leftJoin('pr.staff', 's')
            ->leftJoin('s.person', 'p')
            ->leftJoin('s.driverZones', 'z')
            ->where('pr.staff = :staffId')
            ->andWhere('pr.suppressed = 0')
            ->andWhere($dateCriteria)
            ->orderBy('pr.date', 'ASC')
            ->addOrderBy('pr.start', 'ASC')
            ->addOrderBy('z.priority', 'ASC')
            ->setParameter('staffId', $staffId)
        ;

        if ('all' !== $date) {
            $qb->setParameter('date', $date . '%');
        }

        return $qb
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Returns all the staffs available for a specific date
     */
    public function findStaffsByPresenceDate($date)
    {
        return $this->createQueryBuilder('pr')
            ->addSelect('s, z')
            ->leftJoin('pr.staff', 's')
            ->leftJoin('s.person', 'p')
            ->leftJoin('s.driverZones', 'z')
            ->where('pr.suppressed = 0')
            ->andWhere('pr.date = :date')
            ->andWhere('s.suppressed = 0')
            ->andWhere('s.isActive = 1')
            ->orderBy('s.priority', 'ASC')
            ->addOrderBy('z.priority', 'ASC')
            ->setParameter('date', $date)
            ->getQuery()
            ->getResult()
        ;
    }
}

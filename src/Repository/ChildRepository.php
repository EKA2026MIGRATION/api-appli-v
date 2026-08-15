<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * ChildRepository class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class ChildRepository extends EntityRepository
{
    /**
     * Returns all the children in an array
     */
    public function findAll()
    {
        return $this->createQueryBuilder('c')
            ->where('c.suppressed = 0')
            ->orderBy('c.lastname', 'ASC')
            ->addOrderBy('c.firstname', 'ASC')
            ->getQuery()
        ;
    }

    /**
     * Returns all the children corresponding to the searched term
     */
    public function findAllSearch(string $term)
    {
        return $this->createQueryBuilder('c')
            ->where('LOWER(c.firstname) LIKE :term OR LOWER(c.lastname) LIKE :term')
            ->andWhere('c.suppressed = 0')
            ->orderBy('c.firstname', 'ASC')
            ->addOrderBy('c.lastname', 'ASC')
            ->setParameter('term', '%' . strtolower($term) . '%')
            ->getQuery()
        ;
    }

    public function findAllSearchResult(string $term, $name, $start = "%") {
        return $this->createQueryBuilder('c')
        ->where('LOWER(c.'.$name.') LIKE :term')
        ->andWhere('c.suppressed = 0')
        ->orderBy('c.lastname', 'ASC')
        ->addOrderBy('c.firstname', 'ASC')
        ->setParameter('term', $start . strtolower($term) . '%')
        ->getQuery()
        ->getResult();
    }

    public function listBySchool($name = null) {
        return $this->createQueryBuilder('c')
            ->leftJoin('c.school', 's')
            ->where(' c.school IS NOT NULL ')
            ->andWhere('c.suppressed = 0')
            ->orderBy('s.name', 'ASC')
            ->addOrderBy('c.lastname', 'ASC')
            ->addOrderBy('c.firstname', 'ASC')
            ->getQuery()
            ->getResult();
    }


    /**
     * Returns the child if not suppressed
     */
    public function findOneById($childId)
    {
        return $this->createQueryBuilder('c')
            ->where('c.childId = :childId')
            ->andWhere('c.suppressed = 0')
            ->setParameter('childId', $childId)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function retrieveCurrentBirthdates($start, $n, $maxAge, $maxYearPresence = 2)
    {
        $start_sql = date("m-d", strtotime($start));
        $yearStart = date('Y', strtotime($start)) - $maxAge;
        $dateLimit = new \DateTime();
        $dateLimit->modify('-' . $maxYearPresence . ' years'); // Date il y a maxYearPresence années

        $qb = $this->createQueryBuilder('c')
            ->leftJoin('c.presences', 'cp') // Utilisez la relation 'presences' définie dans Child
            ->where('c.suppressed = 0')
            ->andWhere('c.birthdate > :yearStart')
            ->andWhere('cp.date >= :dateLimit')
            ->setParameter('yearStart', $yearStart . '-01-01')
            ->setParameter('dateLimit', $dateLimit->format('Y-m-d'));

        $orModule = $qb->expr()->orX();
        for ($i = 0; $i < $n; $i++) {
            $orModule->add($qb->expr()->like('c.birthdate', ':mydate' . $i));
        }

        $qb->andWhere($orModule);

        $current_date = $start;
        for ($i = 0; $i < $n; $i++) {
            $current_date_sql = date("m-d", strtotime($current_date));
            $qb->setParameter('mydate' . $i, '%' . $current_date_sql);
            $current_date = date('Y-m-d', strtotime($current_date . ", +1 day"));
        }

        $qb->groupBy('c.childId');

        return $qb->orderBy('c.birthdate', 'ASC')
            ->getQuery()
            ->getResult();
    }

}

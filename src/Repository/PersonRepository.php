<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * PersonRepository class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class PersonRepository extends EntityRepository
{
    /**
     * Returns all the persons in an array
     */
    public function findAll()
    {
        return $this->createQueryBuilder('p')
            ->where('p.suppressed = 0')
            ->orderBy('p.lastname', 'ASC')
            ->addOrderBy('p.firstname', 'ASC')
            ->getQuery()
        ;
    }

    /**
     * Returns all the persons corresponding to the searched term
     */
    public function findAllSearch(string $term)
    {
        return $this->createQueryBuilder('p')
            ->where('LOWER(p.firstname) LIKE :term OR LOWER(p.lastname) LIKE :term')
            ->andWhere('p.suppressed = 0')
            ->orderBy('p.lastname', 'ASC')
            ->addOrderBy('p.firstname', 'ASC')
            ->setParameter('term', '%' . strtolower($term) . '%')
            ->getQuery()
        ;
    }


    /**
     * Returns all the persons corresponding to the searched term
     */
    public function findLastnameByTerm(string $term, $by = "lastname")
    {

        return $this->createQueryBuilder('p')
            ->leftJoin('p.userPersonLink', 'ul')
            ->leftJoin('ul.user', 'u')
            ->select('p.personId as personId, p.firstname as firstname, p.lastname as lastname, p.photo as photo, u.email, u.id as user_id')
            ->where('LOWER(p.'.$by.') LIKE :term')
            ->andWhere('p.suppressed = 0')
            ->orderBy('p.lastname', 'ASC')
            ->addOrderBy('p.firstname', 'ASC')
            ->setParameter('term', strtolower($term))
            ->getQuery()
            ->getScalarResult()
        ;
    }

    /**
     * Returns the person using its user's identifier
     */
    public function findByUserIdentifier(string $identifier)
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.userPersonLink', 'ul')
            ->leftJoin('ul.user', 'u')
            ->where('u.identifier = :identifier')
            ->andWhere('p.suppressed = 0')
            ->setParameter('identifier', $identifier)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

        /**
     * Returns the person using its user's id
     */
    public function findByUserId(int $userId)
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.userPersonLink', 'ul')
            ->leftJoin('ul.user', 'u')
            ->where('u.id = :userId')
            ->andWhere('p.suppressed = 0')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * Returns the person using its user role
     */
    public function findByUserRole(string $role)
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.userPersonLink', 'ul')
            ->leftJoin('ul.user', 'u')
            ->where('u.roles like :role')
            ->andWhere('p.suppressed = 0')
            ->setParameter('role', "%".$role."%")
            ->getQuery()
            ->getResult()
        ;
    }


    /**
     * Returns the person if not suppressed
     */
    public function findOneById($personId)
    {
        return $this->createQueryBuilder('p')
            ->where('p.personId = :personId')
            ->andWhere('p.suppressed = 0')
            ->setParameter('personId', $personId)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }


    public function findByEmail(string $email)
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.userPersonLink', 'ul')
            ->leftJoin('ul.user', 'u')
            ->select('p.personId, p.firstname, p.lastname, p.suppressed, u.email, u.id as user_id, u.identifier')
            ->where('u.email LIKE :email')
            ->setParameter('email', '%' . $email . '%')
            ->orderBy('p.suppressed', 'ASC')
            ->addOrderBy('p.lastname', 'ASC')
            ->getQuery()
            ->getScalarResult()
        ;
    }

    public function findDoublon($letter) {
        $conn = $this->getEntityManager()->getConnection();
        $sql = 'SELECT person_id, lastname, firstname FROM `person` WHERE lastname like "'.$letter.'%" ORDER BY lastname, firstname';
        $stmt = $conn->prepare($sql);
        return $stmt->executeQuery()->fetchAllAssociative();
    }
}

<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * ProductRepository class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 */
class ProductRepository extends EntityRepository
{
    /**
     * Returns all the products in an array
     */
    public function findAll()
    {
        return $this->createQueryBuilder('p')
            ->where('p.suppressed = 0')
            ->andWhere('p.child IS NULL')
            ->orderBy('p.nameFr', 'ASC')
            ->getQuery()
        ;
    }

    /**
     * Returns all the products not archived in an array
     */
    public function findNotArchived()
    {
        return $this->createQueryBuilder('p')
            ->where('p.suppressed = 0')
            ->andWhere('p.visibility <> \'archived\'')
            ->andWhere('p.child IS NULL')
            ->orderBy('p.nameFr', 'ASC')
            ->getQuery()
        ;
    }




    /**
     * Returns all the products linked to a child in an array
     */
    public function findAllByChild($childId)
    {
        return $this->createQueryBuilder('p')
            ->where('p.suppressed = 0')
            ->andWhere('p.child = :childId')
            ->setParameter('childId', $childId)
            ->orderBy('p.nameFr', 'ASC')
            ->getQuery()
        ;
    }

    /**
     * Returns all the products corresponding to the searched term
     */
    public function findAllSearch(string $term)
    {
        return $this->createQueryBuilder('p')
            ->where('LOWER(p.nameFr) LIKE :term')
            ->andWhere('p.suppressed = 0')
            ->andWhere('p.child IS NULL')
            ->orderBy('p.nameFr', 'ASC')
            ->setParameter('term', '%' . strtolower($term) . '%')
            ->getQuery()
        ;
    }

    /**
     * Returns the product if not suppressed
     */
    public function findOneById($productId)
    {
        return $this->createQueryBuilder('p')
            ->select('p', 'c')
            ->leftJoin('p.components', 'c')
            ->where('p.productId = :productId')
            ->andWhere('p.suppressed = 0')
            ->setParameter('productId', $productId)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }


    /**
     * Returns the product if not suppressed
     */
    public function findListNotArchived()
    {
        return $this->createQueryBuilder('p')
            ->select('p.nameFr, f.name as familyname, p.visibility')
            ->leftJoin('p.family', 'f')
            ->where('p.visibility <> :status')
            ->andWhere('p.suppressed = 0')
            ->setParameter('status', 'archived')
            ->getQuery()
            ->getScalarResult()
        ;
    }


    public function getProductByCategoryName($categoryName)
    {
        return $this->createQueryBuilder('p')
            ->join('p.categories', 'pcl')
            ->join('pcl.category', 'c')
            ->where('c.name = :categoryName')
            ->andWhere('p.suppressed = 0')
            ->andWhere("p.visibility = 'frontvisibility' ")
            ->setParameter('categoryName', $categoryName)
            ->getQuery()
            ->getResult()
        ;
    }

}

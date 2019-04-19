<?php

namespace App\Repository;

use App\Entity\Available;
use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Doctrine\DBAL\LockMode;

class AvailableRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Available::class);
    }

    public function findByCategoryDateTomorrow(Category $category, $startdt, $totalPax){
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT a
            FROM App\Entity\Available a
            WHERE a.category = :category AND a.stock >= :stock AND a.datetimestart >= :sdate
            ORDER BY a.datetimestart ASC')
            ->setParameter('category', $category)
            ->setParameter('sdate', $startdt)
            ->setParameter('stock', $totalPax);
        return $query->execute();
    }

    public function findAvailableFromInterval($start, $end){
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT a
            FROM App\Entity\Available a
            WHERE a.datetimeend <= :end
            AND a.datetimestart >= :start
            ORDER BY a.datetimestart ASC')
            ->setParameter('start', $start->format('Y-m-d'))
            ->setParameter('end', $end->format('Y-m-d'));
        return $query->execute();
    }


     public function findAvailableFromIntervalAndCategory($start = null, $end = null, $category){

        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT a
            FROM App\Entity\Available a
            WHERE a.datetimeend <= :end
            AND a.datetimestart >= :start
            AND a.category = :category
            ORDER BY a.datetimestart ASC')
            ->setParameter('start', $start->format('Y-m-d H:i:s'))
            ->setParameter('end', $end->format('Y-m-d H:i:s'))
            ->setParameter('category', $category->getId());
        return $query->execute();
    }



}

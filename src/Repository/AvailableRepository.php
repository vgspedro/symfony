<?php

namespace App\Repository;

use App\Entity\Available;
use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

class AvailableRepository extends ServiceEntityRepository
{
     public function __construct(ManagerRegistry $registry)
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


    public function findAvailableByDatesAndCategoryBookingJoin($start, $end, $category){

        $conn = $this->getEntityManager()->getConnection();
                        
        $sql = 'SELECT DISTINCT (a.id) AS id 
                FROM available a
                INNER JOIN booking b 
                ON a.id = b.available_id           
                WHERE a.category_id = :c
                AND a.datetimeend <= :e
                AND a.datetimestart >= :s';

        $stmt = $conn->prepare($sql);
        $stmt->execute(array('s' => $start->format('Y-m-d H:i:s'), 'e' => $end->format('Y-m-d H:i:s') ,'c' => $category->getId()));
        return $stmt->fetchAll();
    }


    public function findAvailableWithDatesAndCategoryNoBookingJoin($start, $end, $available, $category){

        $conn = $this->getEntityManager()->getConnection();
        $sql = 'SELECT a.id AS id
                FROM available a
                WHERE a.datetimestart >= :s
                AND a.datetimeend <= :e
                AND a.category_id = :c
                AND a.id NOT IN ('. implode(",",$available) .')';

        $stmt = $conn->prepare($sql);
        $stmt->execute(array('s' => $start->format('Y-m-d H:i:s'), 'e' => $end->format('Y-m-d H:i:s'), 'c' => $category->getId()));
        return $stmt->fetchAll();
    }


    public function findCategoryAvailabilityByWeekAndPax(Category $category, $start, $end, $next, $total_pax){

        $em = $this->getEntityManager();

        $query = $em->createQuery(
            'SELECT a
            FROM App\Entity\Available a
            WHERE a.category = :category
            AND a.stock >= :total_pax
            AND a.datetimestart >= :start
            ORDER BY a.datetimestart ASC')
            ->setParameter('start', $start->format('Y-m-d H:i:s'))
            ->setParameter('total_pax', $total_pax)
            ->setParameter('category', $category);
                
        return $query->execute();
    }

}

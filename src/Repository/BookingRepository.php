<?php

namespace App\Repository;

use App\Entity\Booking;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class BookingRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Booking::class);
    }

    /*
    get the clients with credit card that pass 15 days after the event
    */
    public function getClientCreditCardData($deadline){
        $dql = 'SELECT c, b
            FROM App\Entity\Booking b
            JOIN b.client c
            WHERE c.cardNr IS NOT NULL 
            AND c.cardNr <> :empty
            AND b.dateEvent <= :end';
        
        $query = $this->getEntityManager()->createQuery($dql)
          ->setParameter('end', $deadline)
          ->setParameter('empty','');
        
        return $query->getResult();
    }

    public function dashboardValues()
    {
        $today = new \Datetime('now');
        $tomorrow = new \Datetime('tomorrow');

        $conn = $this->getEntityManager()->getConnection();

        $sql = "SELECT  b.status, COUNT(b.status) AS count, COUNT(b.id) AS total
            FROM booking b
            WHERE b.date_event = :date
            GROUP BY b.status";

        $stmt = $conn->prepare($sql);
        $stmt->bindValue('date', $today->format('d/m/Y'));
        $stmt->execute();
        $day0 = $stmt->fetchAll();


        $sql = "SELECT b.status, COUNT(b.status) AS count, COUNT(b.id) AS total 
            FROM booking b
            WHERE b.date_event = :date
            GROUP BY b.status";

        $stmt = $conn->prepare($sql);
        $stmt->bindValue('date', $tomorrow->format('d/m/Y'));
        $stmt->execute();
        $day1 = $stmt->fetchAll();

        $dashRowTree = $this->createQueryBuilder('b')
            ->select('SUM(b.adult) as adults, SUM(b.children) AS childrens, SUM(b.baby) AS babies, SUM(b.adult) + SUM(b.children) + SUM(b.baby) AS total')
            ->getQuery()
            ->getResult()
        ;
        return array('day0' => $day0, 'day1' => $day1, 'total' => $dashRowTree); 
    }
  

    public function bookingFilter($start, $end){

        if ($start && $end)

            $booking = $this->createQueryBuilder('b')
                ->andWhere('b.dateEvent >= :start')
                ->andWhere('b.dateEvent <= :end')
                ->setParameter('start', $start)
                ->setParameter('end', $end)
                ->orderBy('b.dateEvent, b.timeEvent', 'ASC')
                ->getQuery();

        else if($start){
    
            $booking = $this->createQueryBuilder('b')
                ->andWhere('b.dateEvent = :start')
                ->setParameter('start', $start)
                ->orderBy('b.dateEvent, b.timeEvent', 'ASC')
                ->getQuery();
        }

        else if($end){
    
            $booking = $this->createQueryBuilder('b')
                ->andWhere('b.dateEvent = :end')
                ->setParameter('end', $end)
                ->orderBy('b.dateEvent, b.timeEvent', 'ASC')
                ->getQuery();
        }

        return $booking->execute();

    }

    public function findDeleteCategory(Category $category){

        $query = $this->getEntityManager()->createQuery(
                'SELECT b, a, c,
                FROM App\Entity\Booking b
                JOIN b.available a
                JOIN a.category c
                WHERE a.category = :category')
                ->setParameter('category', $category);
        return $query->getSingleScalarResult();
    }

}

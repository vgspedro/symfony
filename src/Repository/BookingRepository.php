<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\Available;
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
        $stmt->bindValue('date', $today->format('Y-m-d'));
        $stmt->execute();
        $day0 = $stmt->fetchAll();


        $sql = "SELECT b.status, COUNT(b.status) AS count, COUNT(b.id) AS total 
            FROM booking b
            WHERE b.date_event = :date
            GROUP BY b.status";

        $stmt = $conn->prepare($sql);
        $stmt->bindValue('date', $tomorrow->format('Y-m-d'));
        $stmt->execute();
        $day1 = $stmt->fetchAll();

        $sql = "SELECT c.name_pt AS category, COUNT(c.name_pt) AS total 
            FROM booking b
            JOIN available a
            ON b.available_id = a.id
            JOIN category c
            ON a.category_id = c.id
            GROUP BY c.name_pt";

        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $c0 = $stmt->fetchAll();
        
        $chart0 = array();
        $tmp = ['Categoria', 'Total'];
        array_push($chart0, $tmp);
        
        foreach ($c0 as $key => $value) {
            $tmp = array( $value['category'], (int)$value['total']); 
            array_push($chart0, $tmp);
        }

        $sql = "SELECT b.status AS status, COUNT(b.status) AS total 
            FROM booking b
            WHERE 1 = 1
            GROUP BY b.status
            ORDER BY b.status ASC";

        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $c1 = $stmt->fetchAll();
        
        $chart1 = array();
        $tmp = ['Status', 'Total'];
        array_push($chart1, $tmp);
        
        foreach ($c1 as $key => $value) {
            $tmp = array(strtoupper($value['status']), (int)$value['total']); 
            array_push($chart1, $tmp);
        }

        $charts = array();
        array_push($charts, $chart0, $chart1);

        return array('day0' => $day0, 'day1' => $day1, 'chart' => $charts); 
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


    public function findDeleteCategory($category){

        $query = $this->getEntityManager()->createQuery(
                'SELECT b, a
                FROM App\Entity\Booking b
                JOIN b.available a
                WHERE a.category = :category')
                ->setParameter('category', $category);
        return $query->getResult();

    }



    private function objectToArray( $object )
    {
        if( !is_object( $object ) && !is_array( $object ) )
        {
            return $object;
        }
        if( is_object( $object ) )
        {
            $object = get_object_vars( $object );
        }
        return array_map( array($this, 'objectToArray'), $object );
    }

}

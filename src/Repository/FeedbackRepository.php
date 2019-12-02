<?php

namespace App\Repository;

use App\Entity\Feedback;
use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

class FeedbackRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Feedback::class);
    }

    public function getCategoryScore()
    {

        $dql = ' SELECT AVG(f.rate) AS average, COUNT(f.id) AS reviews, c.id, c.namePt
            FROM App\Entity\Feedback f
            JOIN f.booking AS b
            JOIN b.available AS a
            JOIN a.category AS c
            WHERE f.active = 1
            AND b.status = :confirmed
            GROUP BY c.id';
        $query = $this->getEntityManager()->createQuery($dql)
        ->setParameter('confirmed','confirmed');

        return $query->getResult();
    }
}

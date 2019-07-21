<?php

namespace App\Repository;

use App\Entity\Feedback;
use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class FeedbackRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Feedback::class);
    }

    public function getCategoryScore(Category $category)
    {


/*SELECT SUM(f.rate), count(f.id), c.id FROM feedback AS f 
JOIN booking as b ON b.id = f.booking_id
JOIN available as a ON a.id = b.available_id
JOIN category as c ON c.id = a.category_id
WHERE c.id = 16
*/


        $dql = ' SELECT SUM(f.rate), COUNT(f.rate)
            FROM feedback AS f
            JOIN f.booking_id AS b
            JOIN b.available_id AS c
            WHERE c_id = :cat';
        $query = $this->getEntityManager()->createQuery($dql)
          ->setParameter('cat', $category->getId());

        return $query->getResult();
    }
}

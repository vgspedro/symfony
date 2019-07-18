<?php

namespace App\Repository;

use App\Entity\Feedback;
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
        return $this->createQueryBuilder('f')
            ->where('f.active = 1')
            ->andWhere('f.visible = 1')
            ->getQuery()
            ->getResult()
        ;
    }
}

<?php

namespace App\Repository;

use App\Entity\Available;
use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class AvailableRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Available::class);
    }

    public function findByCategoryDateTomorrow(Category $category, $startdt, $totalPax){
        return $this->createQueryBuilder('a')
            ->where('a.stock >= :stock')
            ->setParameter('stock', $totalPax)
            ->andWhere('a.datetime >= :datetime')
            ->setParameter('datetime', $startdt)
            ->andWhere('a.category = :category')
            ->setParameter('category', $category)

            ->getQuery()
            ->getResult();
    }
}

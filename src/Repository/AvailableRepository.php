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
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT a
            FROM App\Entity\Available a
            WHERE a.category = :category AND a.stock >= :stock 
            ORDER BY a.datetimestart ASC')
            ->setParameter('category', $category)
            ->setParameter('stock', $totalPax);
        return $query->execute();
    }
}

<?php

namespace App\Repository;

use App\Entity\Promocode;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;


class PromocodeRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)

    //public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Promocode::class);
    }

    /*
    public function findBySomething($value)
    {
        return $this->createQueryBuilder('b')
            ->where('b.something = :value')->setParameter('value', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */
}

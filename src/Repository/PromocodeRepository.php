<?php

namespace App\Repository;

use App\Entity\Promocode;
use App\Entity\Available;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;


class PromocodeRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)

    //public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Promocode::class);
    }

    public function findbyCodeAndCategoryAndPeriod(Available $available, string $code){

        $em = $this->getEntityManager();
        $query = $em->createQuery(
        'SELECT p
            FROM App\Entity\Promocode p
            WHERE (p.start <= :b_date AND p.end >= :b_date) AND p.category = :category AND p.code = :code AND p.isActive = :status')
            ->setParameter('category', $available->getCategory())
            ->setParameter('status', true)
            ->setParameter('b_date', $available->getDatetimeStart()->format('Y-m-d'))
            ->setParameter('code', $code);
            $results = $query->execute();
            return empty($results) ? null : $results[0]; 
    }

}

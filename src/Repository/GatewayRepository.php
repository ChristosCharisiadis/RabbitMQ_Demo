<?php

namespace App\Repository;

use App\Entity\Gateway;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Gateway|null find($id, $lockMode = null, $lockVersion = null)
 * @method Gateway|null findOneBy(array $criteria, array $orderBy = null)
 * @method Gateway[]    findAll()
 * @method Gateway[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GatewayRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Gateway::class);
    }

    // /**
    //  * @return Gateway[] Returns an array of Gateway objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('g.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Gateway
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

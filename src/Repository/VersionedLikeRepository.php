<?php

namespace App\Repository;

use App\Entity\VersionedLike;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method VersionedLike|null find($id, $lockMode = null, $lockVersion = null)
 * @method VersionedLike|null findOneBy(array $criteria, array $orderBy = null)
 * @method VersionedLike[]    findAll()
 * @method VersionedLike[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VersionedLikeRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, VersionedLike::class);
    }

//    /**
//     * @return VersionedLike[] Returns an array of VersionedLike objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?VersionedLike
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

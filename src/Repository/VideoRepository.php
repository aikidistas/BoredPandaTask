<?php

namespace App\Repository;

use App\Entity\Video;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Video|null find($id, $lockMode = null, $lockVersion = null)
 * @method Video|null findOneBy(array $criteria, array $orderBy = null)
 * @method Video[]    findAll()
 * @method Video[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VideoRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Video::class);
    }

    /**
     * @return Video[] Returns an array of Video objects
     */
    public function findByTag($tag)
    {
        return $this->createQueryBuilder('v')
            ->innerJoin('v.tags', 't')
            ->andWhere('t.text = :tag')
            ->setParameter('tag', $tag)
            ->getQuery()
            ->getResult()
        ;
    }
}

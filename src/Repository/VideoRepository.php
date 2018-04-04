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
 * @codeCoverageIgnore
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

    /**
     * @return Video[] Returns an array of Video objects
     */
    public function findByTagAndPerformance($tagId = null, $performance = null)
    {
        $query = $this->createQueryBuilder('v');

        if (!is_null($tagId)) {
            $query
                ->innerJoin('v.tags', 't')
                ->andWhere('t.id = :tagId')
                ->setParameter('tagId', $tagId);
        }

        if (!is_null($performance)) {
            $query
                ->andWhere('v.performance >= :performance')
                ->setParameter('performance', $performance);
        }

        return $query
            ->getQuery()
            ->getResult()
        ;
    }

    public function selectChannelFirstHourViewsMedian(string $channelId)
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = 'SELECT AVG(t1.first_hour_views) as median_val FROM (
                    SELECT @rownum:=@rownum+1 AS `row_number`, v.first_hour_views
                    FROM video v,  (SELECT @rownum:=0) r
                    WHERE
                        v.channel_id = :channel_id 
                    ORDER BY v.first_hour_views
                ) AS t1, 
                (
                    SELECT count(*) AS total_rows
                    FROM video v
                    WHERE
                        v.channel_id = :channel_id
                ) AS t2
                WHERE 1
                AND t1.row_number in ( floor((total_rows+1)/2), floor((total_rows+2)/2) );
        ';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['channel_id' => $channelId]);

        // returns an array of arrays (i.e. a raw data set)
        $result = $stmt->fetchAll();

        if (sizeof($result) > 0 && sizeof($result[0]) > 0) {
            return $result[0]['median_val'];
        }

        return 0;
    }
}

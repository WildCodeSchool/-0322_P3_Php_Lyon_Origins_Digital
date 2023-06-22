<?php

namespace App\Repository;

use App\Entity\Video;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Video>
 *
 * @method Video|null find($id, $lockMode = null, $lockVersion = null)
 * @method Video|null findOneBy(array $criteria, array $orderBy = null)
 * @method Video[]    findAll()
 * @method Video[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VideoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Video::class);
    }

    public function save(Video $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Video $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findLatestVideos(): array
    {
        $now = new DateTimeImmutable();
        
        return $this->createQueryBuilder('v')
        ->orderBy('v.postDate', 'DESC')
        ->setMaxResults(15)
        ->where('v.postDate < :today')
        ->setParameter('today', $now, Types::DATETIME_IMMUTABLE)
        ->getQuery()
        ->getResult();
    }
    
    public function findVideosBy(
        string $order,
        string $filterEntity = null,
        string $filterName = null,
        string $filterValue = null,
        string $orderBy = 'DESC',
        int $maxResult = 15,
    ) :array
    {
        $now = new DateTimeImmutable();
        
        $filtered = $this->createQueryBuilder('v')

        ->join('v.'.$filterEntity, 'f', 'WITH', 'f.' . $filterName . ' = :filterValue')
        ->setParameter('filterValue', $filterValue)
        
        ->where('v.postDate < :today')
        ->setParameter('today', $now, Types::DATETIME_IMMUTABLE)
        
        ->orderBy('v.'.$order, $orderBy)
        ->setMaxResults($maxResult)
        
        ->getQuery()
        ;

        $unfiltered = $this->createQueryBuilder('v')

        ->where('v.postDate < :today')
        ->setParameter('today', $now, Types::DATETIME_IMMUTABLE)
        
        ->orderBy('v.'.$order, $orderBy)
        ->setMaxResults($maxResult)
        
        ->getQuery()
        ;

        if ($filterEntity == null) return $unfiltered->getResult();
        return $filtered->getResult();
    }

    //    /**
    //     * @return Video[] Returns an array of Video objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('v')
    //            ->andWhere('v.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('v.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Video
    //    {
    //        return $this->createQueryBuilder('v')
    //            ->andWhere('v.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}

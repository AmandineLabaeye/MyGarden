<?php

namespace App\Repository;

use App\Entity\LikeArticle;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method LikeArticle|null find($id, $lockMode = null, $lockVersion = null)
 * @method LikeArticle|null findOneBy(array $criteria, array $orderBy = null)
 * @method LikeArticle[]    findAll()
 * @method LikeArticle[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LikeArticleRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, LikeArticle::class);
    }

    // /**
    //  * @return LikeArticle[] Returns an array of LikeArticle objects
    //  */
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
    public function findOneBySomeField($value): ?LikeArticle
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

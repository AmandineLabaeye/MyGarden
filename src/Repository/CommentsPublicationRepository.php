<?php

namespace App\Repository;

use App\Entity\CommentsPublication;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method CommentsPublication|null find($id, $lockMode = null, $lockVersion = null)
 * @method CommentsPublication|null findOneBy(array $criteria, array $orderBy = null)
 * @method CommentsPublication[]    findAll()
 * @method CommentsPublication[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentsPublicationRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, CommentsPublication::class);
    }

    // /**
    //  * @return CommentsPublication[] Returns an array of CommentsPublication objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CommentsPublication
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

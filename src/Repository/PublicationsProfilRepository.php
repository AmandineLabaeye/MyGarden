<?php

namespace App\Repository;

use App\Entity\PublicationsProfil;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method PublicationsProfil|null find($id, $lockMode = null, $lockVersion = null)
 * @method PublicationsProfil|null findOneBy(array $criteria, array $orderBy = null)
 * @method PublicationsProfil[]    findAll()
 * @method PublicationsProfil[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PublicationsProfilRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, PublicationsProfil::class);
    }

    // /**
    //  * @return PublicationsProfil[] Returns an array of PublicationsProfil objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PublicationsProfil
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

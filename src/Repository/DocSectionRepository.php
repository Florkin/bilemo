<?php

namespace App\Repository;

use App\Entity\DocSection;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DocSection|null find($id, $lockMode = null, $lockVersion = null)
 * @method DocSection|null findOneBy(array $criteria, array $orderBy = null)
 * @method DocSection[]    findAll()
 * @method DocSection[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DocSectionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DocSection::class);
    }

    // /**
    //  * @return DocSection[] Returns an array of DocSection objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?DocSection
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

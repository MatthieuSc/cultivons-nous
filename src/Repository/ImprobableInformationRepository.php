<?php

namespace App\Repository;

use App\Entity\ImprobableInformation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ImprobableInformation|null find($id, $lockMode = null, $lockVersion = null)
 * @method ImprobableInformation|null findOneBy(array $criteria, array $orderBy = null)
 * @method ImprobableInformation[]    findAll()
 * @method ImprobableInformation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ImprobableInformationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ImprobableInformation::class);
    }

    // /**
    //  * @return ImprobableInformation[] Returns an array of ImprobableInformation objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ImprobableInformation
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

        /**
     * Returns random improbable information
     */
    public function findOneRandomInfo()
    {
        $query = $this->getEntityManager()->createQuery("
            SELECT a FROM App\Entity\ImprobableInformation a ORDER BY RAND() 
        ")
            ->setMaxResults(1);

            return $query->getResult();

    }
}

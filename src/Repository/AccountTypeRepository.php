<?php

namespace App\Repository;

use App\Entity\AccountType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AccountType>
 *
 * @method AccountType|null find($id, $lockMode = null, $lockVersion = null)
 * @method AccountType|null findOneBy(array $criteria, array $orderBy = null)
 * @method AccountType[]    findAll()
 * @method AccountType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AccountTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AccountType::class);
    }

//    /**
//     * @return AccountType[] Returns an array of AccountType objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?AccountType
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

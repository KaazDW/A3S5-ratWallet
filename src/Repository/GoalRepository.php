<?php

namespace App\Repository;

use App\Entity\Goal;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Goal>
 *
 * @method Goal|null find($id, $lockMode = null, $lockVersion = null)
 * @method Goal|null findOneBy(array $criteria, array $orderBy = null)
 * @method Goal[]    findAll()
 * @method Goal[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GoalRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Goal::class);
    }


    public function findOneByAccountId(int $accountId): ?float
    {
        try {
            $query = $this->createQueryBuilder('g')
                ->select('g.targetAmount')
                ->join('g.accounts', 'a')
                ->andWhere('a.id = :accountId')
                ->setParameter('accountId', $accountId)
                ->getQuery();

            dump($query->getSQL());

            $result = $query->getSingleScalarResult();

            dump($result);

            return $result ?? 0.0;
        } catch (\Exception $e) {
            dump($e->getMessage());
            return null;
        }
    }




//    /**
//     * @return Goal[] Returns an array of Goal objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('g.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Goal
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

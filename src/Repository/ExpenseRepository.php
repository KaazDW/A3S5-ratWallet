<?php

namespace App\Repository;

use App\Entity\Account;
use App\Entity\Category;
use App\Entity\Expense;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Expense>
 *
 * @method Expense|null find($id, $lockMode = null, $lockVersion = null)
 * @method Expense|null findOneBy(array $criteria, array $orderBy = null)
 * @method Expense[]    findAll()
 * @method Expense[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExpenseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Expense::class);
    }

    public function getTotalExpenseAmount(int $id): ?float
    {
        try {
            $query = $this->createQueryBuilder('e')
                ->select('SUM(e.amount) as totalExpenseAmount')
                ->andWhere('e.account = :account_id')
                ->setParameter('account_id', $id)
                ->getQuery();

            // dump($query->getSQL());

            $result = $query->getSingleScalarResult();

            // dump($result);

            return $result ?? 0.0;
        } catch (\Exception $e) {
            // dump($e->getMessage());
            return null;
        }
    }


    public function getSumByCategoryAndAccount(Category $category, Account $account): float
    {
        return $this->createQueryBuilder('e')
            ->select('SUM(e.amount) as sum')
            ->where('e.category = :category')
            ->andWhere('e.account = :account')
            ->setParameter('category', $category)
            ->setParameter('account', $account)
            ->getQuery()
            ->getSingleScalarResult() ?? 0;
    }

    public function getMonthlyExpensesByCategory($user)
    {
        $query = $this->createQueryBuilder('e')
            ->join('e.account', 'a')
            ->andWhere('a.user = :user')
            ->setParameter('user', $user)
            // Add more conditions and joins as needed
            ->getQuery();

        return $query->getResult();
    }


//    /**
//     * @return Expense[] Returns an array of Expense objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('e.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Expense
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

<?php

namespace App\Repository;

use App\Entity\Account;
use App\Entity\Category;
use App\Entity\Expense;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
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


    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function getSumByCategoryAndAccount(Category $category, Account $account)
    {
        return $this->createQueryBuilder('e')
            ->select('e.date as month', 'SUM(e.amount) as sum')
            ->where('e.category = :category')
            ->andWhere('e.account = :account')
            ->setParameter('category', $category)
            ->setParameter('account', $account)
            ->groupBy('month')
            ->getQuery()
            ->getResult();
    }


    public function getMonthlyExpenseSumByCategory(Account $account)
    {
        return $this->createQueryBuilder('e')
            ->select('MONTH(e.date) as month', 'SUM(e.amount) as sum')
            ->where('e.account = :account')
            ->setParameter('account', $account)
            ->groupBy('month')
            ->getQuery()
            ->getResult();
    }

   /* public function getMonthlyExpensesByCategory(int $accountId ): array
    {
        $queryBuilder = $this->createQueryBuilder('e')
            ->select('SUBSTRING(e.date, 1, 4) as year, SUBSTRING(e.date, 6, 2) as month, c.label as category, SUM(e.amount) as totalAmount, b.amount as budgetAmount')
            ->leftJoin('e.category', 'c')
            ->leftJoin('c.budgets', 'b') // Ajouter une jointure avec la table Budget
            ->leftJoin('b.account', 'a') // Ajouter une jointure avec la table Account
            ->where('a.id = :accountId') // Filter by the account ID
            ->setParameter('accountId', $accountId)
            ->groupBy('year, month, category, budgetAmount') // Ajouter budgetAmount à la clause GROUP BY
            ->orderBy('year, month, category');

        return $queryBuilder->getQuery()->getResult();
    }*/

    public function getMonthlyExpensesByCategoryy(int $accountId, int $currentYear ): array
    {
        $queryBuilder = $this->createQueryBuilder('e')
            ->select('SUBSTRING(e.date, 1, 4) as year, c.label as category, SUM(e.amount) as totalAmount')
            ->leftJoin('e.category', 'c')
            ->andWhere('SUBSTRING(e.date, 1, 4) = :currentYear')
            ->setParameter('currentYear', $currentYear)
            ->groupBy('year, category')
            ->orderBy('year, category');

        return $queryBuilder->getQuery()->getResult();
    }

    public function getMonthlyExpensesByCategory(int $accountId, int $currentYear): array
    {
        $queryBuilder = $this->createQueryBuilder('e')
            ->select('SUBSTRING(e.date, 1, 4) as year, c.label as category, SUM(e.amount) as totalAmount, b.amount as budgetAmount')
            ->leftJoin('e.category', 'c')
            ->leftJoin('c.budgets', 'b')
            ->andWhere('SUBSTRING(e.date, 1, 4) = :currentYear')
            ->setParameter('currentYear', $currentYear)
            ->groupBy('year, category, budgetAmount')
            ->orderBy('year, category');

        return $queryBuilder->getQuery()->getResult();
    }
}
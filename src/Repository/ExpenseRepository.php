<?php

namespace App\Repository;

use App\Entity\Account;
use App\Entity\Category;
use App\Entity\Expense;
use DateTimeInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\VarDumper\VarDumper;

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

}

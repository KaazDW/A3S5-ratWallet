<?php
namespace App\Controller;

use App\Entity\Category;
use App\Entity\Expense;
use App\Entity\Account;
use App\Entity\Goal;
use App\Entity\Income;
use DateTimeInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class ChartController extends AbstractController
{
   #[Route('/chart/datacategory/{id}', name: 'datacategory')]
    public function getDataCategory(int $id, EntityManagerInterface $entityManager)
    {
        // Récupérer le référentiel (repository) pour l'entité Expense
        $expenseRepository = $entityManager->getRepository(Expense::class);
        $queryBuilder = $expenseRepository->createQueryBuilder('e')
            ->select('c.label AS category_label', 'SUM(e.amount) AS total_amount')
            ->join('e.category', 'c')
            ->join('e.account', 'a')
            ->where('a.id = :account')
            ->groupBy('e.category');

        // Exécuter la requête
        $query = $queryBuilder->getQuery();
        $query->setParameter('account', $id);
        $result = $query->getResult();

        // Exécuter la requête
        $chartData = [
            'categories' => [],
            'series' => [],
        ];

        foreach ($result as $row) {
            $chartData['categories'][] = $row['category_label'];
            $chartData['series'][] = $row['total_amount'];
        }

        return new JsonResponse(['data' => $chartData]);
        // pour voir les données renvoyées, allez à : http://127.0.0.1:8000/chart/datacategory/{id}
    }


    #[Route('/chart/datahistory', name: 'datahistorybalance')]
    public function getDataHistoryBalance(EntityManagerInterface $entityManager, Security $security): JsonResponse
    {
        $user = $security->getUser();

        $accountRepository = $entityManager->getRepository(Account::class);

        $accounts = $accountRepository->findBy(['userID' => $user]);

        $historyData = [];
        foreach ($accounts as $account) {
            $history = $account->getHistories();
            $historyData[] = [
                'accountName' => $account->getNameAccount(),
                'amountHistory' => array_map(function ($entry) {
                    return $entry->getHistoryBalance();
                }, $history->toArray()),
            ];
        }

        return new JsonResponse($historyData);
    }

    #[Route('/chart/goal-progress/{id}', name: 'goal_progress')]
    public function getDataGoalProgress(int $id, EntityManagerInterface $entityManager, Security $security): JsonResponse
    {
        $user = $security->getUser();
        $accountRepository = $entityManager->getRepository(Account::class);
        $account = $accountRepository->find($id);

        if (!$account || $account->getUserID() !== $user) {
            return new JsonResponse(['error' => 'Compte non trouvé.'], 404);
        }

        $balance = $account->getBalance();
        $goal = $account->getGoal();
        $targetAmount = 0;

        $progress = 0;
        if ($goal) {
            $targetAmount = $goal->getTargetAmount();
            if ($targetAmount > 0) {
                $progress = ($balance / $targetAmount) * 100;
            }
        }

        return new JsonResponse([
            'accountName' => $account->getNameAccount(),
            'balance' => $balance,
            'targetAmount' => $targetAmount,
            'progress' => $progress,
        ]);
    }

    #[Route('/chart/datahistorybyaccount/{id}', name: 'historyBalanceAccount')]
    public function getDataHistoryBalanceById(EntityManagerInterface $entityManager, Security $security, int $id): JsonResponse
    {
        $user = $security->getUser();

        $accountRepository = $entityManager->getRepository(Account::class);

        $account = $accountRepository->findOneBy(['userID' => $user, 'id' => $id]);

        if (!$account) {
            throw $this->createNotFoundException('Compte non trouvé');
        }

        $history = $account->getHistories();

        $historyData = [
            'accountName' => $account->getNameAccount(),
            'amountHistory' => array_map(function ($entry) {
                return [
                    'x' => $entry->getDate()->getTimestamp() * 1000, // Convertir la date en millisecondes UNIX
                    'y' => $entry->getHistoryBalance(),
                ];
            }, $history->toArray()),
        ];

        return new JsonResponse([$historyData]);
    }



    #[Route('/chart/expense-sum/{id}', name: 'expenseSumByCategory')]
    public function getExpenseSumByCategory(EntityManagerInterface $entityManager, Security $security, int $id): JsonResponse
    {
        $user = $security->getUser();

        $accountRepository = $entityManager->getRepository(Account::class);
        $expenseRepository = $entityManager->getRepository(Expense::class);
        $categoryRepository = $entityManager->getRepository(Category::class);

        $account = $accountRepository->findOneBy(['userID' => $user, 'id' => $id]);

        if (!$account) {
            throw $this->createNotFoundException('Compte non trouvé');
        }

        // Retrieve all categories from the database
        $categories = $categoryRepository->findAll(); // Retrieve all categories from the database

        // Retrieve monthly expense sums by category
        $sum = $expenseRepository->getSumByCategoryAndAccount($category, $account);

        // Extract months and sums from the result
        $months = array_column($monthlyExpenseSums, 'month');
        $sums = array_column($monthlyExpenseSums, 'sum');

        $response = [
            'categories' => array_map(fn(Category $category) => $category->getLabel(), $categories),
            'expenseSums' => $sums,
            'months' => $months,
        ];

        return new JsonResponse($response);
    }
}

 $expenseSums = [];
        foreach ($categories as $category) {
            $sum = $expenseRepository->getSumByCategoryAndAccount($category, $account);
            $expenseSums[] = $sum ?? 0;
        }
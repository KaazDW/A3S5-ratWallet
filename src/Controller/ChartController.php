<?php
namespace App\Controller;

use App\Entity\Category;
use App\Entity\Expense;
use App\Entity\Account;
use App\Entity\Goal;
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
            ->join('e.category', 'c') // Utilisation de la relation avec la table Category
            ->join('e.account', 'a') // Utilisation de la relation avec la table Account
            ->where('a.id = :account')
            ->groupBy('e.category');

        // Exécuter la requête
        $query = $queryBuilder->getQuery();
        $query->setParameter('account', $id);
        $result = $query->getResult();

        // Formater les résultats pour la réponse JSON
        $chartData = [];
        foreach ($result as $row) {
            $chartData[] = [
                'categories' => $row['category_id'],
                'series' => $row['totalAmount'],
            ];
        }

        return new JsonResponse($chartData);
        // pour voir les données renvoyées, allez à : http://127.0.0.1:8000/chart/datacategory/{id}
    }


    #[Route('/goal-progress/{id}', name: 'goal_progress')]
    public function goalProgress(int $id, EntityManagerInterface $entityManager): Response
    {
        $account = $entityManager->getRepository(Account::class)->find($id);

        // Assuming getGoal() returns a single goal
        $goal = $account->getGoal();

        // Access the targetAmount directly
        $totalGoalAmount = $goal ? $goal->getTargetAmount() : 0;

        dump($totalGoalAmount);

        if ($totalGoalAmount === 0) {
            $progress = 0;
        } else {
            $progress = ($account->getBalance() / $totalGoalAmount) * 100;
        }

        dump($progress);

        return $this->render('pages/test.html.twig', [
            'progress' => $progress,
        ]);
    }

    #[Route('/chart/datahistory', name: 'datahistory')]
    public function getDataHistory(EntityManagerInterface $entityManager, Security $security): JsonResponse
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

    #[Route('/chart/dataaccounts/{id}', name: 'dataaccounts')]
    public function getDataAccounts(int $id, EntityManagerInterface $entityManager, Security $security): JsonResponse
    {
        $user = $security->getUser();

        // Récupérer le référentiel (repository) pour l'entité Account
        $accountRepository = $entityManager->getRepository(Account::class);

        // Récupérer le compte par ID
        $account = $accountRepository->find($id);

        if (!$account || $account->getUserID() !== $user) {
            return new JsonResponse(['error' => 'Compte non trouvé.'], 404);
        }

        // Récupérer la balance du compte
        $balance = $account->getBalance();

        // Récupérer le goal lié au compte
        $goal = $account->getGoal();

        // Initialiser le targetAmount à zéro si le compte n'a pas de goal
        $targetAmount = 0;

        // Calculer la progression par rapport à l'objectif
        $progress = 0;
        if ($goal) {
            $targetAmount = $goal->getTargetAmount();
            if ($targetAmount > 0) {
                $progress = ($balance / $targetAmount) * 100;
            }
        }

        // Retourner les données sous forme de JSON
        return new JsonResponse([
            'accountName' => $account->getNameAccount(),
            'balance' => $balance,
            'targetAmount' => $targetAmount,
            'progress' => $progress,
        ]);
    }
}

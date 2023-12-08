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

class ChartController extends AbstractController
{
    #[Route('/chart/datacategory/{id}', name: 'datacategory')]
    public function getDataCategory(int $id, EntityManagerInterface $entityManager)
    {
        // Récupérer le référentiel (repository) pour l'entité Expense
        $expenseRepository = $entityManager->getRepository(Expense::class);
        $queryBuilder = $expenseRepository->createQueryBuilder('e')
            ->select('e.category_id', 'SUM(e.amount) as totalAmount')
            ->where('e.account_id = :account')
            ->setParameter('account', $id)
            ->groupBy('e.category_id');

        // Exécuter la requête
        $query = $queryBuilder->getQuery();
        $result = $query->getResult();

        // Formater les résultats pour la réponse JSON
        $chartData = [];
        foreach ($result as $row) {
            $chartData[] = [
                'category_id' => $row['category_id'],
                'total_amount' => $row['totalAmount'],
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

}

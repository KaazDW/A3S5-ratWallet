<?php
namespace App\Controller;

use App\Entity\Account;
use App\Entity\Goal;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ChartController extends AbstractController
{
    #[Route('/chart/datacategory/', name: 'datacategory')]
    public function getData()
    {
        $chartData = [
            'categories' => ['Category 1', 'Category 2', 'Category 3'],
            'series' => [
                ['name' => 'Series 1', 'data' => [30, 40, 35]],
            ],
        ];

        return new JsonResponse($chartData);
        // for see which data are responses go to : http://127.0.0.1:8000/chart/data
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

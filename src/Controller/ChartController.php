<?php

namespace App\Controller;

use App\Entity\Account;
use App\Entity\History;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ChartController extends AbstractController
{
    #[Route('/chart-line/{id}', name: 'chart_line')]
    public function chart(int $id, EntityManagerInterface $entityManager): Response
    {
        $historyDataResponse = $this->forward('App\Controller\ChartController::chartData', [
            'id' => $id,
            'entityManager' => $entityManager,
        ]);

        $data = json_decode($historyDataResponse->getContent(), true);

        $account = $entityManager->getRepository(Account::class)->find($id);

        return $this->render('pages/chartLine.html.twig', [
            'account' => $account,
            'data' => $data,
        ]);
    }

    #[Route('/chart-data/{id}', name: 'chart_data')]
    public function chartData(int $id, EntityManagerInterface $entityManager): Response
    {
        $historyData = $entityManager->getRepository(History::class)->findBy(['account' => $id]);

        $data = [
            'labels' => [],
            'datasets' => [
                [
                    'label' => 'Balance History',
                    'data' => [],
                    'borderWidth' => 1,
                ],
            ],
        ];

        foreach ($historyData as $entry) {
            $data['labels'][] = $entry->getDate()->format('Y-m-d H:i:s');
            $data['datasets'][0]['data'][] = $entry->getHistoryBalance();
        }

        return $this->json($data);
    }

    /**
     * @Route("/pie-chart", name="pie_chart")
     */
    #[Route('/pie-chart-category', name: 'pie_chart_category')]
    public function pieChartCategory(EntityManagerInterface $entityManager): Response
    {
        $categories = $entityManager->getRepository('App\Entity\Category')->findAll();

        $data = [];
        foreach ($categories as $category) {
            $data['labels'][] = $category->getLabel();
            $data['data'][] = $category->getLabel();
        }

        return $this->json($data);
    }
}

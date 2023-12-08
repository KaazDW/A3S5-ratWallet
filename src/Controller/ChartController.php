<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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
}

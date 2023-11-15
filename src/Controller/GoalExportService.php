<?php

namespace App\Controller;

use App\Repository\GoalRepository;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Exception;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class GoalExportService
{
    private GoalRepository $goalRepository;

    public function __construct(GoalRepository $goalRepository)
    {
        $this->goalRepository = $goalRepository;
    }

    /**
     * @throws Exception
     */
    public function exportToExcel(): string
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Headers
        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Target Amount');
        $sheet->setCellValue('C1', 'Deadline');
        // ... add more headers as needed

        // Data
        $goals = $this->goalRepository->findAll();
        $row = 2;
        foreach ($goals as $goal) {
            $sheet->setCellValue('A' . $row, $goal->getId());
            $sheet->setCellValue('B' . $row, $goal->getTargetAmount());
            $sheet->setCellValue('C' . $row, $goal->getDeadline()->format('Y-m-d'));
            // ... add more columns as needed

            $row++;
        }

        $filename = 'goals_export_' . date('YmdHis') . '.xlsx';

        $writer = new Xlsx($spreadsheet);
        $writer->save($filename);

        return $filename;
    }

}
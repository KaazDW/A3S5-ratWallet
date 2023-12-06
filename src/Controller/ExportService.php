<?php

namespace App\Controller;

use App\Repository\AccountRepository;
use App\Repository\GoalRepository;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Exception;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExportService
{
    private GoalRepository $goalRepository;
    private AccountRepository $accountRepository;

    public function __construct(GoalRepository $goalRepository, AccountRepository $accountRepository)
    {
        $this->goalRepository = $goalRepository;
        $this->accountRepository = $accountRepository;
    }

    /**
     * @throws Exception
     */
    public function exportToExcel(): string
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Headers
        $sheet->setCellValue('A1', 'Account Name');
        $sheet->setCellValue('B1', 'ID');
        $sheet->setCellValue('C1', 'Target Amount');
        $sheet->setCellValue('D1', 'Deadline');
        // ... add more headers as needed

        // Data
        $goals = $this->goalRepository->findAll();  // Assuming Goal is the owning side

        $row = 2;

        foreach ($goals as $goal) {
            // Access the owning side of the association to get the account
            $account = $goal->getAccount();

            $sheet->setCellValue('A' . $row, $account->getNameAccount());
            $sheet->setCellValue('B' . $row, $goal->getId());
            $sheet->setCellValue('C' . $row, $goal->getTargetAmount());
            $sheet->setCellValue('D' . $row, $goal->getDeadline()->format('Y-m-d'));
            // ... add more columns as needed

            $row++;
        }

        $filename = 'goals_export_' . date('YmdHis') . '.xlsx';

        $writer = new Xlsx($spreadsheet);
        $writer->save($filename);

        return $filename;
    }

}

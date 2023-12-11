<?php

namespace App\Controller;

use App\Entity\Account;
use App\Entity\User;
use App\Entity\AccountType;
use App\Entity\Category;
use App\Entity\Currency;
use App\Entity\Goal;
use App\Entity\Income;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\GoalRepository;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Exception;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ExportService extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private GoalRepository $goalRepository;

    public function __construct(GoalRepository $goalRepository, EntityManagerInterface $entityManager)
    {
        $this->goalRepository = $goalRepository;
        $this->entityManager = $entityManager;

    }


    /**
     * @throws Exception
     */
    #[Route('/export_excel', name: 'export_excel')]
    public function exportToExcel(EntityManagerInterface $entityManager): Response
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Get user data JSON from getUserData function
        $userDataResponse = $this->getUserData($entityManager);
        $userData = json_decode($userDataResponse->getContent(), true);
        $userDataJson = json_encode($userData);

        // Get account data JSON from getAccountData function
        $accountDataResponse = $this->getAccountData($entityManager);
        $accountData = json_decode($accountDataResponse->getContent(), true);
        $accountDataJson = json_encode($accountData);

        // Get account data JSON from getAccountData function
        $currencyDataResponse = $this->getCurrencyData($entityManager);
        $currencyData = json_decode($currencyDataResponse->getContent(), true);
        $currencyDataJson = json_encode($currencyData);

        // Get accountType data JSON from getAccountTypeData function
        $accountTypeDataResponse = $this->getAccountTypeData($entityManager);
        $accountTypeData = json_decode($accountTypeDataResponse->getContent(), true);
        $accountTypeDataJson = json_encode($accountTypeData);

        // Get category data JSON from getCategoryData function
        $categoryDataResponse = $this->getCategoryData($entityManager);
        $categoryData = json_decode($categoryDataResponse->getContent(), true);
        $categoryDataJson = json_encode($categoryData);

        // Get goals data JSON from getGoalsData function
        $goalsDataResponse = $this->getGoalData($entityManager);
        $goalsData = json_decode($goalsDataResponse->getContent(), true);
        $goalsDataJson = json_encode($goalsData);

        // Get goals data JSON from getGoalsData function
        $historyDataResponse = $this->getHistoryData($entityManager);
        $historyData = json_decode($historyDataResponse->getContent(), true);
        $historyDataJson = json_encode($historyData);

        // Get goals data JSON from getGoalsData function
        $incomeDataResponse = $this->getExpenseData($entityManager);
        $incomeData = json_decode($incomeDataResponse->getContent(), true);
        $incomeDataJson = json_encode($incomeData);

        // Get goals data JSON from getGoalsData function
        $expenseDataResponse = $this->getExpenseData($entityManager);
        $expenseData = json_decode($expenseDataResponse->getContent(), true);
        $expenseDataJson = json_encode($expenseData);

        // Headers
        $sheet->setCellValue('A1', 'User');
        $sheet->setCellValue('B1', 'Account');
        $sheet->setCellValue('C1', 'Income');
        $sheet->setCellValue('D1', 'Expense');
        $sheet->setCellValue('E1', 'History');
        $sheet->setCellValue('F1', 'Goal');
        $sheet->setCellValue('G1', 'Currency');
        $sheet->setCellValue('H1', 'AccountType');
        $sheet->setCellValue('I1', 'Category');

        $sheet->setCellValue('A2', $userDataJson);
        $sheet->setCellValue('B2', $accountDataJson);
        $sheet->setCellValue('C2', $incomeDataJson);
        $sheet->setCellValue('D2', $expenseDataJson);
        $sheet->setCellValue('E2', $historyDataJson);
        $sheet->setCellValue('F2', $goalsDataJson);
        $sheet->setCellValue('G2', $currencyDataJson);
        $sheet->setCellValue('H2', $accountTypeDataJson);
        $sheet->setCellValue('I2', $categoryDataJson);

        $filename = 'goals_export_' . date('YmdHis') . '.xlsx';

        $writer = new Xlsx($spreadsheet);
        $writer->save($filename);

        $response = new Response();
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment;filename="' . $filename . '"');
        $response->setContent(file_get_contents($filename));

        return $response;
    }

    #[Route('/user-data', name: 'user_data')]
    public function getUserData(EntityManagerInterface $entityManager): JsonResponse
    {
        $user = $this->getUser();


        if ($user instanceof User) {

            $userRepository = $entityManager->getRepository(User::class);

            $userData = $userRepository->findOneBy(['id' => $user->getId()]);

            if ($userData) {
                $userDataArray = [
                    'id' => $userData->getId(),
                    'username' => $userData->getUsername(),
                    'email' => $userData->getEmail(),
                ];
                return new JsonResponse($userDataArray);
            }
        }
        return new JsonResponse([]);
    }

    #[Route('/account-data', name: 'account_data')]
    public function getAccountData(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        if ($user instanceof User) {
            $accountRepository = $entityManager->getRepository(Account::class);

            $accountData = $accountRepository->findOneBy(['userID' => $user->getId()]);

            if ($accountData) {
                $accountDataArray = [
                    'id' => $accountData->getId(),
                    'accountName' => $accountData->getNameAccount(),
                    'balance' => $accountData->getBalance(),
                ];

                return new JsonResponse($accountDataArray);
            }
        }

        return new JsonResponse([]);
    }

    #[Route('/accountType-data', name: 'accountType_data')]
    public function getAccountTypeData(EntityManagerInterface $entityManager): Response
    {
            $accountTypeRepository = $entityManager->getRepository(AccountType::class);
            $accountTypes = $accountTypeRepository->findAll();

            $accountTypesArray = [];
            foreach ($accountTypes as $accountType) {
                $accountTypesArray[] = [
                    'id' => $accountType->getId(),
                    'label' => $accountType->getLabel(),
                ];
            }
                return new JsonResponse($accountTypesArray);
    }

    #[Route('/category-data', name: 'category_data')]
    public function getCategoryData(EntityManagerInterface $entityManager): Response
    {
        $categoryRepository = $entityManager->getRepository(Category::class);
        $category = $categoryRepository->findAll();

        $categoryArray = [];
        foreach ($category as $categories) {
            $categoryArray[] = [
                'id' => $categories->getId(),
                'label' => $categories->getLabel(),
            ];
        }
        return new JsonResponse($categoryArray);
    }

    #[Route('/currency-data', name: 'currency_data')]
    public function getCurrencyData(EntityManagerInterface $entityManager): Response
    {
        $currencyRepository = $entityManager->getRepository(Currency::class);
        $currency = $currencyRepository->findAll();

        $currencyArray = [];
        foreach ($currency as $currencys) {
            $currencyArray[] = [
                'id' => $currencys->getId(),
                'label' => $currencys->getLabel(),
            ];
        }
        return new JsonResponse($currencyArray);
    }

    #[Route('/goals-data', name: 'goals_data')]
    public function getGoalData(EntityManagerInterface $entityManager): JsonResponse
    {
        $user = $this->getUser();

        if ($user instanceof User) {
            $accountRepository = $entityManager->getRepository(Account::class);

            // Assuming there is a relation between User and Account, adjust the field names accordingly
            $accounts = $accountRepository->findBy(['userID' => $user]);
            $goalsDataArray = [];


            foreach ($accounts as $account) {
                $goals = $account->getGoal();

                // Utiliser foreach pour parcourir les objectifs directement
                foreach ($goals as $goal) {
                    $goalsDataArray[] = [
                        'account' => [
                            'id' => $account->getId(),
                            'name' => $account->getNameAccount(),
                            // Ajouter d'autres champs du compte au besoin
                        ],
                        'goal' => [
                            'id' => $goal->getId(),
                            'targetAmount' => $goal->getTargetAmount(),
                            'deadline' => $goal->getDeadline()->format('Y-m-d H:i:s'),
                            'description' => $goal->getDescription(),
                        ],
                    ];
                }
            }
            return new JsonResponse($goalsDataArray);
        }
        return new JsonResponse([]);
    }

    #[Route('/history-data', name: 'history_data')]
    public function getHistoryData(EntityManagerInterface $entityManager): JsonResponse
    {
        $user = $this->getUser();
        $historiesDataArray = [];

        if ($user instanceof User) {
            $accountRepository = $this->entityManager->getRepository(Account::class);
            $accounts = $accountRepository->findBy(['userID' => $user->getId()]);

            foreach ($accounts as $account) {
                $histories = $account->getHistories();

                foreach ($histories as $history) {
                    $historiesDataArray[] = [
                        'account' => [
                            'id' => $account->getId(),
                            'name' => $account->getNameAccount(),
                        ],
                        'history' => [
                            'id' => $history->getId(),
                            'date' => $history->getDate()->format('Y-m-d H:i:s'),
                            'historyBalance' => $history->getHistoryBalance(),
                        ],
                    ];
                }
            }
            return new JsonResponse($historiesDataArray);
        }
        return new JsonResponse([]);
    }


     #[Route('/income-data', name: 'income_data')]
     public function getIncomeData(EntityManagerInterface $entityManager): Response
     {
         $user = $this->getUser();
         $incomesDataArray = [];

        if ($user instanceof User) {
            $accountRepository = $this->entityManager->getRepository(Account::class);
            $accounts = $accountRepository->findBy(['userID' => $user->getId()]);

            foreach ($accounts as $account) {

                $incomes = $account->getIncomes();

                foreach ($incomes as $income) {
                    $incomesDataArray[] = [
                        'account' => [
                            'id' => $account->getId(),
                            'name' => $account->getNameAccount(),
                        ],
                        'income' => [
                            'id' => $income->getId(),
                            'category' => $income->getCategory(),
                            'description' => $income->getDescription(),
                            'amount' => $income->getAmount(),
                            'date' => $income->getDate()->format('Y-m-d H:i:s'),
                        ],
                    ];
                }
            }
            return new JsonResponse($incomesDataArray);
        }
        return new JsonResponse([]);
    }

    #[Route('/expense-data', name: 'expense_data')]
    public function getExpenseData(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $expenseDataArray = [];

        if ($user instanceof User) {
            $accountRepository = $this->entityManager->getRepository(Account::class);
            $accounts = $accountRepository->findBy(['userID' => $user->getId()]);

            foreach ($accounts as $account) {

                $expenses = $account->getExpenses();

                foreach ($expenses as $expense) {
                    $expenseDataArray[] = [
                        'account' => [
                            'id' => $account->getId(),
                            'name' => $account->getNameAccount(),
                        ],
                        'income' => [
                            'id' => $expense->getId(),
                            'category' => $expense->getCategory(),
                            'description' => $expense->getDescription(),
                            'amount' => $expense->getAmount(),
                            'date' => $expense->getDate()->format('Y-m-d H:i:s'),
                        ],
                    ];
                }
            }
            return new JsonResponse($expenseDataArray);
        }
        return new JsonResponse([]);
    }
}

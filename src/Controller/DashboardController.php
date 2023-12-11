<?php

namespace App\Controller;

use App\Entity\Account;
use App\Entity\Avis;
use App\Entity\Expense;
use App\Entity\History;
use App\Entity\Income;
use App\Entity\User;
use App\Form\EditExpenseFormType;
use App\Form\EditIncomeFormType;
use App\Form\ExpenseFormType;
use App\Form\IncomeFormType;
use App\Repository\ExpenseRepository;
use App\Repository\GoalRepository;
use App\Repository\DebtRepository;
use App\Repository\IncomeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;


class DashboardController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function accountList(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        if ($user instanceof User) {
            $userId = $user->getId();

            $accountRepository = $entityManager->getRepository(Account::class);
            $accounts = $accountRepository->findBy(['userID' => $userId]);

            return $this->render('components/account.html.twig', [
                'accounts' => $accounts,
            ]);
        }
        return $this->render('login/index.html.twig');
    }

    #[Route('/dashboard/{id}', name: 'detailsAccount')]
    public function accountDetails(int $id, Request $request, EntityManagerInterface $entityManager, IncomeRepository $incomeRepository, ExpenseRepository $expenseRepository, GoalRepository $goalRepository, DebtRepository $debtRepository): Response
    {
        $user = $this->getUser();
        $account = $entityManager->getRepository(Account::class)->find($id);

        if (!$account) {
            throw $this->createNotFoundException('Compte non trouvé');
        }
        $income = new Income();
        $expense = new Expense();

        $income->setDate(new \DateTime());
        $expense->setDate(new \DateTime());

        $incomeForm = $this->createForm(IncomeFormType::class, $income);
        $expenseForm = $this->createForm(ExpenseFormType::class, $expense);



        $formType = $request->query->get('type');
        $form = null;

        if ($formType === 'income') {
            $form = $incomeForm;
        } elseif ($formType === 'expense') {
            $form = $expenseForm;
        } else {
            $this->addFlash('error', 'Invalid form type');
        }

        if ($form) {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $transactionAmount = $form->get('amount')->getData();

                $account->setBalance($account->getBalance() + ($formType === 'income' ? $transactionAmount : -$transactionAmount));

                // Enregistrement dans la table historique
                $history = new History();
                $history->setAccount($account);
                $history->setDate(new \DateTime());

                $history->setHistoryBalance($account->getBalance());

                $entityManager->persist($history);

                if ($formType === 'income') {
                    $income = $form->getData();
                    $income->setAmount($transactionAmount);
                    $income->setAccount($account);
                    $entityManager->persist($income);
                } elseif ($formType === 'expense') {
                    $expense = $form->getData();
                    $expense->setAmount($transactionAmount);
                    $expense->setAccount($account);
                    $entityManager->persist($expense);
                }

                $entityManager->flush();

                return $this->redirectToRoute('detailsAccount', ['id' => $id]);
            }
        }

        $totalIncomeAmount = $incomeRepository->getTotalIncomeAmount($id);
        $totalExpenseAmount = $expenseRepository->getTotalExpenseAmount($id);
        $totalGoal = $goalRepository->findOneByAccountId($id);
        $totalDebt = $debtRepository->findOneByAccountId($id);

        return $this->render('pages/detailsAccount.html.twig', [
            'account' => $account,
            'totalIncomeAmount' => $totalIncomeAmount,
            'totalExpenseAmount' => $totalExpenseAmount,
            'totalGoal' => $totalGoal,
            'totalDebt' => $totalDebt,
            'form' => $form ? $form->createView() : null,
            'formType' => $formType,
            'incomeForm' => $incomeForm->createView(),
            'expenseForm' => $expenseForm->createView(),
        ]);
    }


    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    #[Route('/dashboard', name: 'dashboard')]
    public function dashboard(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $accountRepository = $entityManager->getRepository(Account::class);
        $topAccounts = $accountRepository->findTopAccounts(3);
        $accounts = $accountRepository->findBy(['userID' => $user]);

        if (!$user) {
            $username = 'Invité';
            $balanceSum = 0; // Default value if user is not authenticated
            $totalExpenseSum = 0;
            $totalIncomeSum = 0;
        } else {
            $username = $user->getUsername();

            $balanceSum = 0;
            $totalExpenseSum = 0;
            $totalIncomeSum = 0;

            foreach ($accounts as $account) {
                $balanceSum += $account->getBalance();

                $totalExpenseSum += $entityManager->getRepository(Expense::class)->getTotalExpenseAmount($account->getId());
                $totalIncomeSum += $entityManager->getRepository(Income::class)->getTotalIncomeAmount($account->getId());
            }
        }

        return $this->render('pages/dashboard.html.twig', [
            'username' => $username,
            'accounts' => $accounts,
            'topAccounts' => $topAccounts,
            'balanceSum' => $balanceSum,
            'totalExpenseSum' => $totalExpenseSum,
            'totalIncomeSum' => $totalIncomeSum,
        ]);
    }



    #[Route('/recap/{id}', name: 'recap')]
    public function recap(int $id, EntityManagerInterface $entityManager, Request $request): Response
    {
        $account = $entityManager->getRepository(Account::class)->find($id);

        $categoryFilter = $request->query->get('categoryFilter');
        $typeFilter = $request->query->get('typeFilter');

        $incomes = $entityManager->getRepository(Income::class)->findBy(['account' => $id]);
        $expenses = $entityManager->getRepository(Expense::class)->findBy(['account' => $id]);

        $recapItems = array_merge($incomes, $expenses);

        $recapItems = array_map(function ($item) {
            $itemType = $item instanceof Income ? 'Income' : 'Expense';
            $itemCategory = $item->getCategory()->getLabel();

            $item->type = $itemType;
            $item->categoryName = $itemCategory;
            return $item;
        }, $recapItems);

        // Extract unique categories for the filter options
        $uniqueCategories = array_unique(array_map(function ($item) {
            return $item->categoryName;
        }, $recapItems));

        return $this->render('pages/recap.html.twig', [
            'recapItems' => $recapItems,
            'categoryFilter' => $categoryFilter,
            'uniqueCategories' => $uniqueCategories,
            'account' => $account,
            'typeFilter' => $typeFilter,
        ]);
    }

    #[Route('/delete-item/{id}', name: 'delete_item')]
    public function deleteItem(EntityManagerInterface $entityManager, int $id): Response
    {
        $item = $entityManager->getRepository(Income::class)->find($id);

        if (!$item) {
            $item = $entityManager->getRepository(Expense::class)->find($id);
        }

        if (!$item) {
            throw $this->createNotFoundException('Item not found');
        }

        $entityManager->remove($item);
        $entityManager->flush();

        $this->addFlash('success', 'Item deleted successfully.');

        return $this->redirectToRoute('recap', ['id' => $item->getAccount()->getId()]);
    }

}
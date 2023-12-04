<?php

namespace App\Controller;

use App\Entity\Account;
use App\Entity\Expense;
use App\Entity\History;
use App\Entity\Income;
use App\Entity\User;
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
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

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
        return $this->render('login/index.html.twig', []);

    }

    #[Route('/dashboard/{id}', name: 'detailsAccount')]
    public function accountDetails(int $id, Request $request, EntityManagerInterface $entityManager, IncomeRepository $incomeRepository, ExpenseRepository $expenseRepository, GoalRepository $goalRepository, DebtRepository $debtRepository): Response
    {
        $user = $this->getUser();
        $account = $entityManager->getRepository(Account::class)->find($id);

        if (!$account) {
            throw $this->createNotFoundException('Compte non trouvé');
        }
        $incomeForm = $this->createForm(IncomeFormType::class, new Income());
        $expenseForm = $this->createForm(ExpenseFormType::class, new Expense());

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
    public function index(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $accountRepository = $entityManager->getRepository(Account::class);
        $accounts = $accountRepository->findAll();

        if ($user) {
            $username = $user->getUsername();
        } else {
            $username = 'Invité';
        }

        return $this->render('pages/dashboard.html.twig', [
            'username' => $username,
            'accounts' => $accounts,
        ]);
    }

}
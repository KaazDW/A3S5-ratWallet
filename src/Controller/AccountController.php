<?php

namespace App\Controller;

use App\Entity\Account;
use App\Entity\Debt;
use App\Entity\Expense;
use App\Entity\Goal;
use App\Entity\Income;
use App\Form\AccountFormType;
use App\Form\DebtFormType;
use App\Form\ExpenseFormType;
use App\Form\GoalFormType;
use App\Form\IncomeFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class AccountController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/newAccount', name: 'new_account')]
    public function createAccount(EntityManagerInterface $entityManager, Request $request): Response
    {
        $user = $this->getUser();

        // Vérifiez si l'utilisateur a atteint le nombre maximum de comptes
        $maxAccountLimit = 3;

        if ($user->getNbAccount() >= $maxAccountLimit) {
            $this->addFlash('error', 'Vous avez atteint le nombre maximum de comptes.');
            return $this->redirectToRoute('dashboard');
        }

        $account = new Account();
        $account->setUserId($user);

        $user->setNbAccount($user->getNbAccount() + 1);

        $form = $this->createForm(AccountFormType::class, $account);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($account);
            $this->entityManager->persist($user); // Persiste les changements à l'utilisateur
            $this->entityManager->flush();

            $this->addFlash('success', 'Compte créé avec succès !');

            return $this->redirectToRoute('dashboard');
        }

        return $this->render('pages/newAccount.html.twig', [
            'form' => $form->createView(),
            'account' => $account,
        ]);
    }

    #[Route('/deleteAccount/{id}', name: 'delete_account')]
    public function deleteAccount(int $id, EntityManagerInterface $entityManager): Response
    {
        $accountRepository = $entityManager->getRepository(Account::class);
        $account = $accountRepository->find($id);

        if (!$account) {
            $this->addFlash('error', 'Compte non trouvé.');
            return $this->redirectToRoute('dashboard');
        }

        $user = $this->getUser();
        $user->setNbAccount($user->getNbAccount() - 1);

        $entityManager->remove($account);
        $entityManager->persist($user);
        $entityManager->flush();
        $entityManager->persist($user);


        $this->addFlash('success', 'Compte supprimé avec succès !');

        return $this->redirectToRoute('dashboard');
    }

    #[Route('/createGoal/{id}', name: 'create_goal')]
    public function createGoal(int $id, Request $request, EntityManagerInterface $entityManager, Security $security): Response
    {
        $goal = new Goal();
        $form = $this->createForm(GoalFormType::class, $goal);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($goal);
            $entityManager->flush();

            // Associez le goal à l'Account
            $account = $entityManager->getRepository(Account::class)->find($id);

            if (!$account) {
                // Gérer le cas où l'Account avec l'ID spécifié n'est pas trouvé
                throw $this->createNotFoundException('Account not found');
            }

            $account->setGoal($goal);

            // Mise à jour de l'Account dans la base de données
            $entityManager->persist($account);
            $entityManager->flush();

            return $this->redirectToRoute('dashboard');
        }

        return $this->render('pages/newGoal.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/createDebt/{id}', name: 'create_debt')]
    public function createDebt(int $id, Request $request,EntityManagerInterface $entityManager): Response
    {
        $debt = new Debt();
        $form = $this->createForm(DebtFormType::class, $debt);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $debt->setAccountID($id);
            $entityManager->persist($debt);
            $entityManager->flush();


            return $this->redirectToRoute('dashboard');
        }

        return $this->render('pages/newDebt.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/createIncome', name: 'create_income')]
    public function createIncome(Request $request,EntityManagerInterface $entityManager): Response
    {
        $income = new Income();
        $form = $this->createForm(IncomeFormType::class, $income);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($income);
            $entityManager->flush();

            return $this->redirectToRoute('dashboard');
        }

        return $this->render('pages/newIncome.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/createExpense', name: 'create_expense')]
    public function createExpense(Request $request,EntityManagerInterface $entityManager): Response
    {
        $expense = new Expense();
        $form = $this->createForm(ExpenseFormType::class, $expense);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($expense);
            $entityManager->flush();

            return $this->redirectToRoute('dashboard');
        }

        return $this->render('pages/newExpense.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/export-goals', name: 'export_goals')]
    public function exportGoals(GoalExportService $goalExportService): Response
    {
        $filename = $goalExportService->exportToExcel();

        return $this->file($filename, 'goals_export.xlsx');
    }
}
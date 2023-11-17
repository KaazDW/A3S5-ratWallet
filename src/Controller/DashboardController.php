<?php

namespace App\Controller;

use App\Entity\Account;
use App\Entity\User;
use App\Repository\ExpenseRepository;
use App\Repository\GoalRepository;
use App\Repository\IncomeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
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
    public function detailsAccount(int $id ,EntityManagerInterface $entityManager, IncomeRepository $incomeRepository,ExpenseRepository $expenseRepository, GoalRepository $goalRepository): Response
    {
        $user = $this->getUser();

        $account = $this->entityManager->getRepository(Account::class)->find($id);


        if (!$account) {
            throw $this->createNotFoundException('Compte non trouvé');
        }

        $totalIncomeAmount = $incomeRepository->getTotalIncomeAmount();
        $totalExpenseAmount = $expenseRepository->getTotalExpenseAmount();
        $totalGoal = $goalRepository->findOneByAccountId($id);


        return $this->render('pages/detailsAccount.html.twig', [
            'account' => $account,
            'totalIncomeAmount' => $totalIncomeAmount,
            'totalExpenseAmount' => $totalExpenseAmount,
            'totalGoal' => $totalGoal,
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
<?php

namespace App\Controller;

use App\Entity\Account;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
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

        // Check if the user is an instance of the User class
        if ($user instanceof User) {
            // Get the user ID
            $userId = $user->getId();

            // Get the accounts for the user with the specified user ID
            $accountRepository = $entityManager->getRepository(Account::class);
            $accounts = $accountRepository->findBy(['userID' => $userId]);

            return $this->render('components/account.html.twig', [
                'accounts' => $accounts,
            ]);
        }

    }

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

    #[Route('/dashboard/{id}', name: 'detailsAccount')]
    public function detailsAccount(int $id ,EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        $account = $this->entityManager->getRepository(Account::class)->find($id);


        if (!$account) {
            throw $this->createNotFoundException('Compte non trouvé');
        }


        return $this->render('pages/detailsAccount.html.twig', [
            'account' => $account,
        ]);
    }

}
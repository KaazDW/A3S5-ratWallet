<?php

namespace App\Controller;

use App\Entity\Account;
use App\Form\AccountFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

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

        // Incrémente le nombre de comptes de l'utilisateur
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

}
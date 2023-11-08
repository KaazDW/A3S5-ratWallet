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
    public function createAccount(Request $request): Response
    {
        $user = $this->getUser();
        $account = new Account();
        $account->setUserId($user);

        $form = $this->createForm(AccountFormType::class, $account);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($account);
            $this->entityManager->flush();

            $this->addFlash('success', 'Avis ajouté avec succès !');

            return $this->render('pages/newAccount.html.twig', [
                'form' => $form,
            ]);
        }

        return $this->render('pages/newAccount.html.twig', [
            'form' => $form,
        ]);

    }

}
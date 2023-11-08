<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/profile', name: 'profile')]
    public function createAccount(Request $request): Response
    {

        $user = $this->getUser();
        $username = $user->getUsername();
        $email = $user->getEmail();

        return $this->render('pages/profile.html.twig', [
            'username' => $username,
            'email' => $email,
        ]);

    }
}
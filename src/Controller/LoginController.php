<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
    #[Route('/', name: 'accueil')]
    public function index(): Response
    {
        // Use to avoid access to homepage when connected, redirect to dashboard
        if($this->getUser()){
            return $this->redirectToRoute('dashboard');
        }

        return $this->render('accueil.html.twig', []);
    }

    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        // Use to avoid access to login when connected, redirect to dashboard
        if($this->getUser()){
            return $this->redirectToRoute('dashboard');
        }

        return $this->render('login/index.html.twig', [
          'last_username' => $lastUsername,
          'error'         => $error,
        ]);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(){}

}

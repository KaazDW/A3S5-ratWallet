<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
    #[Route('/', name: 'accueil')]
    public function index(AuthenticationUtils $authenticationUtils, Security $security): Response
    {
        if ($security->isGranted('ROLE_USER')) {
            // Rediriger vers le tableau de bord s'il est connecté
            return $this->redirectToRoute('dashboard');
        }
        return $this->render('accueil.html.twig', []);
    }

    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

          return $this->render('login/index.html.twig', [
              'last_username' => $lastUsername,
              'error'         => $error,
        ]);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(){}

}

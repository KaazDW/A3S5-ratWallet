<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;


class ProfilController extends AbstractController
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

        // Check if the user is authenticated
        if ($user === null) {
            // Handle the case when the user is not authenticated, e.g., redirect to login
            return $this->redirectToRoute('app_login');
        }

        $username = $user->getUsername();
        $email = $user->getEmail();

        return $this->render('pages/profil.html.twig', [
            'username' => $username,
            'email' => $email,
        ]);
    }


    #[Route('/change-username', name: 'change_username')]
    public function changeUsername(Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($request->isMethod('POST')) {
            $newUsername = $request->request->get('new_username');

            $user = $this->getUser();

            $user->setUsername($newUsername);

            $entityManager->flush();

            $this->addFlash('success', 'Username updated successfully.');

            return $this->redirectToRoute('dashboard');
        }

        return $this->render('pages/profil.html.twig');
    }

   // #[Route('/change-language/{lang}', name: 'change_language')]
    public function changeLanguageeeee(Request $request, SessionInterface $session, $lang): RedirectResponse
    {
        $validLanguages = ['fr', 'en'];

        if (!in_array($lang, $validLanguages)) {
            return $this->redirectToRoute('dashboard');
        }

        $session->set('_locale', $lang);
        dump($locale = $request->getLocale());


        $referer = $request->headers->get('referer');
        return $this->redirectToRoute('dashboard');
    }



}
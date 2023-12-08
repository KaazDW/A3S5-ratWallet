<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

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

    #[Route('/changeLanguage', name: 'change_language')]
    public function changeLanguage(Request $request, TranslatorInterface $translator)
    {
        $newLocale = $request->get('new_locale');

        if (in_array($newLocale, $translator->getFallbackLocales())) {
            $translator->setLocale($newLocale);
            $this->addFlash('success', $translator->trans('Language changed successfully.'));
        } else {
            $this->addFlash('error', $translator->trans('Invalid language selection.'));
        }

        return $this->redirect($request->headers->get('referer'));
    }



}
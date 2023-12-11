<?php

namespace App\Controller;

use App\Entity\Category;
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
    public function createAccount(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        if ($user === null) {
            return $this->redirectToRoute('app_login');
        }

        $username = $user->getUsername();
        $email = $user->getEmail();

        $categories = $entityManager->getRepository(Category::class)->findAll();


        return $this->render('pages/profil.html.twig', [
            'username' => $username,
            'email' => $email,
            'categories' => $categories
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

    #[Route('/change-language/{lang}', name: 'change_language')]
    public function changeLanguage(Request $request, SessionInterface $session, $lang): RedirectResponse
    {
        $validLanguages = ['fr', 'en'];

        if (!in_array($lang, $validLanguages)) {
            return $this->redirectToRoute('dashboard');
        }

        $session->set('_locale', $lang);
        dump($locale = $request->getLocale());

        return $this->redirectToRoute('dashboard');
    }

    #[Route('/category/add', name: 'add_category')]
    public function addCategory(EntityManagerInterface $entityManager, Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $label = $request->request->get('label');

            // Validate $label as needed

            // Check if the category with the given label already exists
            $existingCategory = $entityManager->getRepository(Category::class)->findOneBy(['label' => $label]);

            if ($existingCategory) {
                $this->addFlash('danger', 'Category with this label already exists.');
            } else {
                // Create and persist the new category
                $category = new Category();
                $category->setLabel($label);

                $entityManager->persist($category);
                $entityManager->flush();

                $this->addFlash('success', 'Category added successfully.');
            }

            return $this->redirectToRoute('profile');
        }

        return $this->render('pages/profil.html.twig');
    }

    #[Route('/delete-category/{id}', name: 'delete_category')]
    public function deleteCategory(EntityManagerInterface $entityManager, int $id): Response
    {
        $category = $entityManager->getRepository(Category::class)->find($id);

        if (!$category) {
            throw $this->createNotFoundException('Category not found');
        }

        // Check if the category is associated with any records in related tables (e.g., expenses, incomes, etc.)
        $isInUse = $this->isCategoryInUse($category);

        if ($isInUse) {
            $this->addFlash('danger', 'Cannot delete category. It is in use.');
        } else {
            $entityManager->remove($category);
            $entityManager->flush();

            $this->addFlash('success', 'Category deleted successfully.');
        }

        return $this->redirectToRoute('profile');
    }

    /**
     * Check if the category is associated with any records in related tables.
     *
     * @param Category $category
     * @return bool
     */
    private function isCategoryInUse(Category $category): bool
    {
        // Implement logic to check if the category is used in any related tables
        // For example, check if there are associated expenses, incomes, etc.
        // You may need to update this logic based on your specific entity associations.

        // Example: Check if the category is used in expenses
        $expensesCount = $category->getExpenses()->count();

        return $expensesCount > 0;
    }



}
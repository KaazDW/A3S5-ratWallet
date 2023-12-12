<?php

namespace App\Controller;

use App\Entity\Account;
use App\Entity\Budget;
use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;



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
            'categories' => $categories,
        ]);
    }


    #[Route('/add_budget/{id}', name: 'add_budget')]
    public function addBudget(int $id, Request $request, EntityManagerInterface $entityManager, UrlGeneratorInterface $urlGenerator): Response
    {
        $user = $this->getUser();

        if ($user === null) {
            return $this->redirectToRoute('app_login');
        }
        $account = $entityManager->getRepository(Account::class)->find($id);
        if (!$account) {
            throw $this->createNotFoundException('Account not found');
        }

        if ($request->isMethod('POST')) {
            $amount = $request->request->get('amount');
            $categoryId = $request->request->get('category');

            $category = $entityManager->getRepository(Category::class)->find($categoryId);


            if (!$category) {
                throw $this->createNotFoundException('La catégorie n\'existe pas');
            }

            $existingBudget = $entityManager->getRepository(Budget::class)->findOneBy([
                'category' => $category,
                'user' => $user,
            ]);

            if ($existingBudget) {
                $this->addFlash('dangerr', 'Le budget existe deja!');
                $url = $urlGenerator->generate('add_budget', ['id' => $id]);
                return $this->redirect($url);
            }

            $budget = new Budget();
            $budget->setAmount($amount);
            $budget->setCategory($category);
            $budget->setUser($user);
            $budget->setAccount($account);

            $entityManager->persist($budget);
            $entityManager->flush();

            $this->addFlash('successs', 'Le budget a été ajouté avec succès.');

            $url = $urlGenerator->generate('add_budget', ['id' => $id]);
            return $this->redirect($url);
        }

        $categories = $entityManager->getRepository(Category::class)->findAll();

        $budgets = [];
        foreach ($categories as $category) {
            $budget = $entityManager->getRepository(Budget::class)->findOneBy([
                'category' => $category,
                'user' => $user,
            ]);

            // Si aucun budget n'est trouvé, initialiser à 0
            $budgets[$category->getId()] = $budget ? $budget->getAmount() : 0;
        }

        return $this->render('pages/add_budget.html.twig', [
            'categories' => $categories,
            'account' => $account,
            'budgets' => $budget,
        ]);
    }

    #[Route('/edit_budget/{id}', name: 'edit_budget')]
    public function editBudget(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        if ($user === null) {
            return $this->redirectToRoute('app_login');
        }

        $budget = $entityManager->getRepository(Budget::class)->find($id);

        if (!$budget) {
            throw $this->createNotFoundException('Budget not found');
        }

        if ($request->isMethod('POST')) {

            $amount = $request->request->get('amount');
            $budget->setAmount($amount);
            $entityManager->flush();

            $this->addFlash('successs', 'Le budget a été modifié avec succès.');

            return $this->redirectToRoute('add_budget', ['id' => $id]);
        }

        return $this->render('pages/add_budget.html.twig', [
            'budgets' => $budget,
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

            $existingCategory = $entityManager->getRepository(Category::class)->findOneBy(['label' => $label]);

            if ($existingCategory) {
                $this->addFlash('danger', 'Category with this label already exists.');
            } else {
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
        $expensesCount = $category->getExpenses()->count();
        return $expensesCount > 0;
    }



}
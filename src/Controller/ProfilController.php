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
            // Récupérer les données du formulaire
            $amount = $request->request->get('amount');
            $categoryId = $request->request->get('category');

            // Récupérer la catégorie correspondante
            $category = $entityManager->getRepository(Category::class)->find($categoryId);


            if (!$category) {
                throw $this->createNotFoundException('La catégorie n\'existe pas');
            }

            // Vérifier si un budget existe déjà pour cette catégorie et cet utilisateur
            $existingBudget = $entityManager->getRepository(Budget::class)->findOneBy([
                'category' => $category,
                'user' => $user,
            ]);

            // Si un budget existe déjà, rediriger vers la liste des catégories
            if ($existingBudget) {
                $this->addFlash('dangerr', 'Le budget existe deja!');
                // Rediriger vers la liste des catégories après l'ajout du budget
                $url = $urlGenerator->generate('add_budget', ['id' => $id]);
                return $this->redirect($url);
            }

            // Créer un nouvel objet Budget lié à la catégorie et à l'utilisateur actuel
            $budget = new Budget();
            $budget->setAmount($amount);
            $budget->setCategory($category);
            $budget->setUser($user);
            $budget->setAccount($account);

            // Enregistrer le budget dans la base de données
            $entityManager->persist($budget);
            $entityManager->flush();

            $this->addFlash('successs', 'Le budget a été ajouté avec succès.');

            // Rediriger vers la liste des catégories après l'ajout du budget
            $url = $urlGenerator->generate('add_budget', ['id' => $id]);
            return $this->redirect($url);
        }

        // Récupérer toutes les catégories pour le formulaire
        $categories = $entityManager->getRepository(Category::class)->findAll();

        // Récupérer les budgets associés à chaque catégorie pour l'utilisateur actuel
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

        // Vérifier si l'utilisateur a le droit de modifier ce budget (vérifiez s'il s'agit de son budget)

        if ($request->isMethod('POST')) {
            // Récupérer les données du formulaire
            $amount = $request->request->get('amount');

            // Mettre à jour le montant du budget
            $budget->setAmount($amount);

            // Enregistrer les modifications dans la base de données
            $entityManager->flush();

            $this->addFlash('successs', 'Le budget a été modifié avec succès.');

            // Rediriger vers la page où l'utilisateur était
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
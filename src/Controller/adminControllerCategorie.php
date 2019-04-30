<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Ce controller permet la gestion des catégories côté Admin
 * @Route("/admin/categories")
 */
class adminControllerCategorie extends AbstractController
{
    /**
     * Cette function de crée une nouvelle catégorie
     * @Route("/new", name="categories_new")
     */
    public function new(Request $request, ObjectManager $manager)
    {
        $category = new Category();
        $form = $this->createFormBuilder($category)
            ->add('name', TextType::class)
            ->add('Créer', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $category->setActive(0);
            $manager->persist($category);
            $manager->flush();

            return $this->redirectToRoute('categories_active');
        }

        $users = $this->getUser();

        return $this->render('admin/Categories/new.html.twig', [
            'title' => "Nouvelle catégorie",
            "users" => $users,
            "form" => $form->createView()
        ]);
    }

    /**
     * Cette function permet d'afficher toutes les catégories avec un système de pagination
     * @Route("/categories", name="categories")
     */
    public function index(CategoryRepository $categoryRepository, PaginatorInterface $paginator, Request $request)
    {
        $pagin = $paginator->paginate(
            $categoryRepository->findAll(),
            $request->query->getInt('page', 1),
            2
        );
        $users = $this->getUser();
        return $this->render('admin/Categories/index.html.twig', [
            'title' => "Categories",
            "users" => $users,
            "categories" => $pagin
        ]);
    }

    /**
     * Cette function permet de montrer une catégorie selon l'ID reçu uniquement
     * @Route("/{id}", name="categories_show")
     */
    public function show(CategoryRepository $categoryRepository, $id)
    {
        $users = $this->getUser();
        return $this->render('admin/Categories/show.html.twig', [
            'title' => "Voir les Catégories",
            'users' => $users,
            "categories" => $categoryRepository->findBy(['id' => $id])
        ]);
    }

    /**
     * Cette function permet d'édit une catégorie
     * @Route("/{id}/edit", name="categories_edit")
     */
    public function edit(Category $category, Request $request, ObjectManager $manager, $id)
    {
        $form = $this->createFormBuilder($category)
            ->add('name', TextType::class)
            ->add('active', NumberType::class)
            ->add('Modifier', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->flush();

            return $this->redirectToRoute('categories_show', [
                'id' => $id
            ]);
        }

        $users = $this->getUser();

        return $this->render('admin/Categories/edit.html.twig', [
            'title' => "Modifier les Categories",
            "users" => $users,
            "categorie" => $category,
            'form' => $form->createView()
        ]);
    }

    /**
     * Cette function permet de supprimer une catégorie
     * @Route("/delete/{id}", name="categories_delete")
     */
    public function delete(Category $category, Request $request)
    {
        if ($this->isCsrfTokenValid('delete' . $category->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($category);
            $entityManager->flush();
        }
        return $this->redirectToRoute("categories");
    }

    /**
     * Cette function permet d'afficher toutes les catégories à activé avec un système de pagination
     * @Route("/categories/active", name="categories_active")
     */
    public function index_active(CategoryRepository $categoryRepository, PaginatorInterface $paginator, Request $request)
    {
        $pagin = $paginator->paginate(
            $categoryRepository->findBy(['active' => 0]),
            $request->query->getInt('page', 1),
            2
        );
        $users = $this->getUser();
        return $this->render('admin/Categories/active.html.twig', [
            'title' => "Activation des catégories",
            "users" => $users,
            "categories" => $pagin
        ]);
    }

}
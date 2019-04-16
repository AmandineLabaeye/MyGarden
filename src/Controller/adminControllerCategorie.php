<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/categories")
 */
class adminControllerCategorie extends AbstractController
{
    /**
     * @Route("/new", name="categories_new")
     */
    public function new(Request $request, ObjectManager $manager)
    {
        $category = new Category();
        $form = $this->createFormBuilder($category)
            ->add('name', TextType::class)
            ->add('Send', SubmitType::class)
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
            'title' => "New Category",
            "users" => $users,
            "form" => $form->createView()
        ]);
    }

    /**
     * @Route("/categories", name="categories")
     */
    public function index(CategoryRepository $categoryRepository)
    {
        $users = $this->getUser();
        return $this->render('admin/Categories/index.html.twig', [
            'title' => "Categories",
            "users" => $users,
            "categories" => $categoryRepository->findAll()
        ]);
    }

    /**
     * @Route("/{id}", name="categories_show")
     */
    public function show(CategoryRepository $categoryRepository, $id)
    {
        $users = $this->getUser();
        return $this->render('admin/Categories/show.html.twig', [
            'title' => "Show Categorie",
            'users' => $users,
            "categories" => $categoryRepository->findBy(['id' => $id])
        ]);
    }

    /**
     * @Route("/{id}/edit", name="categories_edit")
     */
    public function edit(Category $category, Request $request, ObjectManager $manager, $id)
    {
        $form = $this->createFormBuilder($category)
            ->add('name', TextType::class)
            ->add('active', NumberType::class)
            ->add('Update', SubmitType::class)
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
            'title' => "Edit Categories",
            "users" => $users,
            "categorie" => $category,
            'form' => $form->createView()
        ]);
    }

    /**
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
     * @Route("/categories/active", name="categories_active")
     */
    public function index_active(CategoryRepository $categoryRepository)
    {
        $users = $this->getUser();
        return $this->render('admin/Categories/active.html.twig', [
            'title' => "Active Category",
            "users" => $users,
            "categories" => $categoryRepository->findBy(['active' => 0])
        ]);
    }

}
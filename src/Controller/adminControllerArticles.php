<?php

namespace App\Controller;

use App\Entity\Articles;
use App\Repository\ArticlesRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/articles")
 */
class adminControllerArticles extends AbstractController
{
    /**
     * @Route("/articles", name="articles")
     */
    public function index(ArticlesRepository $articlesRepository)
    {
        $users = $this->getUser();
        return $this->render('admin/Articles/index.html.twig', [
            "title" => "Article",
            "articles" => $articlesRepository->findAll(),
            "users" => $users
        ]);
    }

    /**
     * @Route("/{id}", name="articles_show")
     */
    public function show(ArticlesRepository $articlesRepository, $id)
    {
        $users = $this->getUser();
        return $this->render('admin/Articles/show.html.twig', [
            "title" => "Article",
            "users" => $users,
            "articles" => $articlesRepository->findBy(["id" => $id])
        ]);
    }

    /**
     * @Route("/{id}/edit", name="articles_edit")
     */
    public function edit(Articles $articles, Request $request, ObjectManager $manager, $id)
    {
        $form = $this->createFormBuilder($articles)
            ->add('name', TextType::class)
            ->add('picture', TextType::class)
            ->add('description', TextareaType::class)
            ->add('namelatin', TextType::class)
            ->add('toxicite', TextType::class)
            ->add('environnement', TextType::class)
            ->add('urlBuy', TextType::class)
            ->add('active', NumberType::class)
            ->add('Save', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->flush();

            return $this->redirectToRoute('articles_show', [
                'id' => $id
            ]);
        }
        $users = $this->getUser();
        return $this->render('admin/Articles/edit.html.twig', [
            'title' => "Edit Articles",
            "users" => $users,
            "article" => $articles,
            "form" => $form->createView()
        ]);
    }

    /**
     * @Route("/delete/{id}", name="articles_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Articles $articles)
    {
        if ($this->isCsrfTokenValid('delete' . $articles->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($articles);
            $entityManager->flush();
        }
        return $this->redirectToRoute("articles");
    }

    /**
     * @Route("/articles/active", name="articles_active")
     */
    public function index_active(ArticlesRepository $articlesRepository)
    {
        $users = $this->getUser();
        return $this->render('admin/Articles/active.html.twig', [
            'title' => "Active",
            'users' => $users,
            "articles" => $articlesRepository->findBy(['active' => 0])
        ]);
    }

}
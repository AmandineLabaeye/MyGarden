<?php

namespace App\Controller;

use App\Entity\Articles;
use App\Repository\ArticlesRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Ce controller permet la gestion des articles côté Admin
 * @Route("/admin/articles")
 */
class adminControllerArticles extends AbstractController
{
    /**
     * Cette function permet d'afficher tous les articles avec un système de pagination
     * @Route("/articles", name="articles")
     */
    public function index(ArticlesRepository $articlesRepository, Request $request, PaginatorInterface $paginator)
    {
        $pagin = $paginator->paginate(
            $articlesRepository->findAll(),
            $request->query->getInt('page', 1),
            5
        );
        $users = $this->getUser();
        return $this->render('admin/Articles/index.html.twig', [
            "title" => "Articles",
            "articles" => $pagin,
            "users" => $users
        ]);
    }

    /**
     * Cette function permet de montrer un article selon l'ID reçu uniquement
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
     * Cette function permet d'éditer un article
     * @Route("/{id}/edit", name="articles_edit")
     */
    public function edit(Articles $articles, Request $request, ObjectManager $manager, $id)
    {
        $form = $this->createFormBuilder($articles)
            ->add('name', TextType::class, [
                'label' => "Nom "
            ])
            ->add('picture', TextType::class, [
                'required' => false,
                'label' => "Photo "
            ])
            ->add('description', TextareaType::class,[
                'label' => "Description "
            ])
            ->add('namelatin', TextType::class, [
                'required' => false,
                'label' => "Nom latin "
            ])
            ->add('toxicite', TextType::class, [
                'required' => false,
                'label' => "Toxicité "
            ])
            ->add('environnement', TextType::class, [
                'required' => false,
                'label'=> "Environnement "
            ])
            ->add('urlBuy', TextType::class, [
                'required' => false,
                'label' => "Endroit pour l'acheter "
            ])
            ->add('active', NumberType::class, [
                'label' => "Active "
            ])
            ->add('Modifier', SubmitType::class)
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
            'title' => "Modifier les Articles",
            "users" => $users,
            "article" => $articles,
            "form" => $form->createView()
        ]);
    }

    /**
     * Cette function permet de supprimer un article
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
     * Cette function permet d'afficher tous les articles à activé avec un système de pagination
     * @Route("/articles/active", name="articles_active")
     */
    public function index_active(ArticlesRepository $articlesRepository, Request $request, PaginatorInterface $paginator)
    {
        $pagin = $paginator->paginate(
            $articlesRepository->findBy(['active' => 0]),
            $request->query->getInt('page', 1),
            2
        );
        $users = $this->getUser();
        return $this->render('admin/Articles/active.html.twig', [
            'title' => "Activation des articles",
            'users' => $users,
            "articles" => $pagin
        ]);
    }

}
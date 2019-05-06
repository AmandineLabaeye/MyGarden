<?php

namespace App\Controller;

use App\Entity\Articles;
use App\Entity\Category;
use App\Entity\Comments;
use App\Entity\CommentsPublication;
use App\Entity\LikeArticle;
use App\Entity\PublicationsProfil;
use App\Repository\ArticlesRepository;
use App\Repository\CategoryRepository;
use App\Repository\CommentsRepository;
use App\Repository\LikeArticleRepository;
use App\Repository\UsersRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Ce controller permet la gestion des pages côté membre
 * @Route("/member")
 */
class memberController extends AbstractController
{
    /**
     * Cette function permet d'afficher avec un système de pagination la liste des utilisateurs inscris
     * @Route("/membreinscris", name="users_register_member")
     */
    public function usersRegister(UsersRepository $usersRepository, PaginatorInterface $paginator, Request $request)
    {
        $pagin = $paginator->paginate(
            $usersRepository->findBy(['active' => 1]),
            $request->query->getInt('page', 1),
            10
        );
        $users = $this->getUser();
        return $this->render('member/listeUsers.html.twig', [
            "title" => "Liste Utilisateurs",
            "users" => $users,
            "user" => $pagin
        ]);
    }

    /**
     * Cette function permet d'afficher avec un système de pagination la liste des utilisateurs inscris, ou il y a un
     * système de filtre
     * @Route("/membreinscris", name="users_register_member")
     */
    public function filterU(UsersRepository $usersRepository, PaginatorInterface $paginator, Request $request)
    {
        $surnameUser = null;

        $pagin = $paginator->paginate(
            $usersRepository->findBy(['active' => 1]),
            $request->query->getInt('page', 1),
            10
        );

        $user = $usersRepository->findBy(['active' => 1]);

        $form = $this->createFormBuilder($user)
            ->add('surname', TextType::class, [
                'required' => false,
                'label' => " ",
                'attr' => [
                    'placeholder' => "Prenom Utilisateur"
                ]
            ])
            ->add('Rechercher', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $surnameUser = $form['surname']->getData();
        }

        $users = $this->getUser();
        return $this->render('member/listeUsers.html.twig', [
            "title" => "Liste Utilisateurs",
            "surnameUser" => $surnameUser,
            "users" => $users,
            "user" => $pagin,
            "form" => $form->createView()
        ]);
    }

    /**
     * Cette function permet d'afficher la page d'accueil
     * @Route("/", name="homepage_member")
     */
    public function index(ArticlesRepository $articlesRepository, CategoryRepository $categoryRepository, Request $request, PaginatorInterface $paginator)
    {
        $pagin = $paginator->paginate(
            $articlesRepository->findBy(["active" => 1]),
            $request->query->getInt('page', 1),
            2
        );
        $users = $this->getUser();
        return $this->render("home.html.twig", [
            "title" => "Page d'accueil",
            "users" => $users,
            "articles" => $pagin,
            "categories" => $categoryRepository->findBy(["active" => 1])
        ]);
    }

    /**
     * Cette function permet au utilisateurs d'éditer uniquement leur articles
     * @Route("/{id}/edit", name="articles_edit_member")
     */
    public function edit(Articles $articles, Request $request, ObjectManager $manager, $id)
    {
        $form = $this->createFormBuilder($articles)
            ->add('name', TextType::class)
            ->add('picture', TextType::class, [
                'required' => false
            ])
            ->add('description', TextareaType::class)
            ->add('namelatin', TextType::class, [
                'required' => false
            ])
            ->add('toxicite', TextType::class, [
                'required' => false
            ])
            ->add('environnement', TextType::class, [
                'required' => false
            ])
            ->add('urlBuy', TextType::class, [
                'required' => false
            ])
            ->add('active', NumberType::class)
            ->add('Modifier', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->flush();

            return $this->redirectToRoute('homepage_member');
        }
        $users = $this->getUser();
        return $this->render('admin/Articles/edit.html.twig', [
            'title' => "Modifier des Articles",
            "users" => $users,
            "article" => $articles,
            "form" => $form->createView()
        ]);
    }

    /**
     * Cette function leur permet de supprimer uniquement leur articles
     * @Route("/delete/{id}", name="articles_delete_member", methods={"DELETE"})
     */
    public function delete(Request $request, Articles $articles)
    {
        if ($this->isCsrfTokenValid('delete' . $articles->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($articles);
            $entityManager->flush();
        }
        return $this->redirectToRoute("homepage_member");
    }

    /**
     * Cette function permets d'afficher un article avec ses infos uniquement
     * @Route("/{id}", name="one_member")
     */
    public function one(ArticlesRepository $articlesRepository, CommentsRepository $commentsRepository, Request $request, ObjectManager $manager, PaginatorInterface $paginator, $id)
    {

        $comments = new Comments();

        $users = $this->getUser();

        $idA = $articlesRepository->find($id);

        $date = date("d-m-Y H:i:s");

        $form = $this->createFormBuilder($comments)
            ->add('content', TextareaType::class)
            ->add('Poster', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comments->setActive(0);
            $comments->setUsers($users);
            $comments->setArticles($idA);
            $comments->setDate($date);
            $manager->persist($comments);
            $manager->flush();
        }

        $pagin = $paginator->paginate(
            $commentsRepository->findBy(['articles' => $id, 'active' => 1]),
            $request->query->getInt('page', 1),
            7
        );

        return $this->render('member/one.html.twig', [
            "title" => "L'article",
            "users" => $users,
            "articles" => $articlesRepository->findBy(['id' => $id]),
            'form' => $form->createView(),
            'comments' => $pagin
        ]);
    }

    /**
     * Cette function permet au utilisateurs d'éditer uniquement leur commentaires
     * @Route("/comments/{id}/edit", name="comments_edit_member")
     */
    public function editC(Request $request, Comments $comments, ObjectManager $manager, $id)
    {
        $form = $this->createFormBuilder($comments)
            ->add('content', TextareaType::class)
            ->add('active', NumberType::class)
            ->add('Modifier', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->flush();

            return $this->redirectToRoute('homepage_member');
        }

        $users = $this->getUser();

        return $this->render("admin/Comments/edit.html.twig", [
            'title' => 'Modifier les Commentaires',
            "users" => $users,
            "form" => $form->createView(),
            "comment" => $comments
        ]);
    }

    /**
     * Cette function permet au utilisateur de supprimer uniquement leur commentaires
     * @Route("/comments/delete/{id}", name="comments_delete_member", methods={"DELETE"})
     */
    public function deleteC(Comments $comments, Request $request)
    {
        if ($this->isCsrfTokenValid('delete' . $comments->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($comments);
            $entityManager->flush();
        }
        return $this->redirectToRoute("homepage_member");
    }

    /**
     * Cette function permet de créer un nouveau article
     * @Route("/member/newArticle", name="create_article_member")
     */
    public function new(ObjectManager $manager, Request $request)
    {
        $articles = new Articles();

        $users = $this->getUser();

        $date = date("d-m-Y H:i:s");

        $form = $this->createFormBuilder($articles)
            ->add('name', TextType::class)
            ->add('picture', TextType::class, [
                'required' => false
            ])
            ->add('description', TextType::class)
            ->add('namelatin', TextType::class, [
                'required' => false
            ])
            ->add('toxicite', TextType::class, [
                'required' => false
            ])
            ->add('environnement', TextType::class, [
                'required' => false
            ])
            ->add('urlBuy', TextType::class, [
                'required' => false
            ])
            ->add('categories', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name'])
            ->add('Créer', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $articles->setActive(0);
            $articles->setDate($date);
            $articles->setUsers($users);

            $manager->persist($articles);
            $manager->flush();

            return $this->render('articleV.html.twig',[
                'users' => $users,
                "title" => "Validation"
            ]);
        }
        return $this->render('member/new.html.twig', [
            "title" => "Création d'un article",
            "users" => $users,
            "form" => $form->createView()
        ]);
    }

    /**
     * Cette function permet au utilisateurs d'éditer uniquement leur commentaires de Publications
     * @Route("/commentsP/{id}/edit", name="commentsP_edit_member")
     */
    public function editCP(CommentsPublication $commentsPublication, Request $request, ObjectManager $manager, $id)
    {
        $form = $this->createFormBuilder($commentsPublication)
            ->add('content', TextareaType::class)
            ->add('Modifier', SubmitType::class)
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager->flush();

            return $this->redirectToRoute('users_register_member');
        }
        $users = $this->getUser();
        return $this->render('admin/CommentsP/edit.html.twig', [
            'title' => "Modifier les Commentaires",
            "users" => $users,
            "comment" => $commentsPublication,
            "form" => $form->createView()
        ]);
    }

    /**
     * Cette function permet au utilisateur de supprimer uniquement leur commentaires de Publication
     * @Route("/commentsP/delete/{id}", name="commentsP_delete_member", methods={"DELETE"})
     */
    public function deleteCP(Request $request, CommentsPublication $commentsPublication)
    {
        if ($this->isCsrfTokenValid('delete' . $commentsPublication->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($commentsPublication);
            $entityManager->flush();
        }
        return $this->redirectToRoute("users_register_member");
    }

    /**
     * Cette function permet au utilisateurs d'éditer uniquement leur publications
     * @Route("/publications/{id}/edit", name="publications_edit_member")
     */
    public function editP(PublicationsProfil $profil, Request $request, ObjectManager $manager, $id)
    {
        $form = $this->createFormBuilder($profil)
            ->add('publication', TextareaType::class)
            ->add('picture', TextType::class, [
                'required' => false
            ])
            ->add('Modifier', SubmitType::class)
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager->flush();
            return $this->redirectToRoute('users_register_member');
        }
        $users = $this->getUser();
        return $this->render('admin/Publications/edit.html.twig', [
            "title" => "Modifier les Publications",
            "users" => $users,
            "publication" => $profil,
            'form' => $form->createView()
        ]);
    }

    /**
     * Cette function permet au utilisateur de supprimer uniquement leur publication
     * @Route("/publications/delete/{id}", name="publications_delete_member", methods={"DELETE"})
     */
    public function deleteP(Request $request, PublicationsProfil $profil)
    {
        if ($this->isCsrfTokenValid('delete' . $profil->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($profil);
            $entityManager->flush();
        }
        return $this->redirectToRoute("users_register_member");
    }

    /**
     * Cette function est la page d'accueil où il y a un système de filtre
     * @Route("/", name="homepage_member")
     */
    public function filterA(ArticlesRepository $articlesRepository, CategoryRepository $categoryRepository, PaginatorInterface $paginator, Request $request)
    {
        $nameArticle = null;

        $pagin = $paginator->paginate(
            $articlesRepository->findBy(["active" => 1]),
            $request->query->getInt('page', 1),
            2
        );

        $articles = $articlesRepository->findBy(["active" => 1]);

        $form = $this->createFormBuilder($articles)
            ->add('name', TextType::class, [
                'label' => " ",
                'attr' => [
                    'placeholder' => "Nom de l'article"
                ]
            ])
            ->add('Rechercher', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $nameArticle = $form['name']->getData();
        }

        $users = $this->getUser();

        return $this->render('home.html.twig', [
            "title" => "Page d'accueil",
            "users" => $users,
            "categories" => $categoryRepository->findBy(["active" => 1]),
            'nameArticle' => $nameArticle,
            'articles' => $pagin,
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet de liker ou unliker un article
     *
     * @Route("/article/{id}/like", name="article_like")
     * @param Articles $articles
     * @param ObjectManager $manager
     * @param LikeArticleRepository $likeArticleRepository
     * @return Response
     */
    public function Like(Articles $articles, ObjectManager $manager, LikeArticleRepository $likeArticleRepository): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->json([
                'code' => 403,
                "message" => "Pas autorisé"
            ], 403);
        }
        if ($articles->isLikeByUser($user)) {
            $like = $likeArticleRepository->findOneBy([
                'article' => $articles,
                'users' => $user
            ]);

            $manager->remove($like);
            $manager->flush();

            return $this->json([
                'code' => 200,
                "message" => "Like bien supprimé",
                'likes' => $likeArticleRepository->count(['article' => $articles])
            ], 200);
        }

        $like = new LikeArticle();
        $like->setArticle($articles);
        $like->setUsers($user);

        $manager->persist($like);
        $manager->flush();

        return $this->json([
            'code' => 200,
            "message" => "Like bien ajouté",
            "likes" => $likeArticleRepository->count(['article' => $articles])
        ], 200);
    }
}
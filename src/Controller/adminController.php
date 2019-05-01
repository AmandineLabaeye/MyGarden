<?php

namespace App\Controller;

use App\Entity\Articles;
use App\Entity\Category;
use App\Entity\Comments;
use App\Entity\LikeArticle;
use App\Repository\ArticlesRepository;
use App\Repository\CategoryRepository;
use App\Repository\CommentsRepository;
use App\Repository\LikeArticleRepository;
use App\Repository\UsersRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Ce controller permet la gestion des pages côté admin
 * @Route("/admin")
 */
class adminController extends AbstractController
{
    /**
     * Cette function permet d'afficher le panel Admin avec tout ces liens dans le templates Twig
     * @Route("/index", name="panel_admin")
     */
    public function panel()
    {
        // Permet de récuperer l'utilisateur en cours
        $users = $this->getUser();
        // Permet de faire le rendu donc de dire à quelle vue cette fonction appartient avec ses paramètres
        return $this->render('admin/index.html.twig', [
            "title" => "Page d'accueil administrateur",
            "users" => $users
        ]);
    }

    /**
     * Cette function permet d'afficher avec un système de pagination la liste des utilisateurs inscris
     * @Route("/membreinscris", name="users_register_admin")
     */
    public function usersRegister(UsersRepository $usersRepository, PaginatorInterface $paginator, Request $request)
    {
        // Fonction de pagination, 1er ce qu'il faut paginer, 2eme recupération de la page par défaut, 3eme combien
        // d'élement par page voulu
        $pagin = $paginator->paginate(
            $usersRepository->findBy(['active' => 1]),
            $request->query->getInt('page', 1),
            10
        );
        // Permet de récuperer l'utilisateur en cours
        $users = $this->getUser();
        // Permet de faire le rendu donc de dire à quelle vue cette fonction appartient avec ses paramètres
        return $this->render('member/listeUsers.html.twig', [
            "title" => "Liste Utilisateurs",
            "users" => $users,
            "user" => $pagin
        ]);
    }

    /**
     * Cette function permet d'afficher avec un système de pagination la liste des utilisateurs inscris, ou il y a un
     * système de filtre
     * @Route("/membreinscris", name="users_register_admin")
     */
    public function filterU(UsersRepository $usersRepository, PaginatorInterface $paginator, Request $request)
    {
        // Défini la variable à NULL
        $surnameUser = null;

        // Fonction de pagination, 1er ce qu'il faut paginer, 2eme recupération de la page par défaut, 3eme combien
        // d'élement par page voulu
        $pagin = $paginator->paginate(
            $usersRepository->findBy(['active' => 1]),
            $request->query->getInt('page', 1),
            10
        );

        // Ligne qui permet de récuperer dans la base tout les données voulu
        $user = $usersRepository->findBy(['active' => 1]);

        // Formulaire avec les différents champs et ce qu'ils doivent recevoir et les paramètres
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

        // Recupération et stockage des données reçu
        $form->handleRequest($request);
        // Conditions vérifiant si le formulaire à bien était envoyé et qu'il est valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Si c'est le cas ont stock la données du formulaire dans une variable
            $surnameUser = $form['surname']->getData();
        }

        // Permet de récuperer l'utilisateur en cours
        $users = $this->getUser();
        // Permet de faire le rendu donc de dire à quelle vue cette fonction appartient avec ses paramètres
        return $this->render('member/listeUsers.html.twig', [
            "title" => "Liste Utilisateurs",
            "surnameUser" => $surnameUser,
            "users" => $users,
            "user" => $pagin,
            "form" => $form->createView()
        ]);
    }

    /**
     * Cette function permet de crée un nouvelle article
     * @Route("/newArticle", name="create_article_admin")
     */
    public function new(ObjectManager $manager, Request $request)
    {
        // On instancie la class voulu
        $articles = new Articles();

        // Permet de récuperer l'utilisateur en cours
        $users = $this->getUser();

        // On défini une variable comprenant la date en PHP
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
            // On défini les valeurs par défaut qui ne bougerons pas
            $articles->setActive(0);
            $articles->setDate($date);
            $articles->setUsers($users);

            // On fait persister dans la base
            $manager->persist($articles);
            // Puis on envoie dans la bdd
            $manager->flush();

            return $this->redirectToRoute('homepage');
        }
        return $this->render('member/new.html.twig', [
            "title" => "Création d'article",
            "users" => $users,
            "form" => $form->createView()
        ]);
    }

    /**
     * Cette function permet d'afficher la page d'accueil (Qui est exactement la même pour les membres)
     * @Route("/", name="homepage_admin")
     */
    public function index(ArticlesRepository $articlesRepository, CategoryRepository $categoryRepository, PaginatorInterface $paginator, Request $request)
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
            "categories" => $categoryRepository->findBy(['active' => 1])
        ]);
    }

    /**
     * Cette function permets d'afficher un article avec ses infos uniquement
     * @Route("/{id}", name="one_admin")
     */
    public function one(ArticlesRepository $articlesRepository, CommentsRepository $commentsRepository, Request $request, ObjectManager $manager, PaginatorInterface $paginator, $id)
    {

        $comments = new Comments();

        $users = $this->getUser();

        // Permet de récuperer les données en fonction de l'id récuperer dans l'URL
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
            "title" => "Un article",
            "users" => $users,
            "articles" => $articlesRepository->findBy(['id' => $id]),
            'form' => $form->createView(),
            'comments' => $pagin
        ]);
    }

    /**
     * Cette function est la page d'accueil côté Admin (Qui est exactement la même pour les membres), où il y a un
     * système de filtre
     * @Route("/", name="homepage_admin")
     */
    public function filter(ArticlesRepository $articlesRepository, CategoryRepository $categoryRepository, PaginatorInterface $paginator, Request $request)
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
     * @Route("/article/{id}/like", name="article_like_admin")
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
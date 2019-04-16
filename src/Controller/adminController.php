<?php

namespace App\Controller;

use App\Entity\Articles;
use App\Entity\Category;
use App\Entity\Comments;
use App\Repository\ArticlesRepository;
use App\Repository\CategoryRepository;
use App\Repository\CommentsRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin")
 */
class adminController extends AbstractController
{
    /**
     * @Route("/index", name="panel_admin")
     */
    public function panel()
    {
        $users = $this->getUser();
        return $this->render('admin/index.html.twig', [
            "title" => "Admin Home",
            "users" => $users
        ]);
    }

    /**
     * @Route("/newArticle", name="create_article_admin")
     */
    public function new(ObjectManager $manager, Request $request)
    {
        $articles = new Articles();

        $users = $this->getUser();

        $date = date("d-m-Y H:i:s");

        $form = $this->createFormBuilder($articles)
            ->add('name', TextType::class)
            ->add('picture', TextType::class)
            ->add('description', TextType::class)
            ->add('namelatin', TextType::class)
            ->add('toxicite', TextType::class)
            ->add('environnement', TextType::class)
            ->add('urlBuy', TextType::class)
            ->add('categories', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name'])
            ->add('Send', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $articles->setActive(0);
            $articles->setDate($date);
            $articles->setUsers($users);

            $manager->persist($articles);
            $manager->flush();

            return $this->redirectToRoute('homepage');
        }
        return $this->render('member/new.html.twig', [
            "title" => "Admin Home",
            "users" => $users,
            "form" => $form->createView()
        ]);
    }

    /**
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
            "title" => "Home",
            "users" => $users,
            "articles" => $pagin,
            "categories" => $categoryRepository->findBy(['active' => 1])
        ]);
    }

    /**
     * @Route("/{id}", name="one_admin")
     */
    public function one(ArticlesRepository $articlesRepository, CommentsRepository $commentsRepository, Request $request, ObjectManager $manager, PaginatorInterface $paginator, $id)
    {

        $comments = new Comments();

        $users = $this->getUser();

        $idA = $articlesRepository->find($id);

        $date = date("d-m-Y H:i:s");

        $form = $this->createFormBuilder($comments)
            ->add('content', TextareaType::class)
            ->add('Send', SubmitType::class)
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
            "title" => "One",
            "users" => $users,
            "articles" => $articlesRepository->findBy(['id' => $id]),
            'form' => $form->createView(),
            'comments' => $pagin
        ]);
    }
}
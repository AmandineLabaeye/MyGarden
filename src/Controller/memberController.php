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
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/member")
 */
class memberController extends AbstractController
{
    /**
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
            "title" => "Home",
            "users" => $users,
            "articles" => $pagin,
            "categories" => $categoryRepository->findBy(["active" => 1])
        ]);
    }

    /**
     * @Route("/{id}/edit", name="articles_edit_member")
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

            return $this->redirectToRoute('homepage_member');
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

    /**
     * @Route("/comments/{id}/edit", name="comments_edit_member")
     */
    public function editC(Request $request, Comments $comments, ObjectManager $manager, $id)
    {
        $form = $this->createFormBuilder($comments)
            ->add('content', TextareaType::class)
            ->add('active', NumberType::class)
            ->add('Save', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->flush();

            return $this->redirectToRoute('homepage_member');
        }

        $users = $this->getUser();

        return $this->render("admin/Comments/edit.html.twig", [
            'title' => 'Edit Comment',
            "users" => $users,
            "form" => $form->createView(),
            "comment" => $comments
        ]);
    }

    /**
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
     * @Route("/member/newArticle", name="create_article_member")
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
}
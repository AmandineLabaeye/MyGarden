<?php

namespace App\Controller;

use App\Entity\Comments;
use App\Repository\CommentsRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/comments")
 */
class adminControllerComments extends AbstractController
{
    /**
     * @Route("/comments", name="comments")
     */
    public function index(CommentsRepository $commentsRepository, PaginatorInterface $paginator, Request $request)
    {
        $pagin = $paginator->paginate(
            $commentsRepository->findAll(),
            $request->query->getInt('page', 1),
            5
        );
        $users = $this->getUser();
        return $this->render('admin/Comments/index.html.twig', [
            'title' => "Index Comment",
            "users" => $users,
            "comments" => $pagin
        ]);
    }

    /**
     * @Route("/{id}", name="comments_show")
     */
    public function show(CommentsRepository $commentsRepository, $id)
    {
        $users = $this->getUser();
        return $this->render('admin/Comments/show.html.twig', [
            'title' => "Show Comment",
            "users" => $users,
            "comments" => $commentsRepository->findBy(['id' => $id])
        ]);
    }

    /**
     * @Route("/{id}/edit", name="comments_edit")
     */
    public function edit(Request $request, Comments $comments, ObjectManager $manager, $id)
    {
        $form = $this->createFormBuilder($comments)
            ->add('content', TextareaType::class)
            ->add('active', NumberType::class)
            ->add('Save', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->flush();

            return $this->redirectToRoute('comments_show', [
                'id' => $id
            ]);
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
     * @Route("/delete/{id}", name="comments_delete", methods={"DELETE"})
     */
    public function delete(Comments $comments, Request $request)
    {
        if ($this->isCsrfTokenValid('delete' . $comments->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($comments);
            $entityManager->flush();
        }
        return $this->redirectToRoute("comments");
    }

    /**
     * @Route("/comments/active", name="comments_active")
     */
    public function index_active(CommentsRepository $commentsRepository, PaginatorInterface $paginator, Request $request)
    {
        $pagin = $paginator->paginate(
            $commentsRepository->findBy(['active' => 0]),
            $request->query->getInt('page', 1),
            2
        );
        $users = $this->getUser();
        return $this->render('admin/Comments/active.html.twig', [
            "users" => $users,
            "title" => "Active Comments",
            "comments" => $pagin
        ]);
    }
}
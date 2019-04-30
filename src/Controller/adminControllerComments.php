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
use Symfony\Component\Routing\Annotation\Route;

/**
 * Ce controller permet la gestion des commentaires d'articles côte Admin
 * @Route("/admin/comments")
 */
class adminControllerComments extends AbstractController
{
    /**
     * Cette function permet d'afficher tous les commentaires avec un système de pagination
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
            'title' => "Index Commentaires",
            "users" => $users,
            "comments" => $pagin
        ]);
    }

    /**
     * Cette function permet de montrer un commentaire selon l'ID reçu uniqument
     * @Route("/{id}", name="comments_show")
     */
    public function show(CommentsRepository $commentsRepository, $id)
    {
        $users = $this->getUser();
        return $this->render('admin/Comments/show.html.twig', [
            'title' => "Voir un Commentaire",
            "users" => $users,
            "comments" => $commentsRepository->findBy(['id' => $id])
        ]);
    }

    /**
     * Cette function permet d'éditer un commentaire
     * @Route("/{id}/edit", name="comments_edit")
     */
    public function edit(Request $request, Comments $comments, ObjectManager $manager, $id)
    {
        $form = $this->createFormBuilder($comments)
            ->add('content', TextareaType::class)
            ->add('active', NumberType::class)
            ->add('Modifier', SubmitType::class)
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
            'title' => 'Modifier le Commentaire',
            "users" => $users,
            "form" => $form->createView(),
            "comment" => $comments
        ]);
    }

    /**
     * Cette function permet de supprimer un commentaire
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
            "title" => "Activation des Commentaires",
            "comments" => $pagin
        ]);
    }
}
<?php


namespace App\Controller;


use App\Entity\CommentsPublication;
use App\Entity\PublicationsProfil;
use App\Repository\CommentsPublicationRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/commentsP")
 */
class adminCommentsP extends AbstractController
{
    /**
     * @Route("/commentsP", name="commentsP")
     */
    public function index(CommentsPublicationRepository $commentsPublicationRepository, Request $request, PaginatorInterface $paginator)
    {
        $pagin = $paginator->paginate(
            $commentsPublicationRepository->findAll(),
            $request->query->getInt('page', 1),
            5
        );
        $users = $this->getUser();
        return $this->render('admin/CommentsP/index.html.twig', [
            "title" => "Comments Publications",
            "users" => $users,
            "commentsP" => $pagin
        ]);
    }

    /**
     * @Route("/{id}", name="commentsP_show")
     */
    public function show(CommentsPublicationRepository $commentsPublicationRepository, $id)
    {
        $users = $this->getUser();
        return $this->render('admin/CommentsP/show.html.twig', [
            'title' => "show",
            "users" => $users,
            "commentsP" => $commentsPublicationRepository->findBy(["id" => $id])
        ]);
    }

    /**
     * @Route("/{id}/edit", name="commentsP_edit")
     */
    public function edit(CommentsPublication $commentsPublication, Request $request, ObjectManager $manager, $id)
    {
        $form = $this->createFormBuilder($commentsPublication)
            ->add('content', TextareaType::class)
            ->add('Update', SubmitType::class)
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager->flush();

            return $this->redirectToRoute('commentsP_show', [
                'id' => $id
            ]);
        }
        $users = $this->getUser();
        return $this->render('admin/CommentsP/edit.html.twig', [
            'title' => "Edit Comments",
            "users" => $users,
            "comment" => $commentsPublication,
            "form" => $form->createView()
        ]);
    }

    /**
     * @Route("/delete/{id}", name="commentsP_delete", methods={"DELETE"})
     */
    public function delete(Request $request, CommentsPublication $commentsPublication)
    {
        if ($this->isCsrfTokenValid('delete' . $commentsPublication->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($commentsPublication);
            $entityManager->flush();
        }
        return $this->redirectToRoute("commentsP");
    }


}
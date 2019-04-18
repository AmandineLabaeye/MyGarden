<?php


namespace App\Controller;


use App\Entity\CommentsPublication;
use App\Entity\PublicationsProfil;
use App\Repository\CommentsPublicationRepository;
use App\Repository\PublicationsProfilRepository;
use App\Repository\UsersRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/profile")
 */
class adminProfil extends AbstractController
{
    /**
     * @Route("/{id}", name="profile_users_admin")
     */
    public function profile(UsersRepository $usersRepository, PublicationsProfilRepository $profilRepository, Request $request, ObjectManager $manager, $id)
    {
        $users = $this->getUser();
        $publication = new PublicationsProfil();
        $date = date("d-m-Y H:i:s");
        $form = $this->createFormBuilder($publication)
            ->add('publication', TextareaType::class)
            ->add('picture', TextType::class)
            ->add('Poster', SubmitType::class)
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $publication->setUsers($users);
            $publication->setDate($date);
            $publication->setPage($id);
            $manager->persist($publication);
            $manager->flush();
            return $this->redirectToRoute('profile_users_admin', [
                'id' => $id
            ]);
        }
        return $this->render('member/profile.html.twig', [
            'title' => "Profile",
            "users" => $users,
            "user" => $usersRepository->findBy(['id' => $id]),
            'form' => $form->createView(),
            "publications" => $profilRepository->findBy(["page" => $id])
        ]);
    }

    /**
     * @Route("/publications/{id}", name="comments_publication_admin")
     */
    public function onePublication(PublicationsProfilRepository $profilRepository, CommentsPublicationRepository $commentsPublicationRepository, ObjectManager $manager, Request $request, $id)
    {
        $idP = $profilRepository->find($id);
        $users = $this->getUser();
        $date = date("d-m-Y H:i:s");
        $comments_publication = new CommentsPublication();
        $form = $this->createFormBuilder($comments_publication)
            ->add('content', TextareaType::class)
            ->add('Send', SubmitType::class)
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $comments_publication->setPublication($idP);
            $comments_publication->setUsers($users);
            $comments_publication->setDate($date);
            $manager->persist($comments_publication);
            $manager->flush();

            return $this->redirectToRoute('comments_publication_admin', [
                'id' => $id
            ]);
        }

        return $this->render("member/publication.html.twig", [
            'title' => "publication",
            "users" => $users,
            "publications" => $profilRepository->findBy(["id" => $id]),
            'form' => $form->createView(),
            "comments" => $commentsPublicationRepository->findBy(['publication' => $id])
        ]);
    }

}
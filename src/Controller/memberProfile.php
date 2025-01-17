<?php


namespace App\Controller;


use App\Entity\CommentsPublication;
use App\Entity\PublicationsProfil;
use App\Repository\CommentsPublicationRepository;
use App\Repository\PublicationsProfilRepository;
use App\Repository\UsersRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Ce controller permet la gestion des pages de profil côté Membre
 * @Route("/member/profile")
 */
class memberProfile extends AbstractController
{
    /**
     * Cette function affiche la page de profil et ses éléments
     * @Route("/{id}", name="profile_users")
     */
    public function profile(UsersRepository $usersRepository, PublicationsProfilRepository $profilRepository, Request $request, ObjectManager $manager, PaginatorInterface $paginator, $id)
    {
        $pagin = $paginator->paginate(
            $profilRepository->findBy(["page" => $id]),
            $request->query->getInt('page', 1),
            10
        );
        $users = $this->getUser();
        $publication = new PublicationsProfil();
        $date = date("d-m-Y H:i:s");
        $form = $this->createFormBuilder($publication)
            ->add('publication', TextareaType::class)
            ->add('picture', TextType::class, [
                'required' => false
            ])
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $publication->setUsers($users);
            $publication->setDate($date);
            $publication->setPage($id);

            $manager->persist($publication);
            $manager->flush();

            return $this->redirectToRoute('profile_users', [
                'id' => $id
            ]);
        }


        return $this->render('member/profile.html.twig', [
            'title' => "Profil",
            "users" => $users,
            "user" => $usersRepository->findBy(['id' => $id]),
            "publications" => $pagin,
            "public" => $id,
            'form' => $form->createView()
        ]);
    }

    /**
     * Cette function permet au utilisateurs de modifier leur informations de compte
     * @Route("/parameter/{id}", name="parametre_users_member")
     */
    public function parametre(Request $request, UsersRepository $usersRepository, ObjectManager $manager, UserPasswordEncoderInterface $encoder, $id)
    {
        $user = $this->getUser();
        $form = $this->createFormBuilder($user)
            ->add('avatar', TextType::class,[
                'required' => false
            ])
            ->add('name', TextType::class)
            ->add('surname', TextType::class)
            ->add('age', NumberType::class, [
                'required' => false
            ])
            ->add('region', TextType::class, [
                'required' => false
            ])
            ->add('ville', TextType::class, [
                'required' => false
            ])
            ->add('username', TextType::class)
            ->add('apropos', TextareaType::class)
            ->add('work', TextType::class, [
                'required' => false
            ])
            ->add('password', PasswordType::class)
            ->add('confirm_password', PasswordType::class)
            ->add('Modifier', SubmitType::class)
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $hash = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hash);
            $manager->persist($user);
            $manager->flush();
            return $this->redirectToRoute('profile_users', [
                'id' => $id
            ]);
        }
        return $this->render('member/parametre.html.twig', [
            'title' => "Paramètre Utilisateurs",
            "users" => $user,
            "user" => $usersRepository->findBy(['id' => $id]),
            'form' => $form->createView()
        ]);
    }

    /**
     * Cette function permet au utilisateur de supprimer leur compte
     * @Route("/delete/{id}", name="users_delete_member")
     */
    public function delete(ObjectManager $manager, UsersRepository $usersRepository, $id)
    {
        $users = $usersRepository->find($id);
        $currentUserId = $this->getUser()->getId();
        if ($currentUserId == $id) {
            $manager->remove($users);
            $session = $this->get('session');
            $session = new Session();
            $session->invalidate();
            $manager->flush();
            return $this->redirectToRoute("login");
        } else {
            return $this->redirectToRoute('homepage');
        }
    }

    /**
     * Cette function permet d'afficher une publication seul de la page de profil
     * @Route("/publications/{id}", name="comments_publication")
     */
    public function onePublication(PublicationsProfilRepository $profilRepository, CommentsPublicationRepository $commentsPublicationRepository, ObjectManager $manager, Request $request, PaginatorInterface $paginator, $id)
    {
        $pagin = $paginator->paginate(
            $commentsPublicationRepository->findBy(['publication' => $id]),
            $request->query->getInt('page', 1),
            5
        );
        $idP = $profilRepository->find($id);
        $users = $this->getUser();
        $date = date("d-m-Y H:i:s");
        $comments_publication = new CommentsPublication();
        $form = $this->createFormBuilder($comments_publication)
            ->add('content', TextareaType::class)
            ->add('Poster', SubmitType::class)
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $comments_publication->setPublication($idP);
            $comments_publication->setUsers($users);
            $comments_publication->setDate($date);
            $manager->persist($comments_publication);
            $manager->flush();

            return $this->redirectToRoute('comments_publication', [
                'id' => $id
            ]);
        }

        return $this->render("member/publication.html.twig", [
            'title' => "La publication",
            "users" => $users,
            "publications" => $profilRepository->findBy(["id" => $id]),
            'form' => $form->createView(),
            "comments" => $pagin
        ]);
    }
}
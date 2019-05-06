<?php

namespace App\Controller;

use App\Entity\Users;
use App\Repository\UsersRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Ce controller permet la gestion des Utilisateurs côté Admin
 * @Route("/admin/users")
 */
class adminControllerUsers extends AbstractController
{
    /**
     * Cette function permet d'afficher tous les utilisateurs avec un système de pagination
     * @Route("/users", name="users")
     */
    public function index(UsersRepository $usersRepository, Request $request, PaginatorInterface $paginator)
    {
        $pagin = $paginator->paginate(
            $usersRepository->findAll(),
            $request->query->getInt('page', 1),
            5
        );
        $users = $this->getUser();
        return $this->render('admin/Users/index.html.twig', [
            'title' => 'Tout les utilisateurs',
            'users' => $users,
            "user" => $pagin
        ]);
    }

    /**
     * Cette fonction permet de montrer un utilisateur selon l'ID reçu uniquement
     * @Route("/{id}", name="users_show")
     */
    public function show(UsersRepository $usersRepository, $id)
    {
        $users = $this->getUser();
        return $this->render('admin/Users/show.html.twig', [
            'title' => "Voir l'utilisateur",
            'users' => $users,
            "user" => $usersRepository->findBy(["id" => $id])
        ]);
    }

    /**
     * Cette function permet d'éditer un utilisateur
     * @Route("/{id}/edit", name="users_edit")
     */
    public function edit(Users $users, Request $request, ObjectManager $manager, $id)
    {
        $form = $this->createFormBuilder($users)
            ->add("name", TextType::class, [
                'label' => "Nom "
            ])
            ->add("surname", TextType::class, [
                'label' => "Prénom "
            ])
            ->add("email", TextType::class, [
                'label' => "Adresse Mail "
            ])
            ->add("age", NumberType::class, [
                'required' => false,
                'label' => "Age "
            ])
            ->add("region", TextType::class, [
                'required' => false,
                "label"=> "Region "
            ])
            ->add("ville", TextType::class, [
                'required' => false,
                'label' => "Ville "
            ])
            ->add("username", TextType::class, [
                'label' => "Nom d'utilisateur "
            ])
            ->add("apropos", TextareaType::class, [
                'label' => "Description "
            ])
            ->add("work", TextType::class, [
                'required' => false,
                'label' => "Profession "
            ])
            ->add("rank", TextType::class, [
                'label' => "Rôle "
            ])
            ->add("active", NumberType::class, [
                'label' => "Active "
            ])
            ->add("Modifier", SubmitType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->flush();
            return $this->redirectToRoute('users_show', [
                'id' => $id
            ]);
        }

        $user = $this->getUser();

        return $this->render('admin/Users/edit.html.twig', [
            'title' => "Modifier l'utilisateur",
            "users" => $user,
            "user" => $users,
            "form" => $form->createView()
        ]);
    }

    /**
     * Cette function permet de supprimer un utilisateur
     * @Route("/delete/{id}", name="users_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Users $users)
    {
        if ($this->isCsrfTokenValid('delete' . $users->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($users);
            $entityManager->flush();
        }
        return $this->redirectToRoute("users");
    }

    /**
     * Cette function permet d'afficher tous les utilisateurs à activé avec un système de pagination
     * @Route("/users/active", name="users_active")
     */
    public function index_active(UsersRepository $usersRepository, PaginatorInterface $paginator, Request $request)
    {
        $pagin = $paginator->paginate(
            $usersRepository->findBy(['active' => 0]),
            $request->query->getInt('page', 1),
            2
        );
        $users = $this->getUser();
        return $this->render('admin/Users/active.html.twig', [
            'title' => 'Activation des utilisateurs',
            "users" => $users,
            "user" => $pagin
        ]);
    }
}
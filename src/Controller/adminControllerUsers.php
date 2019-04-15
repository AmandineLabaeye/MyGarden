<?php

namespace App\Controller;

use App\Entity\Users;
use App\Repository\UsersRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/admin/users")
 */
class adminControllerUsers extends AbstractController
{
    /**
     * @Route("/users", name="users")
     */
    public function index(UsersRepository $usersRepository)
    {
        $users = $this->getUser();
        return $this->render('admin/Users/active.html.twig', [
            'title' => 'Tout les users',
            'users' => $users,
            "user" => $usersRepository->findAll()
        ]);
    }

    /**
     * @Route("/{id}", name="users_show")
     */
    public function show(UsersRepository $usersRepository, $id)
    {
        $users = $this->getUser();
        return $this->render('admin/Users/show.html.twig', [
            'title' => "Show Users",
            'users' => $users,
            "user" => $usersRepository->findBy(["id" => $id])
        ]);
    }

    /**
     * @Route("/{id}/edit", name="users_edit")
     */
    public function edit(Users $users, Request $request, ObjectManager $manager, $id)
    {
        $form = $this->createFormBuilder($users)
            ->add("name", TextType::class)
            ->add("surname", TextType::class)
            ->add("email", TextType::class)
            ->add("age", NumberType::class)
            ->add("region", TextType::class)
            ->add("ville", TextType::class)
            ->add("username", TextType::class)
            ->add("apropos", TextareaType::class)
            ->add("work", TextType::class)
            ->add("rank", TextType::class)
            ->add("active", NumberType::class)
            ->add("Save", SubmitType::class)
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
            'title' => 'Edit Users',
            "users" => $user,
            "user" => $users,
            "form" => $form->createView()
        ]);
    }

    /**
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
     * @Route("/users/active", name="users_active")
     */
    public function index_active(UsersRepository $usersRepository)
    {
        $users = $this->getUser();
        return $this->render('admin/Users/active.html.twig', [
            'title' => 'Active',
            "users" => $users,
            "user" => $usersRepository->findBy(['active' => 0])
        ]);
    }
}
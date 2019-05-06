<?php

namespace App\Controller;

use App\Entity\PublicationsProfil;
use App\Repository\PublicationsProfilRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Ce controller permet la gesion des Publications sur les pages de profil côté Admin
 * @Route("/admin/publications")
 */
class adminPublications extends AbstractController
{
    /**
     * Cette function permet d'afficher tous les publications avec un système de pagination
     * @Route("/publications", name="publications")
     */
    public function index(PublicationsProfilRepository $profilRepository, Request $request, PaginatorInterface $paginator)
    {
        $pagin = $paginator->paginate(
            $profilRepository->findAll(),
            $request->query->getInt('page', 1),
            5
        );
        $users = $this->getUser();
        return $this->render('admin/Publications/index.html.twig', [
            "title" => "Publications",
            "users" => $users,
            "publications" => $pagin
        ]);
    }

    /**
     * Cette function permet de montrer une publication selon l'ID reçu uniquement
     * @Route("/{id}", name="publications_show")
     */
    public function show(PublicationsProfilRepository $profilRepository, $id)
    {
        $users = $this->getUser();
        return $this->render('admin/Publications/show.html.twig', [
            'title' => "Voir la Publication",
            "users" => $users,
            "publications" => $profilRepository->findBy(['id' => $id])
        ]);
    }

    /**
     * Cette function permet d'éditer une publication
     * @Route("/{id}/edit", name="publications_edit")
     */
    public function edit(PublicationsProfil $profil, Request $request, ObjectManager $manager, $id)
    {
        $form = $this->createFormBuilder($profil)
            ->add('publication', TextareaType::class, [
                'label' => "Publication "
            ])
            ->add('picture', TextType::class, [
                'required' => false,
                'label' => "Photo "
            ])
            ->add('Modifier', SubmitType::class)
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager->flush();
            return $this->redirectToRoute('publications_show', [
                'id' => $id
            ]);
        }
        $users = $this->getUser();
        return $this->render('admin/Publications/edit.html.twig', [
            "title" => "Modifier la Publication",
            "users" => $users,
            "publication" => $profil,
            'form' => $form->createView()
        ]);
    }

    /**
     * Cette function permet de supprimer une publication
     * @Route("/delete/{id}", name="publications_delete", methods={"DELETE"})
     */
    public function delete(Request $request, PublicationsProfil $profil)
    {
        if ($this->isCsrfTokenValid('delete' . $profil->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($profil);
            $entityManager->flush();
        }
        return $this->redirectToRoute("publications");
    }

}
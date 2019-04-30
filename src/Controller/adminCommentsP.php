<?php


namespace App\Controller;


use App\Entity\CommentsPublication;
use App\Repository\CommentsPublicationRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Ce controller permet la gestion des commentaires des Publications côté Admin
 * @Route("/admin/commentsP")
 */
class adminCommentsP extends AbstractController
{
    /**
     * Cette function permet d'afficher tous les commentaires de Publications avec un système de pagination
     * @Route("/commentsP", name="commentsP")
     */
    public function index(CommentsPublicationRepository $commentsPublicationRepository, Request $request, PaginatorInterface $paginator)
    {
        // Fonction de pagination, 1er ce qu'il faut paginer, 2eme recupération de la page par défaut, 3eme combien
        // d'élement par page voulu
        $pagin = $paginator->paginate(
            $commentsPublicationRepository->findAll(),
            $request->query->getInt('page', 1),
            5
        );
        // Permet de récuperer l'utilisateur en cours
        $users = $this->getUser();
        // Permet de faire le rendu donc de dire à quelle vue cette fonction appartient avec ses paramètres
        return $this->render('admin/CommentsP/index.html.twig', [
            "title" => "Commentaires des Publications",
            "users" => $users,
            "commentsP" => $pagin
        ]);
    }

    /**
     * Cette function permet de montrer un commentaire selon l'ID reçu uniquement
     * @Route("/{id}", name="commentsP_show")
     */
    public function show(CommentsPublicationRepository $commentsPublicationRepository, $id)
    {
        // Permet de récuperer l'utilisateur en cours
        $users = $this->getUser();
        // Permet de faire le rendu donc de dire à quelle vue cette fonction appartient avec ses paramètres
        return $this->render('admin/CommentsP/show.html.twig', [
            'title' => "Voir",
            "users" => $users,
            "commentsP" => $commentsPublicationRepository->findBy(["id" => $id])
        ]);
    }

    /**
     * Cette function permet d'éditer un commentaire
     * @Route("/{id}/edit", name="commentsP_edit")
     */
    public function edit(CommentsPublication $commentsPublication, Request $request, ObjectManager $manager, $id)
    {
        // Formulaire avec les différents champs et ce qu'ils doivent recevoir
        $form = $this->createFormBuilder($commentsPublication)
            ->add('content', TextareaType::class)
            ->add('Modifier', SubmitType::class)
            ->getForm();
        // Recupération et stockage des données reçu
        $form->handleRequest($request);
        // Conditions vérifiant si le formulaire à bien était envoyé et qu'il est valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Si c'est la cas, ça envoie les données dans la base de données
            $manager->flush();

            // Redirige vers une page une fois le formulaire envoyé
            return $this->redirectToRoute('commentsP_show', [
                'id' => $id
            ]);
        }
        // Permet de récuperer l'utilisateur en cours
        $users = $this->getUser();
        // Permet de faire le rendu donc de dire à quelle vue cette fonction appartient avec ses paramètres
        return $this->render('admin/CommentsP/edit.html.twig', [
            'title' => "Modifier les Commentaires",
            "users" => $users,
            "comment" => $commentsPublication,
            "form" => $form->createView()
        ]);
    }

    /**
     * Cette function permet de supprimer un commentaire
     * @Route("/delete/{id}", name="commentsP_delete", methods={"DELETE"})
     */
    public function delete(Request $request, CommentsPublication $commentsPublication)
    {
        // Condition qui vérifie que c'est la même id des deux côtés
        if ($this->isCsrfTokenValid('delete' . $commentsPublication->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            // Si oui, on remove l'objet
            $entityManager->remove($commentsPublication);
            // Et on actualise la base de données
            $entityManager->flush();
        }
        // Puis on redirige sur une autre page
        return $this->redirectToRoute("commentsP");
    }


}
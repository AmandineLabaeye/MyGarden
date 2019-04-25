<?php


namespace App\Controller;

use App\Entity\PublicationsProfil;
use App\Repository\ArticlesRepository;
use App\Repository\PublicationsProfilRepository;
use App\Repository\UsersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/ajax")
 */
class ajaxController extends AbstractController
{
    /**
     * @Route("/add/{id}/publicationsP",name="add_publicationsP", condition="request.isXmlHttpRequest()")
     */
    //public function addPublication(PublicationsProfilRepository $profilRepository, UsersRepository $usersRepository, Request $request, $id): Response
    //{
        /*profils = $profilRepository->findBy(["page" => $id]);


        foreach ($profils as $profil) {
             $publicationM = ["publication" => $profil->getPublication(), "image" => $profil->getPicture(), "date" => $profil->getDate()];
         }


        $users = $this->getUser();

        $publication = new PublicationsProfil();

        $date = date("d-m-Y H:i:s");

        $form = $this->createFormBuilder($publication, [
            'action' => $this->generateUrl($request->get('_route'))
        ])
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

            $this->getDoctrine()->getManager()->persist($publication);
            $this->getDoctrine()->getManager()->flush();
            return new Response('success');
        }

        return $this->json([
            'code' => 200,
            'message' => "Message bien publié",
            'publication' => $publicationM,
        ], 200);
        return $this->render('member/profile.html.twig', [
            'form' => $form->createView(),
            'title' => "Profile",
            "users" => $users,
            "user" => $usersRepository->findBy(['id' => $id]),
            "publication" => $profils
        ]);*/
      //  return new Response('Ajax Publication');
    //}

    /**
     * @Route("/add/{id}/commentP", name="add_commentP")
     */
    //public function AddComment()
    //{

    //}

    /**
     * @Route("/caroussel", name="caroussel")
     */
    /*public function caroussel(ArticlesRepository $articlesRepository)
    {
        $articles = $articlesRepository->findAll();
        for ($i = 0; $i == count($articles); $i++) {
            $articlesP = [$articles[$i]->getPicture()];
        }

        return $this->json([
            'code' => 200,
            'message' => "Photo bien reçu",
            'article' => $articlesP
        ], 200);
    }*/
}
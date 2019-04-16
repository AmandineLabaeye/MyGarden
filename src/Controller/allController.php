<?php


namespace App\Controller;


use App\Entity\Users;
use App\Repository\ArticlesRepository;
use App\Repository\CategoryRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class allController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function index(ArticlesRepository $articlesRepository, CategoryRepository $categoryRepository)
    {
        $users = $this->getUser();
        return $this->render("home.html.twig", [
            "title" => "Home",
            "users" => $users,
            "articles" => $articlesRepository->findBy(['active' => 1]),
            "categories" => $categoryRepository->findBy(["active" => 1])
        ]);
    }

    /**
     * @Route("/petf/{id}", name="categories_pf")
     */
    public function plantesfleurs(ArticlesRepository $articlesRepository, $id)
    {
        $users = $this->getUser();
        return $this->render('allMember/petf.html.twig', [
            'title' => "Plantes",
            "users" => $users,
            "articles" => $articlesRepository->findBy(['categories' => $id])
        ]);
    }

    /**
     * @Route("/registration", name="registration")
     */
    public function registration(Request $request, ObjectManager $manager, UserPasswordEncoderInterface $encoder)
    {
        $users = new Users();

        $user = $this->getUser();

        $form = $this->createFormBuilder($users)
            ->add('avatar', TextType::class)
            ->add('name', TextType::class)
            ->add('surname', TextType::class)
            ->add('email', TextType::class)
            ->add('age', NumberType::class)
            ->add('region', TextType::class)
            ->add('ville', TextType::class)
            ->add('username', TextType::class)
            ->add('work', TextType::class)
            ->add('password', PasswordType::class)
            ->add('confirm_password', PasswordType::class)
            ->add('apropos', TextareaType::class)
            ->add('Inscription', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hash = $encoder->encodePassword($users, $users->getPassword());
            $users->setPassword($hash);
            $users->setRank("ROLE_USER");
            $users->setActive("1");
            $manager->persist($users);
            $manager->flush();

            return $this->redirectToRoute('login');
        }

        return $this->render("Visitor/inscription.html.twig", [
            "title" => "Registration",
            'form' => $form->createView(),
            "users" => $user
        ]);
    }

    /**
     * @Route("/login", name="login", methods={"POST", "GET"})
     */
    public function login()
    {
        $users = $this->getUser();
        return $this->render('Visitor/connexion.html.twig', [
            "title" => "Login",
            "users" => $users
        ]);
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logout()
    {
    }
}
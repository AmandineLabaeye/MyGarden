<?php


namespace App\Controller;


use App\Actions\email;
use App\Entity\Articles;
use App\Entity\Users;
use App\Repository\ArticlesRepository;
use App\Repository\CategoryRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Knp\Component\Pager\PaginatorInterface;
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
    public function index(ArticlesRepository $articlesRepository, CategoryRepository $categoryRepository, Request $request, PaginatorInterface $paginator)
    {
        $pagin = $paginator->paginate(
            $articlesRepository->findBy(["active" => 1]),
            $request->query->getInt('page', 1),
            2
        );
        $users = $this->getUser();
        return $this->render("home.html.twig", [
            "title" => "Home",
            "users" => $users,
            "articles" => $pagin,
            "categories" => $categoryRepository->findBy(["active" => 1])
        ]);
    }

    /**
     * @Route("/petf/{id}", name="categories_pf")
     */
    public function plantesfleurs(ArticlesRepository $articlesRepository, Request $request, PaginatorInterface $paginator, $id)
    {
        $pagin = $paginator->paginate(
            $articlesRepository->findBy(['categories' => $id]),
            $request->query->getInt('page', 1),
            2
        );
        $users = $this->getUser();
        return $this->render('allMember/petf.html.twig', [
            'title' => "Plantes",
            "users" => $users,
            "articles" => $pagin
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
            ->add('avatar', TextType::class, [
                'required' => false
            ])
            ->add('name', TextType::class)
            ->add('surname', TextType::class)
            ->add('email', TextType::class)
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
            ->add('work', TextType::class, [
                'required' => false
            ])
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

    /**
     * @Route("/contact", name="contact_form")
     */
    public function contact()
    {
        $users = $this->getUser();

        return $this->render('allMember/contact.html.twig', [
            'title' => "Contact",
            "users" => $users
        ]);
    }

    /**
     * @Route("/sendmail" , name="send_mail" , methods="POST")
     */
    public function send_mail(Request $request, \Swift_Mailer $mailer, \Twig_Environment $templating)
    {

        //Methode alternative , récuperation du conteneur twig et injection
        // $templating = $this->container->get('twig');

        $mail = new email($templating);
        $mail->sendMail($request, $mailer);

    }

    /**
     * @Route("/", name="homepage")
     */
    public function filter(ArticlesRepository $articlesRepository, CategoryRepository $categoryRepository, PaginatorInterface $paginator, Request $request)
    {
        $nameArticle = null;

        $pagin = $paginator->paginate(
            $articlesRepository->findBy(["active" => 1]),
            $request->query->getInt('page', 1),
            2
        );

        $articles = $articlesRepository->findBy(["active" => 1]);

        $form = $this->createFormBuilder($articles)
            ->add('name', TextType::class, [
                'label' => " ",
                'attr' => [
                    'placeholder' => "Nom de l'article"
                ]
            ])
            ->add('Search', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $nameArticle = $form['name']->getData();
        }

        $users = $this->getUser();

        return $this->render('home.html.twig', [
            "title" => "Home",
            "users" => $users,
            "categories" => $categoryRepository->findBy(["active" => 1]),
            'nameArticle' => $nameArticle,
            'articles' => $pagin,
            'form' => $form->createView()
        ]);
    }

}
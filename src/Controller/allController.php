<?php


namespace App\Controller;


use App\Entity\Users;
use App\Repository\ArticlesRepository;
use App\Repository\CategoryRepository;
use App\Repository\UsersRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Swift_SmtpTransport;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Ce controller permet la gestion des pages visiteurs
 * Class allController
 * @package App\Controller
 */
class allController extends AbstractController
{
    /**
     * Cette function permet d'afficher la page d'accueil
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
            "title" => "Page d'accueil",
            "users" => $users,
            "articles" => $pagin,
            "categories" => $categoryRepository->findBy(["active" => 1])
        ]);
    }

    /**
     * Cette function permet d'afficher les articles en fonction de l'id de catégories
     * @Route("/petf/{id}", name="categories_pf")
     */
    public function plantesfleurs(ArticlesRepository $articlesRepository, Request $request, PaginatorInterface $paginator, CategoryRepository $categoryRepository, $id)
    {
        $pagin = $paginator->paginate(
            $articlesRepository->findBy(['categories' => $id, "active" => 1]),
            $request->query->getInt('page', 1),
            2
        );
        $users = $this->getUser();
        return $this->render('allMember/petf.html.twig', [
            'title' => "Plantes Ou Fleurs",
            "users" => $users,
            "articles" => $pagin,
            "categories" => $categoryRepository->findBy(["active" => 1])
        ]);
    }

    /**
     * Cette function permet de s'inscrire au site
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
            "title" => "Inscription",
            'form' => $form->createView(),
            "users" => $user
        ]);
    }

    /**
     * Cette function permet de se connecter
     * @Route("/login", name="login", methods={"POST", "GET"})
     */
    public function login()
    {
        $users = $this->getUser();
        return $this->render('Visitor/connexion.html.twig', [
            "title" => "Connexion",
            "users" => $users
        ]);
    }

    /**
     * Cette function permet de se déconnecter
     * @Route("/logout", name="logout")
     */
    public function logout()
    {
    }

    /**
     * Cette function permet de nous contacter
     * @Route("/contact", name="contact_form")
     */
    public function contact(Request $request)
    {
        $users = $this->getUser();

        $form = $this->createFormBuilder()
            ->add('Nom', TextType::class, [
                'label' => " ",
                'attr' => [
                    'placeholder' => "Nom"
                ]
            ])
            ->add('Mail', TextType::class, [
                'label' => " ",
                'attr' => [
                    'placeholder' => "Adresse Email"
                ]
            ])
            ->add('Objet', TextType::class, [
                'label' => " ",
                'attr' => [
                    'placeholder' => "Objet"
                ]
            ])
            ->add('Message', TextareaType::class, [
                'label' => " ",
                'attr' => [
                    'placeholder' => "Message"
                ]
            ])
            ->add('Envoyer', SubmitType::class)
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $Objet = $form['Objet']->getData();
            $Nom = $form['Nom']->getData();
            $Message = $form['Message']->getData();
            $mail = $form['Mail']->getData();
            $transport = (new Swift_SmtpTransport( 'smtp.gmail.com', 465, 'ssl'))
                ->setUsername('mygardenbyamandine@gmail.com')
                ->setPassword('mygarden2302');

            $mailer = (new \Swift_Mailer($transport));

            $message = (new \Swift_Message('My Garden'))
                ->setSubject($Objet)
                ->setFrom($mail)
                ->setTo(['mygardenbyamandine@gmail.com' => 'TEST'])
                ->setBody($this->renderView(
                        'email/email.html.twig', [
                            'message' => nl2br($Message),
                            'user' => $mail,
                            'nom' => $Nom,
                            'date' => date("d-m-Y H:i:s")
                        ]
                    ),
                    'text/html'
                );
            $mailer->send($message);

            return $this->render('Visitor/aprescontact.html.twig', [
                "title" => "Après Contact",
                'users' => $users
            ]);
        }

        return $this->render('allMember/contact.html.twig', [
            'title' => "Contactez-nous",
            "users" => $users,
            'form' => $form->createView()
        ]);
    }

    /**
     * Cette function permet d'afficher la page d'accueil avec un système de filtre
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
            ->add('Rechercher', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $nameArticle = $form['name']->getData();
        }

        $users = $this->getUser();

        return $this->render('home.html.twig', [
            "title" => "Page d'accueil",
            "users" => $users,
            "categories" => $categoryRepository->findBy(["active" => 1]),
            'nameArticle' => $nameArticle,
            'articles' => $pagin,
            'form' => $form->createView()
        ]);
    }

    /**
     * Function qui gère la page du mot de passe oublié
     *
     * @Route("/password", name="forgot_password")
     */
    public function ForgotPassword(Request $request, \Swift_Mailer $mailer, UsersRepository $usersRepository, ObjectManager $manager)
    {
        $users = new Users();
        $date = date("H:i:s");
        $form = $this->createFormBuilder()
            ->add('Mail', TextType::class, [
                'label' => " ",
                "attr" => [
                    "placeholder" => 'Adresse Mail'
                ]
            ])
            ->add('Envoyer', SubmitType::class)
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form['Mail']->getData();
            if ($email == $usersRepository->findBy(['email' => $email])) {
                $name = $users->getUsername();
                $id = $users->getId();
                $Objet = 'Réinitialisation de votre mot de passe';
                $Nom = $name;
                $Email = $email;
                $Message = "Bonjour $Nom, <br>
                            Nous avons reçu une demande de réinitialisation de votre mot de passe <br> 
                            Cliquez simplement sur le lien ci-dessous pour créer un nouveau mot de passe <br>
                            <a href='/reinitialisation/$id/$date/password'> Modifier votre mot de passe </a> <br> 
                            Si finalement vous ne voulais pas modifier votre mot de passe pas de panique votre ancien 
                            mot de passe et toujours valable! <br>
                            Très bonne journée, à bientôt";

                $message = (new \Swift_Message($Objet))
                    ->setFrom([$Email => $Nom])
                    ->setTo(['noreply@mygarden.fr' => 'Gérant'])
                    ->setBody(
                        $this->renderView(
                        // templates/emails/registration.html.twig
                            'email/email.html.twig',
                            ['message' => nl2br($Message)]
                        ),
                        'text/html'
                    )
                    ->addPart(
                        $this->renderView(
                            'email/email.txt.twig',
                            ['message' => $Message]
                        ),
                        'text/plain'
                    );

                $mailer->send($Message);

                $users->setVerif($date);
                $manager->persist($users);
                $manager->flush();

                return $this->redirectToRoute('homepage');
            }
        }

        return $this->render('Visitor/password.html.twig', [
            'title' => "Mot de passe oublié",
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/reinitialisation/{id}/{date}/password", name="reinitialisation_password")
     */
    public function ReinitialisationMdp(Request $request, ObjectManager $manager, Users $users, UserPasswordEncoderInterface $encoder, UsersRepository $usersRepository, $date)
    {
        $form = $this->createFormBuilder($users)
            ->add('password', PasswordType::class, [
                'label' => " ",
                "attr" => [
                    'placeholder' => "Nouveau mot de passe"
                ]
            ])
            ->add('confirm_password', PasswordType::class, [
                'label' => " ",
                "attr" => [
                    'placeholder' => "Confirmation du nouveau mot de passe"
                ]
            ])
            ->add('Valider', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hash = $encoder->encodePassword($users, $users->getPassword());
            $users->setPassword($hash);
            $manager->flush();

            return $this->redirectToRoute('login');
        }

        $user = $this->getUser();
        return $this->render('Visitor/editpassword.html.twig', [
            'title' => "Modifier password",
            'form' => $form->createView(),
            "users" => $user,
            'date' => $date,
            'user' => $usersRepository->findBy(['active' => 1])
        ]);
    }
}
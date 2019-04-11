<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/member")
 */
class memberController extends AbstractController
{
    /**
     * @Route("/", name="homepage_member")
     */
    public function index()
    {
        $users = $this->getUser();
        return $this->render('member/index.html.twig', [
            "title" => "Admin Home",
            "users" => $users
        ]);
    }
}
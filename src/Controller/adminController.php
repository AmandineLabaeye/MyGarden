<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin")
 */
class adminController extends AbstractController
{
    /**
     * @Route("/", name="homepage_admin")
     */
    public function index()
    {
        $users = $this->getUser();
        return $this->render('admin/index.html.twig', [
            "title" => "Admin Home",
            "users" => $users
        ]);
    }

}
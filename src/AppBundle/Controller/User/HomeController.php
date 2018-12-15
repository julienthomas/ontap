<?php

namespace AppBundle\Controller\User;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class HomeController extends Controller
{
    /**
     * @Route("/user", name="user_homepage")
     */
    public function homeAction()
    {
        return $this->render('user/home/home.html.twig');
    }
}

<?php

namespace AppBundle\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class HomeController extends Controller
{
    /**
     * @Route("/admin", name="admin_home")
     */
    public function homeAction()
    {
        $this->get('ontap.service.user')->findOrCreateUser('julien.thomas0@gmail.com');

        $stats = $this->get('ontap.service.admin_dashboard')->getDashboardStats($this->getUser()->getLanguage());

        return $this->render('admin/home/home.html.twig', ['stats' => $stats]);
    }
}

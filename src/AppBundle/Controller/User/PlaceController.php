<?php

namespace AppBundle\Controller\User;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class PlaceController extends Controller
{
    /**
     * @Route("/user/places", name="user_place")
     */
    public function homeAction()
    {
        return $this->render('user/place/list.html.twig');
    }
}

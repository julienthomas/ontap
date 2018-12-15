<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Language;
use AppBundle\Entity\Place;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PlaceController extends Controller
{
    /**
     * @Route("/information/{id}", name="place_information")
     * @Route("/admin/place/information/{id}", name="admin_place_information", defaults={"isAdmin" = true})
     * @Route("/user/place/information", name="user_place_information", defaults={"isUser" = true})
     *
     * @param Request $request
     * @param $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function informationAction(Request $request, $id = null, $isAdmin = false, $isUser = false)
    {
        $language = $this->getUser() ? $this->getUser()->getLanguage() :
            $this->getDoctrine()->getManager()->getRepository(Language::class)->findOneByLocale($request->getLocale());
        if (!$language) {
            $language = $this->getDoctrine()->getManager()->getRepository(Language::class)->findOneByLocale('fr_FR');
        }
        $place = null;
        if ($id) {
            $place = $this->getDoctrine()->getManager()->getRepository(Place::class)->getPlaceInformation($id, $language);
        } elseif ($isUser) {
            $place = $this->getUser()->getPlace();
        }
        if (!$place) {
            throw new NotFoundHttpException();
        }
        $schedules = $this->get('ontap.service.place')->buildScheduleArray($place->getSchedules());
        $editRoute = null;
        $layout = 'layout.html.twig';
        if ($isAdmin) {
            $layout = 'layout_admin.html.twig';
            $editRoute = $this->get('router')->generate('admin_place_edit', ['id' => $id]);
        } elseif ($isUser) {
            $layout = 'layout_user.html.twig';
            $editRoute = '#';
        }

        return $this->render('place/information.html.twig', [
            'editRoute' => $editRoute,
            'layout' => $layout,
            'place' => $place,
            'schedules' => $schedules,
        ]);
    }
}

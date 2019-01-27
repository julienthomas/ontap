<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Beer;
use AppBundle\Entity\Brewery;
use AppBundle\Entity\Place;
use AppBundle\Form\PlaceType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class PlaceController extends Controller
{
    /**
     * @Route("/admin/place", name="admin_place")
     */
    public function listAction()
    {
        return $this->render('admin/place/list.html.twig');
    }

    /**
     * @Route("/admin/place/new-refresh", name="admin_place_new_refresh")
     * @Method("POST")
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function newListRefreshAction(Request $request)
    {
        $data = $this->get('ontap.service.place')->getNewList($request->request->all());

        return new JsonResponse($data);
    }

    /**
     * @Route("/admin/place/refresh", name="admin_place_refresh")
     * @Method("POST")
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function listRefreshAction(Request $request)
    {
        $data = $this->get('ontap.service.place')->getList($request->request->all());

        return new JsonResponse($data);
    }

    /**
     * @Route("/admin/place/add", name="admin_place_add")
     * @Route("/admin/place/edit/{id}", name="admin_place_edit")
     *
     * @param Request $request
     * @param Place   $place
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function addEditAction(Request $request, Place $place = null)
    {
        $placeService = $this->get('ontap.service.place');
        $isNew = false;
        if (null === $place) {
            $place = $placeService->initPlace();
            $isNew = true;
        }

        $ownerEmail = $place->getOwner() ? $place->getOwner()->getEmail() : null;
        $basePictures = $placeService->getCurrentPictures($place);
        $formType = new PlaceType($this->getUser()->getLanguage(), $this->get('ontap.service.place'), true, $ownerEmail);
        $placeForm = $this->createForm($formType, $place);
        $beerForm = $this->createForm($this->get('ontap.form.beer'), new Beer());
        $beerTypeForm = $this->createForm($this->get('ontap.form.beer_type'), new Beer\Type());
        $breweryForm = $this->createForm($this->get('ontap.form.brewery'), new Brewery());
        $breweries = $this->getDoctrine()->getManager()->getRepository(Brewery::class)->getBreweriesWithBeers();

        $placeForm->handleRequest($request);
        if ($placeForm->isSubmitted()) {
            $translator = $this->get('translator');
            $flashbag = $this->get('session')->getFlashBag();
            if ($placeForm->isValid()) {
                $placeService->savePlace($place);
                $placeService->setPlaceOwner($place, $placeForm->get('owner')->getData());
                $placeService->deleteUnusedPictures($place, $basePictures);
                $msg = $isNew ? $translator->trans('Place successfully added.') : $translator->trans('Place successfully edited.');
                $flashbag->add('success', $msg);

                return $this->redirectToRoute('admin_place');
            } else {
                $flashbag->add('error', $translator->trans('Some fields are invalids.'));
            }
        }

        return $this->render('admin/place/add_edit.html.twig', [
            'isNew' => $isNew,
            'place' => $place,
            'breweries' => $breweries,
            'form' => $placeForm->createView(),
            'beerForm' => $beerForm->createView(),
            'beerTypeForm' => $beerTypeForm->createView(),
            'breweryForm' => $breweryForm->createView(),
            'addOwner' => true,
        ]);
    }

    /**
     * @Route("/admin/place/image-upload", name="admin_place_image_upload")
     *
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @throws \Exception
     */
    public function imageUploadAction(Request $request)
    {
        $asset = $this->get('ontap.service.place')->uploadImage($request->files->get('image'));
        if (!$asset) {
            throw new \Exception('Invalid image');
        }

        return new JsonResponse(['file' => $asset]);
    }
}

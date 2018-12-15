<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Brewery;
use AppBundle\Entity\Country;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class BreweryController extends Controller
{
    /**
     * @Route("/admin/brewery", name="admin_brewery")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction()
    {
        $countries = $this->getDoctrine()->getManager()->getRepository(Country::class)
            ->getCountriesWithTranslation($this->getUser()->getLanguage());
        $countriesData = [];
        /** @var Country $country */
        foreach ($countries as $country) {
            $countriesData[$country->getId()] = $country->getTranslations()->first()->getName();
        }

        return $this->render('admin/brewery/list.html.twig', ['countries' => $countriesData]);
    }

    /**
     * @Route("/admin/brewery/refresh", name="admin_brewery_refresh")
     * @Method("POST")
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function listRefreshAction(Request $request)
    {
        $data = $this->get('ontap.service.brewery')->getList($request->request->all(), $this->getUser()->getLanguage());

        return new JsonResponse($data);
    }

    /**
     * @Route("/admin/brewery/add", name="admin_brewery_add")
     * @Route("/admin/brewery/edit/{id}", name="admin_brewery_edit")
     *
     * @param Request $request
     * @param Brewery $brewery
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addEditAction(Request $request, Brewery $brewery = null)
    {
        $isNew = false;
        if (null === $brewery) {
            $brewery = new Brewery();
            $isNew = true;
        }

        $form = $this->createForm($this->get('ontap.form.brewery'), $brewery);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $translator = $this->get('translator');
            $flashbag = $this->get('session')->getFlashBag();
            if ($form->isValid()) {
                $this->get('ontap.service.brewery')->saveBrewery($brewery);
                $msg = $isNew ? $translator->trans('Brewery successfully added.') : $translator->trans('Brewery successfully edited.');
                $flashbag->add('success', $msg);

                return $this->redirectToRoute('admin_brewery');
            } else {
                $flashbag->add('error', $translator->trans('Some fields are invalids.'));
            }
        }

        return $this->render('admin/brewery/add_edit.html.twig', [
            'form' => $form->createView(),
            'isNew' => $isNew,
        ]);
    }

//    /**
//     * @Route("/admin/beer-type/import", name="admin_beer_type_import")
//     * @param Request $request
//     * @return \Symfony\Component\HttpFoundation\Response
//     */
//    public function importAction(Request $request)
//    {
//        return $this->render('admin/beer_type/import.html.twig');
//    }
//
//    /**
//     * @Route("/admin/beer-type/import/process", name="admin_beer_type_import_process")
//     * @param Request $request
//     * @return \Symfony\Component\HttpFoundation\Response
//     */
//    public function importProcessAction(Request $request)
//    {
//        $beerTypeService = $this->get('ontap.service.beer_type');
//        $file = $request->files->get('file');
//        if (($types = $beerTypeService->parseFile($file)) === false) {
//            return new JsonResponse([], Response::HTTP_BAD_REQUEST);
//        }
//        $data = $beerTypeService->importTypes($types);
//        return new JsonResponse($data, Response::HTTP_CREATED);
//    }
}

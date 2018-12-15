<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Beer\Type;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class BeerTypeController extends Controller
{
    /**
     * @Route("/admin/beer-type", name="admin_beer_type")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction()
    {
        return $this->render('admin/beer_type/list.html.twig');
    }

    /**
     * @Route("/admin/beer-type/refresh", name="admin_beer_type_refresh")
     * @Method("POST")
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function listRefreshAction(Request $request)
    {
        $data = $this->get('ontap.service.beer_type')->getList($request->request->all(), $this->getUser()->getLanguage());

        return new JsonResponse($data);
    }

    /**
     * @Route("/admin/beer-type/add", name="admin_beer_type_add")
     * @Route("/admin/beer-type/edit/{id}", name="admin_beer_type_edit")
     *
     * @param Request $request
     * @param Type    $type
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addEdit(Request $request, Type $type = null)
    {
        $isNew = false;
        if (null === $type) {
            $type = new Type();
            $isNew = true;
        }

        $form = $this->createForm($this->get('ontap.form.beer_type'), $type);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $translator = $this->get('translator');
            $flashbag = $this->get('session')->getFlashBag();
            if ($form->isValid()) {
                $this->get('ontap.service.beer_type')->saveType($type);
                $msg = $isNew ? $translator->trans('Type successfully added.') : $translator->trans('Type successfully edited.');
                $flashbag->add('success', $msg);

                return $this->redirectToRoute('admin_beer_type');
            } else {
                $flashbag->add('error', $translator->trans('Some fields are invalids.'));
            }
        }

        return $this->render('admin/beer_type/add_edit.html.twig', [
            'form' => $form->createView(),
            'isNew' => $isNew,
        ]);
    }

    /**
     * @Route("/admin/beer-type/import", name="admin_beer_type_import")
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function importAction(Request $request)
    {
        return $this->render('admin/beer_type/import.html.twig');
    }

    /**
     * @Route("/admin/beer-type/import/process", name="admin_beer_type_import_process")
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function importProcessAction(Request $request)
    {
        $beerTypeService = $this->get('ontap.service.beer_type');
        $file = $request->files->get('file');
        if (false === ($types = $beerTypeService->parseFile($file))) {
            return new JsonResponse([], Response::HTTP_BAD_REQUEST);
        }
        $data = $beerTypeService->importTypes($types);

        return new JsonResponse($data, Response::HTTP_CREATED);
    }
}

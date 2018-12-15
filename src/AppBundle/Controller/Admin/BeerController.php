<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Beer;
use AppBundle\Entity\Beer\Type;
use AppBundle\Entity\Brewery;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class BeerController extends Controller
{
    /**
     * @Route("/admin/beer", name="admin_beer")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction()
    {
        $types = $this->getDoctrine()->getRepository(Type::class)
            ->getTypesWithTranslation($this->getUser()->getLanguage());
        $typesData = [];
        /** @var Type $type */
        foreach ($types as $type) {
            $typesData[$type->getId()] = $type->getTranslations()->first()->getName();
        }

        return $this->render('admin/beer/list.html.twig', ['types' => $typesData]);
    }

    /**
     * @Route("/admin/beer/refresh", name="admin_beer_refresh")
     * @Method("POST")
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function listRefreshAction(Request $request)
    {
        $data = $this->get('ontap.service.beer')->getList($request->request->all(), $this->getUser()->getLanguage());

        return new JsonResponse($data);
    }

    /**
     * @Route("/admin/beer/add", name="admin_beer_add")
     * @Route("/admin/beer/edit/{id}", name="admin_beer_edit")
     *
     * @param Request $request
     * @param Beer    $beer
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addEdit(Request $request, Beer $beer = null)
    {
        $isNew = false;
        if (null === $beer) {
            $beer = new Beer();
            $isNew = true;
        }

        $beerForm = $this->createForm($this->get('ontap.form.beer'), $beer);
        $breweryForm = $this->createForm($this->get('ontap.form.brewery'), new Brewery());
        $beerTypeForm = $this->createForm($this->get('ontap.form.beer_type'), new Type());

        $beerForm->handleRequest($request);
        if ($beerForm->isSubmitted()) {
            $translator = $this->get('translator');
            $flashbag = $this->get('session')->getFlashBag();
            if ($beerForm->isValid()) {
                $this->get('ontap.service.beer')->saveBeer($beer);
                $msg = $isNew ? $translator->trans('Beer successfully added.') : $translator->trans('Beer successfully edited.');
                $flashbag->add('success', $msg);

                return $this->redirectToRoute('admin_beer');
            } else {
                $flashbag->add('error', $translator->trans('Some fields are invalids.'));
            }
        }

        return $this->render('admin/beer/add_edit.html.twig', [
            'form' => $beerForm->createView(),
            'breweryForm' => $breweryForm->createView(),
            'beerTypeForm' => $beerTypeForm->createView(),
            'isNew' => $isNew,
        ]);
    }
}

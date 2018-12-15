<?php

namespace AppBundle\Controller\Auth;

use AppBundle\Entity\Beer;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class BeerController extends Controller
{
    /**
     * @Route("/auth/beer/create", name="auth_beer_create")
     * @Method("POST")
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function createAction(Request $request)
    {
        $beerService = $this->get('ontap.service.beer');

        $beer = $beerService->getBeerByName($request->request->get('beer')['name']);
        if ($beer) {
            return new JsonResponse(['id' => $beer->getId()], Response::HTTP_OK);
        }

        $beer = new Beer();
        $form = $this->createForm($this->get('ontap.form.beer'), $beer);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $beerService->saveBeer($beer);

                return new JsonResponse([
                    'id' => $beer->getId(),
                    'name' => $beer->getName(),
                ], Response::HTTP_CREATED);
            } else {
                $errors = [];
                $formErrors = $form->getErrors(true);
                foreach ($formErrors as $formError) {
                    $errors[$formError->getOrigin()->getName()] = $formError->getMessage();
                }

                return new JsonResponse($errors, Response::HTTP_BAD_REQUEST);
            }
        }

        return new JsonResponse([], Response::HTTP_EXPECTATION_FAILED);
    }
}

<?php

namespace AppBundle\Controller\Auth;

use AppBundle\Entity\Brewery;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class BreweryController extends Controller
{
    /**
     * @Route("/auth/brewery/create", name="auth_brewery_create")
     * @Method("POST")
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function createAction(Request $request)
    {
        $breweryService = $this->get('ontap.service.brewery');

        $brewery = $breweryService->getBreweryByName($request->request->get('brewery')['name']);
        if ($brewery) {
            return new JsonResponse(['id' => $brewery->getId()], Response::HTTP_OK);
        }

        $brewery = new Brewery();
        $form = $this->createForm($this->get('ontap.form.brewery'), $brewery);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $breweryService->saveBrewery($brewery);

                return new JsonResponse([
                    'id' => $brewery->getId(),
                    'name' => $brewery->getName(),
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

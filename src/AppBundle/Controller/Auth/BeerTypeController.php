<?php

namespace AppBundle\Controller\Auth;

use AppBundle\Entity\Beer\Type;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class BeerTypeController extends Controller
{
    /**
     * @Route("/auth/beer-type/create", name="auth_beer_type_create")
     * @Method("POST")
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function createAction(Request $request)
    {
        $typeService = $this->get('ontap.service.beer_type');

        $type = $typeService->getTypeByName(
            $this->getUser()->getLanguage(),
            $request->request->get('beer_type')['translations']
        );
        if ($type) {
            return new JsonResponse(['id' => $type->getId()], Response::HTTP_OK);
        }

        $type = new Type();
        $form = $this->createForm($this->get('ontap.form.beer_type'), $type);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $typeService->saveType($type);

                return new JsonResponse([
                    'id' => $type->getId(),
                    'name' => $type->getTranslations()->first()->getName(),
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

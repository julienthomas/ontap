<?php

namespace AppBundle\Security;

use AppBundle\Entity\Role;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Router;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

class AuthenticationRedirect implements AuthenticationSuccessHandlerInterface
{
    /**
     * @var Router
     */
    private $router;

    /**
     * @var string
     */
    private $adminHomeRoute;

    /**
     * @var string
     */
    private $userHomeRoute;

    /**
     * @param Router $router
     * @param $adminHomeRoute
     * @param $userHomeRoute
     */
    public function __construct(Router $router, $adminHomeRoute, $userHomeRoute)
    {
        $this->router = $router;
        $this->adminHomeRoute = $adminHomeRoute;
        $this->userHomeRoute = $userHomeRoute;
    }

    /**
     * Redirect the authenticated user depending on his roles.
     *
     * @param Request        $request
     * @param TokenInterface $token
     *
     * @return Response
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        $roles = [];
        foreach ($token->getRoles() as $role) {
            $roles[] = $role->getRole();
        }

        if (in_array(Role::ADMIN, $roles)) {
            $route = $this->adminHomeRoute;
        } elseif (in_array(Role::USER, $roles)) {
            $route = $this->userHomeRoute;
        } else {
            return new Response(Response::HTTP_FORBIDDEN);
        }

        return new RedirectResponse($this->router->generate($route));
    }
}

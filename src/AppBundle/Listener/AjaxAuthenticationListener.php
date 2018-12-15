<?php

namespace AppBundle\Listener;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class AjaxAuthenticationListener
{
    /**
     * @param GetResponseForExceptionEvent $event
     */
    public function onCoreException(GetResponseForExceptionEvent $event)
    {
        $request = $event->getRequest();
        $exception = $event->getException();

        if ($request->isXmlHttpRequest() &&
            ($exception instanceof AuthenticationException || $exception instanceof AccessDeniedException)) {
            $event->setResponse(new Response('', Response::HTTP_FORBIDDEN));
        }
    }
}

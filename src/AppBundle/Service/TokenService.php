<?php

namespace AppBundle\Service;

use AppBundle\Entity\Admin;
use AppBundle\Entity\Token;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\TwigBundle\TwigEngine;
use Symfony\Component\Routing\Router;
use Symfony\Component\Translation\Translator;

class TokenService extends AbstractService
{
    /**
     * @var MailService
     */
    private $mailService;

    /**
     * @var TwigEngine
     */
    private $templating;

    /**
     * @var Router
     */
    private $router;

    /**
     * @var Translator
     */
    private $translator;

    /**
     * @var int
     */
    private $tokenTtl;

    /**
     * @param EntityManager $manager
     * @param $tokenParameters
     */
    public function __construct(
        EntityManager $manager,
        MailService $mailService,
        TwigEngine $templating,
        Router $router,
        Translator $translator,
        $tokenTtl
    ) {
        parent::__construct($manager);

        $this->mailService = $mailService;
        $this->templating = $templating;
        $this->router = $router;
        $this->translator = $translator;
        $this->tokenTtl = $tokenTtl;
    }

    /**
     * @param string $login
     *
     * @return bool
     */
    public function createAndSend($login)
    {
        $user = null;
        $admin = $this->manager->getRepository(Admin::class)->findOneByLogin($login);
        if (null === $admin) {
            $user = $this->manager->getRepository(User::class)->findOneByEmail($login);
            if (null === $user) {
                return false;
            }
        }

        $token = new Token();
        $now = new \DateTime('now', new \DateTimeZone('UTC'));
        $tokenPrefix = $admin ? $admin->getEmail() : $user->getEmail();
        $token
            ->setToken(hash('sha256', $tokenPrefix.uniqid(null, true)))
            ->setAdmin($admin)
            ->setUser($user)
            ->setCreatedDate($now)
            ->setTtl($this->tokenTtl);
        $this->persistAndFlush($token);

        $subject = "[{$this->translator->trans('ONTAP')}] {$this->translator->trans('Reset your password')}";
        $mailBody = $this->templating->render('mail/password_reset.txt.twig', [
            'route' => $this->router->generate('password_reset', ['token' => $token->getToken()], true),
        ]);

        $to = $admin ? $admin->getEmail() : $user->getEmail();
        $this->mailService->send($subject, $mailBody, $to);

        return true;
    }

    /**
     * @param string $token
     *
     * @return Token|null
     */
    public function findToken($token)
    {
        /** @var Token $token */
        $token = $this->manager->getRepository(Token::class)->findOneBy([
            'token' => $token,
            'enabled' => true,
        ]);
        if (null === $token) {
            return null;
        }

        $now = new \DateTime('now', new \DateTimeZone('UTC'));
        $tokenMaxDate = clone $token->getCreatedDate();
        $tokenMaxDate->modify(sprintf('+%d seconds', $token->getTtl()));
        if ($now > $tokenMaxDate) {
            return null;
        }

        return $token;
    }

    /**
     * @param Token $token
     */
    public function invalidateToken(Token $token)
    {
        $token->setEnabled(false);
        $this->persistAndFlush($token);
    }
}

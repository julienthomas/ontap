<?php

namespace AppBundle\Service;

use AppBundle\Entity\Role;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\TwigBundle\TwigEngine;
use Symfony\Component\Routing\Router;
use Symfony\Component\Translation\Translator;

class UserService extends AbstractService
{
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
     * @var TokenService
     */
    private $tokenService;

    /**
     * @var MailService
     */
    private $mailService;

    /**
     * @var PasswordService
     */
    private $passwordService;

    /**
     * @param EntityManager $manager
     * @param TwigEngine $templating
     * @param Router $router
     * @param Translator $translator
     * @param TokenService $tokenService
     * @param MailService $mailService
     * @Param PasswordService $passwordService
     */
    public function __construct(
        EntityManager $manager,
        TwigEngine $templating,
        Router $router,
        Translator $translator,
        TokenService $tokenService,
        MailService $mailService,
        PasswordService $passwordService
    ){
        parent::__construct($manager);

        $this->tokenService = $tokenService;
        $this->mailService = $mailService;
        $this->templating = $templating;
        $this->router = $router;
        $this->translator = $translator;
        $this->passwordService = $passwordService;
    }

    /**
     * @param string $email
     *
     * @return User
     */
    public function findOrCreateUser($email)
    {
        $user = $this->manager->getRepository(User::class)->findOneByEmail($email);

        if (!$user) {

            $user = new User();
            $salt = $this->passwordService->createSalt();
            $password = $this->passwordService->createPassword(
                $user,
                '',
                ''
            );

            $role = $this->manager->getRepository(Role::class)->findOneByCode(Role::USER);
            $user
                ->setEmail($email)
                ->setSalt($salt)
                ->setPassword($password)
                ->addRole($role)
            ;
            $this->persistAndFlush($user);


            $this->createAndSendToken($user);
        }

        return $user;
    }

    /**
     * @param User $user
     *
     * @throws \Exception
     * @throws \Twig\Error\Error
     */
    private function createAndSendToken(User $user)
    {
        $token = $this->tokenService->createUserToken($user);

        $subject = "[{$this->translator->trans('ONTAP')}] {$this->translator->trans('Create your password')}";
        $mailBody = $this->templating->render('mail/password_create.txt.twig', [
            'route' => $this->router->generate('password_create', ['token' => $token->getToken()], true),
        ]);

        $this->mailService->send($subject, $mailBody, $user->getEmail());
    }
}
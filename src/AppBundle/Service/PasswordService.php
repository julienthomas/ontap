<?php
/**
 * Created by PhpStorm.
 * User: Hellion
 * Date: 26/11/2017
 * Time: 13:28.
 */

namespace AppBundle\Service;

use AppBundle\Entity\Admin;
use AppBundle\Entity\Token;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Validator\Constraints\NotBlank;

class PasswordService extends AbstractService
{
    /**
     * @var FormFactory
     */
    private $formFactory;

    /**
     * @var EncoderFactory
     */
    private $encoder;

    /**
     * @var Translator
     */
    private $translator;

    /**
     * PasswordService constructor.
     *
     * @param EntityManager  $manager
     * @param FormFactory    $formFactory
     * @param EncoderFactory $encoder
     * @param Translator     $translator
     */
    public function __construct(
        EntityManager $manager,
        FormFactory $formFactory,
        EncoderFactory $encoder,
        Translator $translator
    ) {
        parent::__construct($manager);
        $this->formFactory = $formFactory;
        $this->encoder = $encoder;
        $this->translator = $translator;
    }

    public function getForgottenForm()
    {
        $form = $this->formFactory->createBuilder('form')
            ->add(
                'login',
                'text',
                [
                    'label' => 'Login',
                    'required' => true,
                    'constraints' => new NotBlank([
                        'message' => $this->translator->trans('The login must be filled.'),
                    ]),
                ]
            )
        ;

        return $form->getForm();
    }

    /**
     * @return \Symfony\Component\Form\FormInterface
     */
    public function getResetForm()
    {
        $form = $this->formFactory->createBuilder('form')
            ->add(
                'password',
                'repeated',
                [
                    'type' => 'password',
                    'invalid_message' => $this->translator->trans('The password fields must match.'),
                    'required' => true,
                    'first_options' => ['label' => 'Password'],
                    'second_options' => ['label' => 'Repeat Password'],
                    'constraints' => new NotBlank([
                        'message' => $this->translator->trans('The password must be filled.'),
                    ]),
                ]
            )
        ;

        return $form->getForm();
    }

    /**
     * @param Token  $token
     * @param string $plainPassword
     */
    public function setPasswordFromToken(Token $token, $plainPassword)
    {
        $admin = $token->getAdmin();
        $user = $token->getUser();
        $this->setPassword($plainPassword, $admin, $user);
    }

    /**
     * @param string     $plainPassword
     * @param Admin|null $admin
     * @param User|null  $user
     */
    public function setPassword($plainPassword, Admin $admin = null, User $user = null)
    {
        $currentUser = null !== $admin ? $admin : $user;

        $this->encoder->getEncoder($currentUser);
        $salt = hash('sha256', (new \DateTime('now'))->format('YmdHis'));
        $password = $this->encoder->getEncoder($currentUser)->encodePassword($plainPassword, $salt);
        $currentUser
            ->setPassword($password)
            ->setSalt($salt);
        $this->persistAndFlush($currentUser);
    }
}

<?php

namespace AppBundle\Form\Place;

use AppBundle\Service\PlaceService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PictureType extends AbstractType
{
    /**
     * @var PlaceService
     */
    private $placeService;

    /**
     * @var bool
     */
    private $inError;

    public function __construct(PlaceService $placeService)
    {
        $this->placeService = $placeService;
        $this->inError = false;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'file',
                'hidden',
                ['required' => false]
            )
            ->addEventListener(FormEvents::SUBMIT, [$this, 'verifFile']);
    }

    /**
     * @param FormEvent $event
     */
    public function verifFile(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();

        if (empty($data->getFile())) {
            return;
        }
        if (!$this->placeService->verifFile($data->getFile())) {
            $form->get('file')->addError(new FormError(_('Invalid picture file.')));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => 'AppBundle\Entity\Place\Picture']);
    }

    public function getName()
    {
        return 'place_picture';
    }
}

<?php

namespace AppBundle\Form\Place;

use AppBundle\Entity\Place\Schedule;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ScheduleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'day',
                'integer',
                ['required' => false]
            )->add(
                'openingTime',
                'time',
                [
                    'widget' => 'single_text',
                    'required' => false,
                ]
            )->add(
                'closureTime',
                'time',
                [
                    'widget' => 'single_text',
                    'required' => false,
                ]
            );
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => Schedule::class]);
    }

    public function getName()
    {
        return 'place_schedule';
    }
}

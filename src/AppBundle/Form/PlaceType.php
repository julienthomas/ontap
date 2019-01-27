<?php

namespace AppBundle\Form;

use AppBundle\Entity\Beer;
use AppBundle\Entity\Language;
use AppBundle\Form\Place\AddressType;
use AppBundle\Form\Place\PictureType;
use AppBundle\Form\Place\ScheduleType;
use AppBundle\Service\PlaceService;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlaceType extends AbstractType
{
    /**
     * @var Language
     */
    private $language;

    /**
     * @var PlaceService
     */
    private $placeService;

    /**
     * @var bool
     */
    private $addOwner;

    /**
     * @var bool
     */
    private $ownerEmail;

    /**
     * @param Language     $language
     * @param PlaceService $placeService
     */
    public function __construct(Language $language, PlaceService $placeService, $addOwer = false, $ownerEmail = null)
    {
        $this->language = $language;
        $this->placeService = $placeService;
        $this->addOwner = $addOwer;
        $this->ownerEmail = $ownerEmail;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'name',
                'text',
                ['label' => 'Name']
            )
            ->add(
                'email',
                'email',
                [
                    'label' => 'Email',
                    'required' => false,
                ]
            )
            ->add(
                'phone',
                'text',
                [
                    'label' => 'Phone number',
                    'required' => false,
                ]
            )
            ->add(
                'address',
                new AddressType($this->language)
            )
            ->add(
                'website',
                'url',
                [
                    'label' => 'Website',
                    'required' => false,
                ]
            )
            ->add(
                'facebook',
                'url',
                [
                    'label' => 'Facebook',
                    'required' => false,
                ]
            )
            ->add(
                'description',
                'textarea',
                [
                    'label' => 'Description',
                    'required' => false,
                ]
            )
            ->add(
                'beers',
                'entity',
                [
                    'class' => Beer::class,
                    'required' => false,
                    'attr' => ['title' => '- Choose one or more -'],
                    'multiple' => true,
                    'choice_label' => 'name',
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('beer')
                            ->addSelect('brewery')
                            ->innerJoin('beer.brewery', 'brewery')
                            ->addOrderBy('brewery.name')
                            ->orderBy('beer.name');
                    },
                    'group_by' => function ($val) {
                        return $val->getBrewery()->getName();
                    },
                    'choice_attr' => function ($val) {
                        return ['data-tokens' => $val->getBrewery()->getName()];
                    },
                ]
            )
            ->add(
                'schedules',
                'collection',
                [
                    'type' => new ScheduleType(),
                    'required' => false,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => true,
                ]
            )
            ->add(
                'pictures',
                'collection',
                [
                    'type' => new PictureType($this->placeService),
                    'allow_add' => true,
                    'allow_delete' => true,
                ]
            )
        ;

        if ($this->addOwner) {
            $builder->add(
                'owner',
                'email',
                [
                    'label' => 'Email',
                    'required' => false,
                    'mapped' => false,
                    'data' => $this->ownerEmail,
                ]
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\Place',
            'cascade_validation' => true,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'place';
    }
}

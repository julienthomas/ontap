<?php

namespace AppBundle\Form;

use AppBundle\Entity\Beer;
use AppBundle\Entity\Brewery;
use AppBundle\Entity\Language;
use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\Beer\Type;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class BeerType extends AbstractType
{
    /**
     * @var Language
     */
    private $language;

    /**
     * @param TokenStorage $tokenStorage
     */
    public function __construct(TokenStorage $tokenStorage)
    {
        $this->language = $tokenStorage->getToken()->getUser()->getLanguage();
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
                'type',
                'entity',
                [
                    'label' => 'Type',
                    'empty_value' => '- Choose one -',
                    'class' => Type::class,
                    'choice_label' => 'translations.first.name',
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('type')
                            ->addSelect('translations')
                            ->leftJoin('type.translations', 'translations')
                            ->leftJoin('translations.language', 'language')
                            ->where('language.locale = :locale')
                            ->setParameter('locale', $this->language->getLocale())
                            ->orderBy('translations.name', 'ASC');
                    },
                ]
            )
            ->add(
                'alcoholDegree',
                'number',
                [
                    'label' => 'Alcohol degree',
                    'scale' => 1,
                    'attr' => ['min' => 0, 'max' => 100],
                ]
            )
            ->add(
                'brewery',
                'entity',
                [
                    'label' => 'Brewery',
                    'empty_value' => '- Choose one -',
                    'class' => Brewery::class,
                    'choice_label' => 'name',
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('brewery')
                            ->orderBy('brewery.name', 'ASC');
                    },
                ]
            );
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('data_class', Beer::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'beer';
    }
}

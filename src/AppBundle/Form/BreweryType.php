<?php

namespace AppBundle\Form;

use AppBundle\Entity\Brewery;
use AppBundle\Util\FormUtil;
use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\Language;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class BreweryType extends AbstractType
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
                'country',
                'entity',
                [
                    'label' => 'Country',
                    'empty_value' => '- Choose one -',
                    'class' => 'AppBundle\Entity\Country',
                    'choice_label' => 'translations.first.name',
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('country')
                            ->addSelect('translations')
                            ->leftJoin('country.translations', 'translations')
                            ->leftJoin('translations.language', 'language')
                            ->where('language.locale = :locale')
                            ->setParameter('locale', $this->language->getLocale())
                            ->orderBy('translations.name', 'ASC');
                    },
                ]
            );
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            FormUtil::removeWhiteSpaces($event, 'name');
        });
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('data_class', Brewery::class);
    }

    public function getName()
    {
        return 'brewery';
    }
}

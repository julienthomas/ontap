<?php

namespace AppBundle\Form;

use AppBundle\Entity\Beer\Type;
use AppBundle\Transformer\BeerTypeTranslationTransformer;
use AppBundle\Util\FormUtil;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Validator\RecursiveValidator;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class BeerTypeType extends AbstractType
{
    /**
     * @var EntityManager
     */
    private $manager;

    /**
     * @var RecursiveValidator
     */
    private $validator;

    /**
     * @param EntityManager      $manager
     * @param ValidatorInterface $validator
     */
    public function __construct(EntityManager $manager, ValidatorInterface $validator)
    {
        $this->manager = $manager;
        $this->validator = $validator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'translations',
                'text',
                ['label' => 'Name']
            );
        $builder
            ->get('translations')
            ->addModelTransformer(new BeerTypeTranslationTransformer($builder->getData(), $this->manager));
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            FormUtil::removeWhiteSpaces($event, 'translations');
        });
        $builder->addEventListener(FormEvents::POST_SUBMIT, [$this, 'verifTranslations']);
    }

    /**
     * @param FormEvent $event
     */
    public function verifTranslations(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();
        foreach ($data->getTranslations() as $translation) {
            $errors = $this->validator->validate($translation);
            if (count($errors) > 0) {
                $form->get('translations')->addError(new FormError($errors[0]->getMessage()));
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('data_class', Type::class);
    }

    public function getName()
    {
        return 'beer_type';
    }
}

<?php

namespace AppBundle\Transformer;

use AppBundle\Entity\Language;
use Doctrine\ORM\EntityManager;
use AppBundle\Entity\Beer\Type;
use AppBundle\Entity\Beer\Type\Translation;
use Symfony\Component\Form\DataTransformerInterface;

class BeerTypeTranslationTransformer implements DataTransformerInterface
{
    /**
     * @var Type
     */
    private $type;

    /**
     * @var EntityManager
     */
    private $manager;

    /**
     * @param Type          $type
     * @param EntityManager $manager
     */
    public function __construct(Type $type, EntityManager $manager)
    {
        $this->type = $type;
        $this->manager = $manager;
    }

    /**
     * {@inheritdoc}
     */
    public function transform($value)
    {
        if (null === $this->type->getId()) {
            return null;
        }
        $translations = $this->manager->getRepository(Translation::class)->getTranslationsByType($this->type);
        /** @var Type\Translation $translation */
        foreach ($translations as $translation) {
            if ('fr_FR' === $translation->getLanguage()->getLocale()) {
                return $translation->getName();
            }
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($value)
    {
        if ($this->type->getId()) {
            $translations = $this->manager->getRepository(Translation::class)->getTranslationsByType($this->type);
            /** @var Type\Translation $translation */
            foreach ($translations as $translation) {
                if ('fr_FR' === $translation->getLanguage()->getLocale()) {
                    $translation->setName($value);

                    return [$translation];
                }
            }
        }
        $translation = new Translation();
        $language = $this->manager->getRepository(Language::class)->findOneByLocale('fr_FR');
        $translation
            ->setLanguage($language)
            ->setName($value);
        $translations[] = $translation;

        return [$translation];
    }
}

<?php

namespace AppBundle\Service;

use AppBundle\Entity\Language;
use AppBundle\Form\BeerTypeType;
use AppBundle\Util\DatatableUtil;
use Doctrine\ORM\EntityManager;
use AppBundle\Entity\Beer\Type;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class BeerTypeService extends AbstractService
{
    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var Translator
     */
    private $translator;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var BeerTypeType
     */
    private $beerTypeForm;

    const DATATABLE_KEY_ID = 'id';
    const DATATABLE_KEY_NAME = 'name';
    const DATATABLE_KEY_BEER_NUMBER = 'beer_number';

    /**
     * @param EntityManager        $manager
     * @param \Twig_Environment    $twig
     * @param Translator           $translator
     * @param FormFactoryInterface $formFactory
     * @param BeerTypeType         $beerTypeForm
     */
    public function __construct(
        EntityManager $manager,
        \Twig_Environment $twig,
        Translator $translator,
        FormFactoryInterface $formFactory,
        BeerTypeType $beerTypeForm
    ) {
        parent::__construct($manager);
        $this->twig = $twig;
        $this->translator = $translator;
        $this->formFactory = $formFactory;
        $this->beerTypeForm = $beerTypeForm;
    }

    /**
     * @param $requestData
     * @param Language $language
     *
     * @return array
     */
    public function getList($requestData, Language $language)
    {
        $listParams = $this->getListParams($requestData);

        $results = $this->manager->getRepository(Type::class)->getDatatableList(
            $listParams['searchs'],
            $listParams['order'],
            $listParams['limit'],
            $listParams['offset'],
            $language
        );

        $template = $this->twig->loadTemplate('admin/beer_type/datatable/items.html.twig');
        $data = [];
        foreach ($results['data'] as $type) {
            $data[] = [
                $type[self::DATATABLE_KEY_NAME],
                $type[self::DATATABLE_KEY_BEER_NUMBER],
                $template->renderBlock('btns', ['id' => $type[self::DATATABLE_KEY_ID]]),
            ];
        }

        return [
            'data' => $data,
            'recordsTotal' => $results['recordsTotal'],
            'recordsFiltered' => $results['recordsFiltered'],
        ];
    }

    /**
     * @param $requestData
     *
     * @return array
     */
    private function getListParams($requestData)
    {
        $orderColumns = [self::DATATABLE_KEY_NAME, self::DATATABLE_KEY_BEER_NUMBER];
        $searchColumns = [
            ['name' => self::DATATABLE_KEY_NAME, 'searchType' => DatatableUtil::SEARCH_LIKE],
            ['name' => self::DATATABLE_KEY_BEER_NUMBER, 'searchType' => DatatableUtil::SEARCH_EQUAL],
        ];

        return [
            'searchs' => DatatableUtil::getSearchs($requestData, $searchColumns),
            'order' => DatatableUtil::getOrder($requestData, $orderColumns),
            'limit' => DatatableUtil::getLimit($requestData),
            'offset' => DatatableUtil::getOffset($requestData),
        ];
    }

    /**
     * @param Type $type
     */
    public function saveType(Type $type)
    {
        $translations = $type->getTranslations();
        $type->clearTranslations();
        $this->persistAndFlush($type);
        /** @var Type\Translation $translation */
        foreach ($translations as $translation) {
            if (!$type->hasTranslation($translation)) {
                $translation->setType($type);
                $type->addTranslation($translation);
            }
        }
        $this->persistAndFlush($type->getTranslations()->toArray());
        $this->removeUnused($type);
    }

    /**
     * @param Type $type
     */
    private function removeUnused(Type $type)
    {
        $translations = $this->manager->getRepository('AppBundle:Beer\Type\Translation')->findByType($type);

        $remove = [];
        foreach ($translations as $translation) {
            if (!$type->hasTranslation($translation)) {
                $remove[] = $translation;
            }
        }
        $this->removeAndFlush($remove);
    }

    /**
     * @param UploadedFile $file
     *
     * @return array|bool
     */
    public function parseFile(UploadedFile $file)
    {
        if ('text/csv' !== $file->getClientMimeType()) {
            return false;
        }
        $csv = array_map('str_getcsv', file($file));
        foreach ($csv as $index => $line) {
            $csv[$index] = explode(';', $line[0])[0];
        }
        if ('name' !== $csv[0]) {
            return false;
        }
        array_splice($csv, 0, 1);

        return $csv;
    }

    /**
     * @param $types
     *
     * @return array
     */
    public function importTypes($types)
    {
        $data = [];
        foreach ($types as $index => $typeName) {
            $data[$index] = ['name' => $typeName];
            $type = new Type();
            $form = $this->formFactory->create($this->beerTypeForm, $type, ['csrf_protection' => false]);
            $form->submit(['translations' => $typeName]);
            if (!$form->isValid()) {
                $errors = $form->getErrors(true, true);
                $data[$index]['error'] = $this->translator->trans($errors->offsetGet(0)->getMessage());
            } else {
                $this->saveType($type);
            }
        }

        return $data;
    }

    /**
     * @param Language $language
     * @param $name
     *
     * @return Type|null
     */
    public function getTypeByName(Language $language, $name)
    {
        $name = preg_replace('/\s+/', ' ', $name);
        $translation = $this->manager->getRepository(Type\Translation::class)
            ->findOneBy(['name' => $name, 'language' => $language]);
        if ($translation) {
            return $translation->getType();
        }

        return null;
    }
}

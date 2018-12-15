<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Language;
use Doctrine\ORM\EntityRepository;

class CountryRepository extends EntityRepository
{
    /**
     * @param $ids
     *
     * @return array
     */
    public function findByIds($ids)
    {
        $qb = $this->_em->createQueryBuilder()
            ->select('country')
            ->from('AppBundle:Country', 'country')
            ->where('country.id IN :ids')
            ->setParameter('ids', $ids);

        $query = $qb->getQuery();
        $query->useQueryCache(true);

        return $query->getResult();
    }

    public function getCountriesWithTranslation(Language $language)
    {
        $qb = $this->_em->createQueryBuilder()
            ->select('country, translations')
            ->from('AppBundle:Country', 'country')
            ->innerJoin('country.translations', 'translations')
            ->where('translations.language = :language')
            ->orderBy('translations.name')
            ->setParameter('language', $language);

        $query = $qb->getQuery();
        $query->useQueryCache(true);

        return $query->getResult();
    }
}

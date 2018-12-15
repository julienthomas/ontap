<?php

namespace AppBundle\Repository\Beer\Type;

use AppBundle\Entity\Beer\Type;
use Doctrine\ORM\EntityRepository;

class TranslationRepository extends EntityRepository
{
    /**
     * @param Type $type
     *
     * @return array
     */
    public function getTranslationsByType(Type $type)
    {
        $qb = $this->_em->createQueryBuilder()
            ->select('translation, language')
            ->from('AppBundle:Beer\Type\Translation', 'translation')
            ->innerJoin('translation.language', 'language')
            ->where('translation.type = :type')
            ->setParameter('type', $type);

        $query = $qb->getQuery();
        $query->useQueryCache(true);

        return $query->getResult();
    }
}

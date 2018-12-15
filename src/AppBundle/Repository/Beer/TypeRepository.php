<?php

namespace AppBundle\Repository\Beer;

use AppBundle\Entity\Beer\Type;
use AppBundle\Entity\Language;
use AppBundle\Service\BeerTypeService;
use AppBundle\Util\DatatableUtil;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;

class TypeRepository extends EntityRepository
{
    /**
     * @param $searchs
     * @param $order
     * @param $limit
     * @param $offset
     * @param Language $language
     *
     * @return array
     */
    public function getDatatableList($searchs, $order, $limit, $offset, Language $language)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb
            ->select(sprintf(
                'type.id as %s, translations.name as %s, count(beers) as %s',
                BeerTypeService::DATATABLE_KEY_ID,
                BeerTypeService::DATATABLE_KEY_NAME,
                BeerTypeService::DATATABLE_KEY_BEER_NUMBER
            ))
            ->from('AppBundle:Beer\Type', 'type')
            ->leftJoin('type.translations', 'translations')
            ->leftJoin('AppBundle:Beer', 'beers', Join::WITH, 'beers.type = type')
            ->where('translations.language = :language')
            ->groupBy('type.id')
            ->setParameter('language', $language);

        if (null !== $order) {
            $qb->orderBy($order['col'], $order['dir']);
        }

        if (null !== $offset && null !== $limit) {
            $qb->setFirstResult($offset);
        }
        if (null !== $limit) {
            $qb->setMaxResults($limit);
        }
        if (null !== $searchs) {
            foreach ($searchs as $search) {
                $expr = $search['expr'];
                $paramKey = $search['param']['key'];
                $paramValue = $search['param']['value'];

                $qb
                    ->orHaving($expr)
                    ->setParameter($paramKey, $paramValue);
            }
        }

        return DatatableUtil::getQbData($this->_em, $qb, 'type.id', $searchs);
    }

    /**
     * @param Language $language
     *
     * @return array
     */
    public function getTypesWithTranslation(Language $language)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb
            ->select('type, translations')
            ->from('AppBundle:Beer\Type', 'type')
            ->innerJoin('type.translations', 'translations')
            ->where('translations.language = :language')
            ->orderBy('translations.name')
            ->setParameter('language', $language);

        $query = $qb->getQuery();
        $query->useQueryCache(true);

        return $query->getResult();
    }

    /**
     * @return mixed
     */
    public function getTypesCount()
    {
        $qb = $this->_em->createQueryBuilder()
            ->select('count(type)')
            ->from('AppBundle:Beer\Type', 'type');
        $query = $qb->getQuery();
        $query->useQueryCache(true);

        return $query->getSingleScalarResult();
    }

    /**
     * @param Language $language
     *
     * @return Type|mixed
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getNewestType(Language $language)
    {
        $qb = $this->_em->createQueryBuilder()
            ->select('type')
            ->from('AppBundle:Beer\Type', 'type')
            ->innerJoin('type.translations', 'translations')
            ->where('translations.language = :language')
            ->orderBy('type.id', 'DESC')
            ->setMaxResults(1)
            ->setParameter('language', $language);
        $query = $qb->getQuery();
        $query->useQueryCache(true);

        return $query->getOneOrNullResult();
    }
}

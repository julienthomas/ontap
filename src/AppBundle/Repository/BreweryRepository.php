<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Brewery;
use AppBundle\Entity\Language;
use AppBundle\Service\BreweryService;
use AppBundle\Util\DatatableUtil;
use Doctrine\ORM\EntityRepository;

class BreweryRepository extends EntityRepository
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
                'brewery.id as %s, brewery.name as %s, country.id as %s, translations.name as %s, count(beers) as %s',
                BreweryService::DATATABLE_KEY_ID,
                BreweryService::DATATABLE_KEY_NAME,
                BreweryService::DATATABLE_KEY_COUNTRY_ID,
                BreweryService::DATATABLE_KEY_COUNTRY_NAME,
                BreweryService::DATATABLE_KEY_BEER_NUMBER
            ))
            ->from('AppBundle:Brewery', 'brewery')
            ->innerJoin('brewery.country', 'country')
            ->innerJoin('country.translations', 'translations')
            ->leftJoin('brewery.beers', 'beers')
            ->where('translations.language = :language')
            ->groupBy('brewery.id')
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

        return DatatableUtil::getQbData($this->_em, $qb, 'brewery.id', $searchs);
    }

    /**
     * @return array
     */
    public function getBreweriesWithBeers()
    {
        $qb = $this->_em->createQueryBuilder()
            ->select('brewery, beers')
            ->from('AppBundle:Brewery', 'brewery')
            ->innerJoin('brewery.beers', 'beers')
            ->orderBy('brewery.name');

        $query = $qb->getQuery();
        $query->useQueryCache(true);

        return $query->getResult();
    }

    /**
     * @return mixed
     */
    public function getBreweriesCount()
    {
        $qb = $this->_em->createQueryBuilder()
            ->select('count(brewery)')
            ->from('AppBundle:Brewery', 'brewery');
        $query = $qb->getQuery();
        $query->useQueryCache(true);

        return $query->getSingleScalarResult();
    }

    /**
     * @return Brewery|mixed
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getNewestBrewery()
    {
        $qb = $this->_em->createQueryBuilder()
            ->select('brewery')
            ->from('AppBundle:Brewery', 'brewery')
            ->orderBy('brewery.id', 'DESC')
            ->setMaxResults(1);
        $query = $qb->getQuery();
        $query->useQueryCache(true);

        return $query->getOneOrNullResult();
    }
}

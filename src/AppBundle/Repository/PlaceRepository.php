<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Language;
use AppBundle\Entity\Place;
use AppBundle\Entity\Place\Type;
use AppBundle\Service\PlaceService;
use AppBundle\Util\DatatableUtil;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr;

class PlaceRepository extends EntityRepository
{
    /**
     * @param $searchs
     * @param $order
     * @param $limit
     * @param $offset
     *
     * @return array
     */
    public function getNewPlacesDatatableList($searchs, $order, $limit, $offset)
    {
        $qb = $this->getPlacesDatatableListQuery($searchs, $order, $limit, $offset);
        $qb
            ->leftJoin('AppBundle:UserPlace', 'userPlace', 'userPlace.place = place')
            ->andWhere('userPlace.place IS NULL')
        ;

        return DatatableUtil::getQbData($this->_em, $qb, 'place.id', $searchs);
    }

    /**
     * @param $searchs
     * @param $order
     * @param $limit
     * @param $offset
     *
     * @return array
     */
    public function getPlacesDatatableList($searchs, $order, $limit, $offset)
    {
        $qb = $this->getPlacesDatatableListQuery($searchs, $order, $limit, $offset);

        return DatatableUtil::getQbData($this->_em, $qb, 'place.id', $searchs);
    }

    /**
     * @param $searchs
     * @param $order
     * @param $limit
     * @param $offset
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function getPlacesDatatableListQuery($searchs, $order, $limit, $offset)
    {
        $qb = $this->_em->createQueryBuilder('place');
        $qb
            ->select(sprintf(
                'place.id as %s, place.name AS %s, place.email AS %s, placeAddress.address AS %s',
                PlaceService::DATATABLE_KEY_ID,
                PlaceService::DATATABLE_KEY_NAME,
                PlaceService::DATATABLE_KEY_EMAIL,
                PlaceService::DATATABLE_KEY_ADDRESS
            ))
            ->from('AppBundle:Place', 'place')
            ->innerJoin('place.address', 'placeAddress');

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

        return $qb;
    }

    /**
     * @param $beerId
     *
     * @return array
     */
    public function getHomeMapPlaces($beerId)
    {
        $qb = $this->_em->createQueryBuilder()
            ->select('place, address, beers')
            ->from('AppBundle:Place', 'place')
            ->innerJoin('place.address', 'address')
            ->leftJoin('place.beers', 'beers')
            ->leftJoin('beers.brewery', 'brewery')
            ->orderBy('beers.name');

        if ($beerId) {
            $qb
                ->where('beers.id = :beerId')
                ->setParameter('beerId', $beerId);
        }

        $query = $qb->getQuery();
        $query->useQueryCache(true);

        return $query->getResult();
    }

    /**
     * @param $placeId
     * @param \AppBundle\Entity\Language $language
     *
     * @return \AppBundle\Entity\Place|null
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getPlaceInformation($placeId, Language $language = null)
    {
        $qb = $this->_em->createQueryBuilder()
            ->select('place, address, country, country_translations, beers, brewery, pictures, schedules')
            ->from('AppBundle:Place', 'place')
            ->innerJoin('place.address', 'address')
            ->innerJoin('address.country', 'country')
            ->leftJoin('country.translations', 'country_translations', Expr\Join::WITH, 'country_translations.language = :language')
            ->leftJoin('place.beers', 'beers')
            ->leftJoin('beers.type', 'beer_type')
            ->leftJoin('beer_type.translations', 'beer_type_translations', Expr\Join::WITH, 'beer_type_translations.language = :language')
            ->leftJoin('beers.brewery', 'brewery')
            ->leftJoin('place.pictures', 'pictures')
            ->leftJoin('place.schedules', 'schedules')
            ->where('place.id = :id')
            ->orderBy('beers.name')
            ->setParameters([
                'id' => $placeId,
                'language' => $language,
            ]);

        $query = $qb->getQuery();
        $query->useQueryCache(true);

        return $query->getOneOrNullResult();
    }

    /**
     * @return Place|null
     */
    public function getNewestPlace()
    {
        $qb = $this->_em->createQueryBuilder()
            ->select('place')
            ->from('AppBundle:Place', 'place')
            ->orderBy('place.id', 'DESC')
            ->setMaxResults(1)
        ;
        $query = $qb->getQuery();
        $query->useQueryCache(true);

        return $query->getOneOrNullResult();
    }

    /**
     * @return int
     */
    public function getPlaceCount()
    {
        $qb = $this->_em->createQueryBuilder()
            ->select('count(place)')
            ->from('AppBundle:Place', 'place')
        ;
        $query = $qb->getQuery();
        $query->useQueryCache(true);

        return $query->getSingleScalarResult();
    }
}

<?php

namespace AppBundle\Service;

use AppBundle\Entity\Beer;
use AppBundle\Entity\Brewery;
use AppBundle\Entity\Place;
use Doctrine\ORM\EntityManager;
use AppBundle\Entity\Language;

class AdminDashboardService
{
    /**
     * @var EntityManager
     */
    private $manager;

    /**
     * @param EntityManager $manager
     */
    public function __construct(EntityManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param Language $language
     *
     * @return array
     */
    public function getDashboardStats(Language $language)
    {
        $placeRepository = $this->manager->getRepository(Place::class);
        $breweryRepository = $this->manager->getRepository(Brewery::class);
        $beerTypeRepository = $this->manager->getRepository(Beer\Type::class);
        $beerRepository = $this->manager->getRepository(Beer::class);

        $newestPlace = $placeRepository->getNewestPlace();
        $newestBrewery = $breweryRepository->getNewestBrewery();
        $newestBeer = $beerRepository->getNewestBeer();
        $newestBeerType = $beerTypeRepository->getNewestType($language);

        return [
            'places' => [
                'count' => $placeRepository->getPlaceCount(),
                'newest' => $newestPlace ? [
                    'name' => $newestPlace->getName(),
                    'date' => $this->getDate($newestPlace->getCreatedDate()),
                ] : null,
            ],
            'beers' => [
                'count' => $beerRepository->getBeersCount(),
                'newest' => $newestBeer ? [
                    'name' => $newestBeer->getName(),
                    'date' => $this->getDate($newestBeer->getCreatedDate()),
                ] : null,
            ],
            'beerTypes' => [
                'count' => $beerTypeRepository->getTypesCount(),
                'newest' => $newestBeerType ? [
                    'name' => $newestBeerType->getTranslations()->first()->getName(),
                    'date' => $this->getDate($newestBeerType->getCreatedDate()),
                ] : null,
            ],
            'breweries' => [
                'count' => $breweryRepository->getBreweriesCount(),
                'newest' => $newestBrewery ? [
                    'name' => $newestBrewery->getName(),
                    'date' => $this->getDate($newestBrewery->getCreatedDate()),
                ] : null,
            ],
        ];
    }

    /**
     * @param \DateTime $baseDate
     *
     * @return \DateTime
     */
    private function getDate(\DateTime $baseDate)
    {
        $date = new \DateTime($baseDate->format('Y/m/d H:i:s'), new \DateTimeZone('UTC'));

        return $date->setTimezone(new \DateTimeZone('Europe/Paris'));
    }
}

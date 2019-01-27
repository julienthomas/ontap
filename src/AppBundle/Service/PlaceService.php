<?php

namespace AppBundle\Service;

use AppBundle\Entity\Beer;
use AppBundle\Entity\Timezone;
use AppBundle\Entity\UserPlace;
use AppBundle\Util\DatatableUtil;
use Doctrine\ORM\EntityManager;
use AppBundle\Entity\Place;
use AppBundle\Entity\Place\Type;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Bundle\FrameworkBundle\Templating\Helper\AssetsHelper;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class PlaceService extends AbstractService
{
    const DATATABLE_KEY_ID = 'id';
    const DATATABLE_KEY_NAME = 'name';
    const DATATABLE_KEY_EMAIL = 'email';
    const DATATABLE_KEY_ADDRESS = 'address';

    /**
     * @var AssetsHelper
     */
    protected $assetsHelper;

    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var Router
     */
    private $router;

    /**
     * @var UserService
     */
    private $userService;

    /**
     * @var string
     */
    private $marker;

    /**
     * @param EntityManager     $manager
     * @param AssetsHelper      $assetsHelper
     * @param \Twig_Environment $twig
     * @param Router            $router
     * @param UserService       $userService
     * @param string            $marker
     */
    public function __construct(
        EntityManager $manager,
        AssetsHelper $assetsHelper,
        \Twig_Environment $twig,
        Router $router,
        UserService $userService,
        $marker
    ) {
        parent::__construct($manager);
        $this->assetsHelper = $assetsHelper;
        $this->twig = $twig;
        $this->router = $router;
        $this->userService = $userService;
        $this->marker = $marker;
    }

    /**
     * @param $beerId
     *
     * @return array
     */
    public function getHomeMapPlaces($beerId)
    {
        $places = $this->manager->getRepository('AppBundle:Place')->getHomeMapPlaces($beerId);
        $data = [];
        /** @var Place $place */
        foreach ($places as $place) {
            $address = $place->getAddress();
            $data[] = [
                'name' => $place->getName(),
                'address' => $address->getAddress(),
                'addressComplement' => $address->getAddressComplement(),
                'zipCode' => $address->getZipCode(),
                'city' => $address->getCity(),
                'latitude' => $address->getLatitude(),
                'longitude' => $address->getLongitude(),
                'website' => $place->getWebsite(),
                'facebook' => $place->getFacebook(),
                'marker' => $this->assetsHelper->getUrl($this->marker),
                'phone' => $place->getPhone(),
                'email' => $place->getEmail(),
                'description' => $place->getDescription(),
                'route' => $this->router->generate('place_information', ['id' => $place->getId()]),
                'beers' => $this->buildBeersArray($place->getBeers()),
            ];
        }

        return $data;
    }

    public function getNewList($requestData)
    {
        $listParams = $this->getListParams($requestData);

        $results = $this->manager->getRepository('AppBundle:Place')->getNewPlacesDatatableList(
            $listParams['searchs'],
            $listParams['order'],
            $listParams['limit'],
            $listParams['offset']
        );

        return $this->buildDatatableData($results);
    }

    public function getList($requestData)
    {
        $listParams = $this->getListParams($requestData);

        $results = $this->manager->getRepository('AppBundle:Place')->getPlacesDatatableList(
            $listParams['searchs'],
            $listParams['order'],
            $listParams['limit'],
            $listParams['offset']
        );

        return $this->buildDatatableData($results);
    }

    /**
     * @param $results
     *
     * @return array
     */
    private function buildDatatableData($results)
    {
        $template = $this->twig->load('admin/place/datatable/items.html.twig');
        $data = [];
        foreach ($results['data'] as $place) {
            $data[] = [
                $place[self::DATATABLE_KEY_NAME],
                $place[self::DATATABLE_KEY_EMAIL],
                $place[self::DATATABLE_KEY_ADDRESS],
                $template->renderBlock('btns', ['id' => $place[self::DATATABLE_KEY_ID]]),
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
        $orderColumns = [self::DATATABLE_KEY_NAME, self::DATATABLE_KEY_EMAIL, self::DATATABLE_KEY_ADDRESS];
        $searchColumns = [
            ['name' => self::DATATABLE_KEY_NAME, 'searchType' => DatatableUtil::SEARCH_LIKE],
            ['name' => self::DATATABLE_KEY_EMAIL, 'searchType' => DatatableUtil::SEARCH_LIKE],
            ['name' => self::DATATABLE_KEY_ADDRESS, 'searchType' => DatatableUtil::SEARCH_LIKE],
        ];

        return [
            'searchs' => DatatableUtil::getSearchs($requestData, $searchColumns),
            'order' => DatatableUtil::getOrder($requestData, $orderColumns),
            'limit' => DatatableUtil::getLimit($requestData),
            'offset' => DatatableUtil::getOffset($requestData),
        ];
    }

    /**
     * @param UploadedFile $file
     *
     * @return null|string
     */
    public function uploadImage(UploadedFile $file)
    {
        $assetPath = 'assets/img/place/picture';
        $serverPath = "{$_SERVER['DOCUMENT_ROOT']}/web/{$assetPath}";
        $mimeType = $file->getClientMimeType();

        if (!in_array($mimeType, ['image/jpeg', 'image/png'])) {
            return null;
        }
        $fileName = str_replace('.', null, uniqid('', true)).".{$file->getClientOriginalExtension()}";
        if (!file_exists($serverPath) || !is_dir($serverPath)) {
            try {
                mkdir($serverPath, 0755, true);
            } catch (\Exception $e) {
                throw new \Exception("PlaceService uploadPicture mkdir error: {$e->getMessage()}");
            }
        }
        $file->move($serverPath, $fileName);

        return "{$assetPath}/{$fileName}";
    }

    /**
     * @param $path
     *
     * @return bool
     */
    public function verifFile($path)
    {
        $serverPath = "{$_SERVER['DOCUMENT_ROOT']}/web/{$path}";
        if (!file_exists($serverPath)) {
            return true;
        }
        $mimeType = mime_content_type($serverPath);

        return in_array($mimeType, ['image/jpeg', 'image/png']);
    }

    /**
     * @param Place $place
     */
    public function savePlace(Place $place)
    {
        $address = $place->getAddress();
        $pictures = $place->getPictures();
        $schedules = $place->getSchedules();

        $place
            ->setAddress(null)
            ->clearPictures()
            ->clearSchedules();

        $this->persistAndFlush($address);
        $place->setAddress($address);
        $this->persistAndFlush($place);
        $newPictures = [];
        /** @var Place\Picture $picture */
        foreach ($pictures as $picture) {
            $serverPath = "{$_SERVER['DOCUMENT_ROOT']}/web/{$picture->getFile()}";
            if ($picture->getFile() && file_exists($serverPath) && !$place->hasPicture($picture)) {
                if (count($newPictures) < 3) {
                    $picture->setPlace($place);
                    $place->addPicture($picture);
                } else {
                    $this->deleteFile($picture->getFile());
                }
            }
        }
        /** @var Place\Schedule $schedule */
        foreach ($schedules as $schedule) {
            if (!$place->hasSchedule($schedule)) {
                $schedule->setPlace($place);
                $place->addSchedule($schedule);
            }
        }
        $this->persistAndFlush($place->getPictures()->toArray());
        $this->persistAndFlush($place->getSchedules()->toArray());
        $this->removeUnused($place);
    }

    /**
     * @param Place  $place
     * @param string $email
     */
    public function setPlaceOwner(Place $place, $email)
    {
        $owner = $place->getOwner();

        if ($owner && $owner->getEmail() === $email) {
            return;
        }

        if ($email) {
            $user = $this->userService->findOrCreateUser($email);
            $userPlace = new UserPlace($user, $place);
            $userPlace->setIsOwner(true);
            $this->persistAndFlush($userPlace);

//            $oldUserPlace = $this->manager->getRepository(UserPlace::class)->findOneBy([
//                'user' => $owner,
//                'place' => $place,
//            ]);
//            if ($oldUserPlace) {
//                $this->removeAndFlush($oldUserPlace);
//            }
        }
    }

    /**
     * @param Place $place
     */
    private function removeUnused(Place $place)
    {
        $pictures = $this->manager->getRepository(Place\Picture::class)->findByPlace($place);
        $schedules = $this->manager->getRepository(Place\Schedule::class)->findByPlace($place);

        $remove = [];
        /** @var Place\Picture $picture */
        foreach ($pictures as $picture) {
            if (!$place->hasPicture($picture)) {
                $remove[] = $picture;
                if ($picture->getFile()) {
                    $this->deleteFile($picture->getFile());
                }
            }
        }

        /** @var Place\Schedule $schedule */
        foreach ($schedules as $schedule) {
            if (!$place->hasSchedule($schedule)) {
                $remove[] = $schedule;
            }
        }

        $this->removeAndFlush($remove);
    }

    /**
     * @param Place $place
     *
     * @return array
     */
    public function getCurrentPictures(Place $place)
    {
        $data = [];
        /** @var Place\Picture $picture */
        foreach ($place->getPictures() as $picture) {
            if ($picture->getFile() && file_exists($picture->getFile())) {
                $data[] = $picture->getFile();
            }
        }

        return $data;
    }

    /**
     * @param Place $place
     * @param $basePictures
     */
    public function deleteUnusedPictures(Place $place, $basePictures)
    {
        $currentPictures = $this->getCurrentPictures($place);

        foreach ($basePictures as $basePicture) {
            if (!in_array($basePicture, $currentPictures) && file_exists($basePicture)) {
                $this->deleteFile($basePicture);
            }
        }
    }

    /**
     * @param $path
     */
    private function deleteFile($path)
    {
        $serverPath = "{$_SERVER['DOCUMENT_ROOT']}/web/{$path}";
        if (file_exists($serverPath)) {
            unlink($serverPath);
        }
    }

    /**
     * @param $schedules
     *
     * @return array
     */
    public function buildScheduleArray($schedules)
    {
        $data = [];
        /** @var Place\Schedule $schedule */
        foreach ($schedules as $schedule) {
            $data[$schedule->getDay()][] = [
                'opening' => $schedule->getOpeningTime(),
                'closure' => $schedule->getClosureTime(),
            ];
        }

        return $data;
    }

    /**
     * @return Place
     */
    public function initPlace()
    {
        $place = new Place();
        $timezone = $this->manager->getRepository(Timezone::class)->findOneByName('Europe/paris');
        $place
            ->setTimezone($timezone)
            ->setEnabled(true)
        ;

        return $place;
    }

    /**
     * @param $beers
     *
     * @return array
     */
    public function buildBeersArray($beers)
    {
        $data = [];
        /** @var Beer $beer */
        foreach ($beers as $beer) {
            $data[] = [
                'name' => $beer->getName(),
                'brewery' => $beer->getBrewery()->getName(),
            ];
        }

        return $data;
    }
}

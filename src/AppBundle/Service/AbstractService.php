<?php

namespace AppBundle\Service;

use Doctrine\ORM\EntityManager;

abstract class AbstractService
{
    /**
     * @var EntityManager
     */
    protected $manager;

    /**
     * @param EntityManager $manager
     */
    public function __construct(EntityManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param $data
     */
    protected function persistAndFlush($data)
    {
        if (is_array($data)) {
            foreach ($data as $entity) {
                $this->manager->persist($entity);
            }
        } else {
            $this->manager->persist($data);
        }
        $this->manager->flush($data);
    }

    /**
     * @param $data
     */
    protected function removeAndFlush($data)
    {
        if (is_array($data)) {
            foreach ($data as $entity) {
                $this->manager->remove($entity);
            }
        } else {
            $this->manager->remove($data);
        }
        $this->manager->flush($data);
    }
}

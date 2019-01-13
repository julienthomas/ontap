<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserPlace
 *
 * @ORM\Table(name="user_place", uniqueConstraints={@ORM\UniqueConstraint(name="user_id_place_id", columns={"user_id", "place_id"})})
 * @ORM\Entity
 */
class UserPlace
{
    /**
     * @var boolean
     *
     * @ORM\Column(name="is_owner", type="boolean", nullable=true)
     */
    private $isOwner = '0';

    /**
     * @var User
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="User", inversedBy="userPlaces")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    private $user;

    /**
     * @var Place
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Place", inversedBy="userPlaces")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="place_id", referencedColumnName="id")
     * })
     */
    private $place;

    /**
     * @param User $user
     * @param Place $place
     */
    function __construct(User $user, Place $place)
    {
        $this->user = $user;
        $this->place = $place;
    }

    /**
     * @return boolean
     */
    public function isOwner()
    {
        return $this->isOwner;
    }

    /**
     * @param boolean $isOwner
     *
     * @return self
     */
    public function setIsOwner($isOwner)
    {
        $this->isOwner = $isOwner;

        return $this;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     *
     * @return self
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Place
     */
    public function getPlace()
    {
        return $this->place;
    }

    /**
     * @param Place $place
     *
     * @return self
     */
    public function setPlace(Place $place)
    {
        $this->place = $place;

        return $this;
    }
}


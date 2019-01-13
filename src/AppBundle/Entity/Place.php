<?php

namespace AppBundle\Entity;

use AppBundle\Util\EntityUtil;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints;

/**
 * Place.
 *
 * @ORM\Table(name="place", uniqueConstraints={@ORM\UniqueConstraint(name="place_address_id", columns={"place_address_id"})}, indexes={@ORM\Index(name="timezone_id", columns={"timezone_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PlaceRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Place
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", length=10, options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=64, nullable=false)
     * @Constraints\NotBlank(
     *  message="The name must be filled."
     * )
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=128, nullable=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=32, nullable=true)
     */
    private $phone;

    /**
     * @var string
     *
     * @ORM\Column(name="website", type="text", length=65535, nullable=true)
     * @Constraints\Url(
     *  message="The website url is invalid."
     * )
     */
    private $website;

    /**
     * @var string
     *
     * @ORM\Column(name="facebook", type="text", length=65535, nullable=true)
     * @Constraints\Url(
     *  message="The website url is invalid."
     * )
     */
    private $facebook;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", length=65535, nullable=true)
     */
    private $description;

    /**
     * @var bool
     *
     * @ORM\Column(name="enabled", type="boolean", nullable=false)
     */
    private $enabled;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_date", type="datetime", nullable=false)
     */
    private $createdDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_date", type="datetime", nullable=true)
     */
    private $updatedDate;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Place\Picture", mappedBy="place")
     */
    private $pictures;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Place\Schedule", mappedBy="place")
     */
    private $schedules;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Place\Event", mappedBy="place")
     */
    private $events;

    /**
     * @var \AppBundle\Entity\Place\Address
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Place\Address")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="place_address_id", referencedColumnName="id", nullable=false)
     * })
     */
    private $address;

    /**
     * @var \AppBundle\Entity\Timezone
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Timezone")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="timezone_id", referencedColumnName="id", nullable=false)
     * })
     */
    private $timezone;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Beer")
     * @ORM\JoinTable(name="place_beer",
     *   joinColumns={
     *     @ORM\JoinColumn(name="place_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="beer_id", referencedColumnName="id")
     *   }
     * )
     */
    private $beers;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\UserPlace", mappedBy="place")
     */
    private $userPlaces;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->createdDate = new \DateTime('now', new \DateTimeZone(EntityUtil::DEFAULT_TIMEZONE));
        $this->pictures = new \Doctrine\Common\Collections\ArrayCollection();
        $this->schedules = new \Doctrine\Common\Collections\ArrayCollection();
        $this->events = new \Doctrine\Common\Collections\ArrayCollection();
        $this->beers = new \Doctrine\Common\Collections\ArrayCollection();
        $this->userPlaces = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return Place
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set email.
     *
     * @param string $email
     *
     * @return Place
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email.
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set phone.
     *
     * @param string $phone
     *
     * @return Place
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone.
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set website.
     *
     * @param string $website
     *
     * @return Place
     */
    public function setWebsite($website)
    {
        $this->website = $website;

        return $this;
    }

    /**
     * Get website.
     *
     * @return string
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * Set facebook.
     *
     * @param string $facebook
     *
     * @return Place
     */
    public function setFacebook($facebook)
    {
        $this->facebook = $facebook;

        return $this;
    }

    /**
     * Get webSite.
     *
     * @return string
     */
    public function getFacebook()
    {
        return $this->facebook;
    }

    /**
     * Set description.
     *
     * @param string $description
     *
     * @return Place
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set enabled.
     *
     * @param bool $enabled
     *
     * @return Place
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * Get enabled.
     *
     * @return bool
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Set createdDate.
     *
     * @param \DateTime $createdDate
     *
     * @return Place
     */
    public function setCreatedDate($createdDate)
    {
        $this->createdDate = $createdDate;

        return $this;
    }

    /**
     * Get createdDate.
     *
     * @return \DateTime
     */
    public function getCreatedDate()
    {
        return $this->createdDate;
    }

    /**
     * Set updatedDate.
     *
     * @param \DateTime $updatedDate
     *
     * @return Place
     */
    public function setUpdatedDate($updatedDate)
    {
        $this->updatedDate = $updatedDate;

        return $this;
    }

    /**
     * Get updatedDate.
     *
     * @return \DateTime
     */
    public function getUpdatedDate()
    {
        return $this->updatedDate;
    }

    /**
     * Add picture.
     *
     * @param \AppBundle\Entity\Place\Picture $picture
     *
     * @return Place
     */
    public function addPicture(\AppBundle\Entity\Place\Picture $picture)
    {
        $this->pictures[] = $picture;

        return $this;
    }

    /**
     * Remove picture.
     *
     * @param \AppBundle\Entity\Place\Picture $picture
     */
    public function removePicture(\AppBundle\Entity\Place\Picture $picture)
    {
        $this->pictures->removeElement($picture);
    }

    /**
     * Get pictures.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPictures()
    {
        return $this->pictures;
    }

    /**
     * Clear pictures.
     *
     * @return Place
     */
    public function clearPictures()
    {
        $this->pictures = new \Doctrine\Common\Collections\ArrayCollection();

        return $this;
    }

    /**
     * @param \AppBundle\Entity\Place\Picture $picture
     *
     * @return bool
     */
    public function hasPicture(\AppBundle\Entity\Place\Picture $picture)
    {
        return $this->pictures->contains($picture);
    }

    /**
     * Add schedule.
     *
     * @param \AppBundle\Entity\Place\Schedule $schedule
     *
     * @return Place
     */
    public function addSchedule(\AppBundle\Entity\Place\Schedule $schedule)
    {
        $this->schedules[] = $schedule;

        return $this;
    }

    /**
     * Remove schedule.
     *
     * @param \AppBundle\Entity\Place\Schedule $schedule
     */
    public function removeSchedule(\AppBundle\Entity\Place\Schedule $schedule)
    {
        $this->schedules->removeElement($schedule);
    }

    /**
     * Get schedules.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSchedules()
    {
        return $this->schedules;
    }

    /**
     * Clear schedules.
     *
     * @return Place
     */
    public function clearSchedules()
    {
        $this->schedules = new \Doctrine\Common\Collections\ArrayCollection();

        return $this;
    }

    /**
     * @param \AppBundle\Entity\Place\Schedule $schedule
     *
     * @return bool
     */
    public function hasSchedule(\AppBundle\Entity\Place\Schedule $schedule)
    {
        return $this->schedules->contains($schedule);
    }

    /**
     * Add event.
     *
     * @param \AppBundle\Entity\Place\Event $event
     *
     * @return Place
     */
    public function addEvent(\AppBundle\Entity\Place\Event $event)
    {
        $this->events[] = $event;

        return $this;
    }

    /**
     * Remove event.
     *
     * @param \AppBundle\Entity\Place\Event $event
     */
    public function removeEvent(\AppBundle\Entity\Place\Event $event)
    {
        $this->events->removeElement($event);
    }

    /**
     * Get events.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEvents()
    {
        return $this->events;
    }

    /**
     * Set address.
     *
     * @param \AppBundle\Entity\Place\Address $address | null
     *
     * @return Place
     */
    public function setAddress(\AppBundle\Entity\Place\Address $address = null)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address.
     *
     * @return \AppBundle\Entity\Place\Address
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set timezone.
     *
     * @param \AppBundle\Entity\Timezone $timezone
     *
     * @return Place
     */
    public function setTimezone(\AppBundle\Entity\Timezone $timezone)
    {
        $this->timezone = $timezone;

        return $this;
    }

    /**
     * Get timezone.
     *
     * @return \AppBundle\Entity\Timezone
     */
    public function getTimezone()
    {
        return $this->timezone;
    }

    /**
     * Add beer.
     *
     * @param \AppBundle\Entity\Beer $beer
     *
     * @return Place
     */
    public function addBeer(\AppBundle\Entity\Beer $beer)
    {
        $this->beers[] = $beer;

        return $this;
    }

    /**
     * Remove beer.
     *
     * @param \AppBundle\Entity\Beer $beer
     */
    public function removeBeer(\AppBundle\Entity\Beer $beer)
    {
        $this->beers->removeElement($beer);
    }

    /**
     * Get beers.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBeers()
    {
        return $this->beers;
    }

    /**
     * Get owner.
     *
     * @return \AppBundle\Entity\User|null
     */
    public function getOwner()
    {
        /** @var UserPlace $userPlace */
        foreach ($this->userPlaces as $userPlace) {
            if ($userPlace->isOwner()) {
                return $userPlace->getUser();
            }
        }

        return null;
    }

    /**
     * Set user.
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return Place
     */
    public function setUser(\AppBundle\Entity\User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @ORM\PreUpdate()
     */
    public function onPreUpdate()
    {
        $this->updatedDate = new \DateTime('now', new \DateTimeZone(EntityUtil::DEFAULT_TIMEZONE));
    }
}

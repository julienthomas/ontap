<?php

namespace AppBundle\Entity;

use AppBundle\Util\EntityUtil;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Beer.
 *
 * @ORM\Table(name="beer", uniqueConstraints={@ORM\UniqueConstraint(name="name", columns={"name"})}, indexes={@ORM\Index(name="beer_type_id", columns={"beer_type_id"}), @ORM\Index(name="brewery_id", columns={"brewery_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\BeerRepository")
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity(
 *  fields={"name"},
 *  message="This beer already exists."
 * )
 */
class Beer
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
     * @ORM\Column(name="alcohol_degree", type="decimal", precision=4, scale=1, nullable=false)
     * @Constraints\NotBlank(
     *  message="The alcohol degree must be filled."
     * )
     */
    private $alcoholDegree;

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
     * @var \AppBundle\Entity\Beer\Type
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Beer\Type")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="beer_type_id", referencedColumnName="id", nullable=false)
     * })
     * @Constraints\NotBlank(
     *  message="The type must be filled."
     * )
     */
    private $type;

    /**
     * @var \AppBundle\Entity\Brewery
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Brewery", inversedBy="beers")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="brewery_id", referencedColumnName="id", nullable=false)
     * })
     * @Constraints\NotBlank(
     *  message="The brewery must be filled."
     * )
     */
    private $brewery;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->createdDate = new \DateTime('now', new \DateTimeZone(EntityUtil::DEFAULT_TIMEZONE));
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
     * @return Beer
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
     * Set alcoholDegree.
     *
     * @param string $alcoholDegree
     *
     * @return Beer
     */
    public function setAlcoholDegree($alcoholDegree)
    {
        $this->alcoholDegree = $alcoholDegree;

        return $this;
    }

    /**
     * Get alcoholDegree.
     *
     * @return string
     */
    public function getAlcoholDegree()
    {
        return $this->alcoholDegree;
    }

    /**
     * Set createdDate.
     *
     * @param \DateTime $createdDate
     *
     * @return Beer
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
     * @return Beer
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
     * Set type.
     *
     * @param \AppBundle\Entity\Beer\Type $type
     *
     * @return Beer
     */
    public function setType(\AppBundle\Entity\Beer\Type $type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type.
     *
     * @return \AppBundle\Entity\Beer\Type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set brewery.
     *
     * @param \AppBundle\Entity\Brewery $brewery
     *
     * @return Beer
     */
    public function setBrewery(\AppBundle\Entity\Brewery $brewery)
    {
        $this->brewery = $brewery;

        return $this;
    }

    /**
     * Get brewery.
     *
     * @return \AppBundle\Entity\Brewery
     */
    public function getBrewery()
    {
        return $this->brewery;
    }

    /**
     * @ORM\PreUpdate()
     */
    public function onPreUpdate()
    {
        $this->updatedDate = new \DateTime('now', new \DateTimeZone(EntityUtil::DEFAULT_TIMEZONE));
    }
}

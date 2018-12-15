<?php

namespace AppBundle\Entity\Place\Event;

use Doctrine\ORM\Mapping as ORM;

/**
 * Type.
 *
 * @ORM\Table(name="place_event_type")
 * @ORM\Entity
 */
class Type
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
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Place\Event\Type\Translation", mappedBy="type")
     */
    private $translations;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->translations = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Add translation.
     *
     * @param \AppBundle\Entity\Place\Event\Type\Translation $translation
     *
     * @return Type
     */
    public function addTranslation(\AppBundle\Entity\Place\Event\Type\Translation $translation)
    {
        $this->translations[] = $translation;

        return $this;
    }

    /**
     * Remove translation.
     *
     * @param \AppBundle\Entity\Place\Event\Type\Translation $translation
     */
    public function removeTranslation(\AppBundle\Entity\Place\Event\Type\Translation $translation)
    {
        $this->translations->removeElement($translation);
    }

    /**
     * Get translations.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTranslations()
    {
        return $this->translations;
    }
}

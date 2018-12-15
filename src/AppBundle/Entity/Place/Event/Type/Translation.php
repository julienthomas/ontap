<?php

namespace AppBundle\Entity\Place\Event\Type;

use Doctrine\ORM\Mapping as ORM;

/**
 * Translation.
 *
 * @ORM\Table(name="place_event_type_translation")
 * @ORM\Entity
 */
class Translation
{
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=64, nullable=false)
     */
    private $name;

    /**
     * @var \AppBundle\Entity\Place\Event\Type
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Place\Event\Type", inversedBy="translations")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="event_type_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * })
     */
    private $type;

    /**
     * @var \AppBundle\Entity\Language
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Language")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="language_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * })
     */
    private $language;

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return Translation
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
     * Set type.
     *
     * @param \AppBundle\Entity\Place\Event\Type $type
     *
     * @return Translation
     */
    public function setType(\AppBundle\Entity\Place\Event\Type $type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type.
     *
     * @return \AppBundle\Entity\Place\Event\Type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set language.
     *
     * @param \AppBundle\Entity\Language $language
     *
     * @return Translation
     */
    public function setLanguage(\AppBundle\Entity\Language $language)
    {
        $this->language = $language;

        return $this;
    }

    /**
     * Get language.
     *
     * @return \AppBundle\Entity\Language
     */
    public function getLanguage()
    {
        return $this->language;
    }
}

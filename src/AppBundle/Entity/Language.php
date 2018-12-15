<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Language.
 *
 * @ORM\Table(name="language")
 * @ORM\Entity
 */
class Language
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
     * @ORM\Column(name="locale", type="string", length=5, nullable=false)
     */
    private $locale;

    /**
     * @var string
     *
     * @ORM\Column(name="date_format", type="string", length=32, nullable=false)
     */
    private $dateFormat;

    /**
     * @var string
     *
     * @ORM\Column(name="datetime_format", type="string", length=32, nullable=false)
     */
    private $datetimeFormat;

    /**
     * @var bool
     *
     * @ORM\Column(name="rtl", type="boolean", nullable=false, options={"default":0})
     */
    private $rtl = '0';

    /**
     * @var bool
     *
     * @ORM\Column(name="enabled", type="boolean", nullable=false, options={"default":0})
     */
    private $enabled = '0';

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Language\Translation", mappedBy="baseLanguage")
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
     * Set locale.
     *
     * @param string $locale
     *
     * @return Language
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * Get locale.
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * Set dateFormat.
     *
     * @param string $dateFormat
     *
     * @return Language
     */
    public function setDateFormat($dateFormat)
    {
        $this->dateFormat = $dateFormat;

        return $this;
    }

    /**
     * Get dateFormat.
     *
     * @return string
     */
    public function getDateFormat()
    {
        return $this->dateFormat;
    }

    /**
     * Set datetimeFormat.
     *
     * @param string $datetimeFormat
     *
     * @return Language
     */
    public function setDatetimeFormat($datetimeFormat)
    {
        $this->datetimeFormat = $datetimeFormat;

        return $this;
    }

    /**
     * Get datetimeFormat.
     *
     * @return string
     */
    public function getDatetimeFormat()
    {
        return $this->datetimeFormat;
    }

    /**
     * Set rtl.
     *
     * @param bool $rtl
     *
     * @return Language
     */
    public function setRtl($rtl)
    {
        $this->rtl = $rtl;

        return $this;
    }

    /**
     * Get rtl.
     *
     * @return bool
     */
    public function getRtl()
    {
        return $this->rtl;
    }

    /**
     * Set enabled.
     *
     * @param bool $enabled
     *
     * @return Language
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
     * Add translation.
     *
     * @param \AppBundle\Entity\Language\Translation $translation
     *
     * @return Language
     */
    public function addTranslation(\AppBundle\Entity\Language\Translation $translation)
    {
        $this->translations[] = $translation;

        return $this;
    }

    /**
     * Remove translation.
     *
     * @param \AppBundle\Entity\Language\Translation $translation
     */
    public function removeTranslation(\AppBundle\Entity\Language\Translation $translation)
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

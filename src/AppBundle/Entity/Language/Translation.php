<?php

namespace AppBundle\Entity\Language;

use Doctrine\ORM\Mapping as ORM;

/**
 * Translation.
 *
 * @ORM\Table(name="language_translation")
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
     * @var \AppBundle\Entity\Language
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Language", inversedBy="translations")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="base_language_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $baseLanguage;

    /**
     * @var \AppBundle\Entity\Language
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Language")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="language_id", referencedColumnName="id", onDelete="CASCADE")
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
     * Set baseLanguage.
     *
     * @param \AppBundle\Entity\Language $baseLanguage
     *
     * @return Translation
     */
    public function setBaseLanguage(\AppBundle\Entity\Language $baseLanguage)
    {
        $this->baseLanguage = $baseLanguage;

        return $this;
    }

    /**
     * Get baseLanguage.
     *
     * @return \AppBundle\Entity\Language
     */
    public function getBaseLanguage()
    {
        return $this->baseLanguage;
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

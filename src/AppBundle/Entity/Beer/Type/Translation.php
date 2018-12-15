<?php

namespace AppBundle\Entity\Beer\Type;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints;

/**
 * Translation.
 *
 * @ORM\Table(name="beer_type_translation", uniqueConstraints={@ORM\UniqueConstraint(columns={"language_id", "name"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Beer\Type\TranslationRepository")
 * @UniqueEntity(
 *  fields={"name", "language"},
 *  message="This type already exists."
 * )
 */
class Translation
{
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
     * @var \AppBundle\Entity\Beer\Type
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Beer\Type", inversedBy="translations")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="type_id", referencedColumnName="id", onDelete="CASCADE")
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
     * @param \AppBundle\Entity\Beer\Type $type
     *
     * @return Translation
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

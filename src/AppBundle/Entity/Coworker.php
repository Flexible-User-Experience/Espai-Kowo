<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Traits\DescriptionTrait;
use AppBundle\Entity\Traits\SlugTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Class Coworker
 *
 * @category Entity
 * @package  AppBundle\Entity
 * @author   Anton Serra <aserratorta@gmail.com>
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="name_unique", columns={"name", "surname"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CoworkerRepository")
 * @Vich\Uploadable
 */
class Coworker extends AbstractBase
{
    use DescriptionTrait;
    use SlugTrait;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     * @Gedmo\Slug(fields={"name", "surname"})
     */
    private $slug;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer", nullable=true, options={"default"=0})
     */
    private $position = 0;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $surname;

    /**
     * @var Category
     *
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="coworkers")
     */
    private $category;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=true)
     * @Assert\Email(strict=true, checkMX=true, checkHost=true)
     */
    private $email;

    /**
     * @var File
     *
     * @Vich\UploadableField(mapping="coworker", fileNameProperty="imageName")
     * @Assert\File(
     *     maxSize="10M",
     *     mimeTypes={"image/jpg", "image/jpeg", "image/png", "image/gif"}
     * )
     * @Assert\Image(minWidth=1200)
     */
    private $imageFile;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $imageName;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="SocialNetwork", mappedBy="coworker", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $socialNetworks;

    /**
     *
     *
     * Methods
     *
     *
     */

    /**
     * Coworker constructor
     */
    public function __construct()
    {
        $this->socialNetworks = new ArrayCollection();
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param int $position
     *
     * @return $this
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getSurname()
    {
        return $this->surname;
    }

    /**
     * @param string $surname
     *
     * @return Coworker
     */
    public function setSurname($surname)
    {
        $this->surname = $surname;

        return $this;
    }

    /**
     * @return Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param Category $category
     *
     * @return $this
     */
    public function setCategory(Category $category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     *
     * @return $this
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Set imageFile
     *
     * @param File|UploadedFile $imageFile
     *
     * @return $this
     */
    public function setImageFile(File $imageFile = null)
    {
        $this->imageFile = $imageFile;
        if ($imageFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTime('now');
        }

        return $this;
    }

    /**
     * Get imageFile
     *
     * @return File|UploadedFile
     */
    public function getImageFile()
    {
        return $this->imageFile;
    }

    /**
     * Set imageName
     *
     * @param string $imageName
     *
     * @return $this
     */
    public function setImageName($imageName)
    {
        $this->imageName = $imageName;

        return $this;
    }

    /**
     * Get imageName
     *
     * @return string
     */
    public function getImageName()
    {
        return $this->imageName;
    }

    /**
     * @return ArrayCollection
     */
    public function getSocialNetworks()
    {
        return $this->socialNetworks;
    }

    /**
     * @param ArrayCollection|array $socialNetworks
     *
     * @return $this
     */
    public function setSocialNetworks($socialNetworks)
    {
        $this->socialNetworks = $socialNetworks;

        return $this;
    }

    /**
     * @param SocialNetwork $socialNetwork
     *
     * @return $this
     */
    public function addSocialNetwork(SocialNetwork $socialNetwork)
    {
        $socialNetwork->setCoworker($this);
        $this->socialNetworks->add($socialNetwork);

        return $this;
    }

    /* @param SocialNetwork $socialNetwork
     *
     * @return $this
     */
    public function removeSocialNetwork(SocialNetwork $socialNetwork)
    {
        $this->socialNetworks->removeElement($socialNetwork);

        return $this;
    }
}

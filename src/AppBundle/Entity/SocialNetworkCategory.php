<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Traits\TitleTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class SocialNetworkCategory
 *
 * @category Entity
 * @package  AppBundle\Entity
 * @author   Anton Serra <aserratorta@gmail.com>
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SocialNetworkCategoryRepository")
 * @Vich\Uploadable
 * @UniqueEntity("title")
 */
class SocialNetworkCategory extends AbstractBase
{
    use TitleTrait;

    /**
     * @var File
     *
     * @Vich\UploadableField(mapping="social_network_category", fileNameProperty="imageName")
     * @Assert\File(
     *     maxSize = "10M",
     *     mimeTypes = {"image/jpg", "image/jpeg", "image/png", "image/gif"}
     * )
     * @Assert\Image(minWidth = 120)
     */
    private $imageFile;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $imageName;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $fontAwesomeClass;


    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="SocialNetwork", mappedBy="category", cascade={"persist", "remove"}, orphanRemoval=true)
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
     * SocialNetworkCategory constructor.
     */
    public function __construct()
    {
        $this->socialNetworks = new ArrayCollection();
    }

    /**
     * @return File|UploadedFile
     */
    public function getImageFile()
    {
        return $this->imageFile;
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
     * @return string
     */
    public function getImageName()
    {
        return $this->imageName;
    }

    /**
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
     * @return ArrayCollection
     */
    public function getSocialNetworks()
    {
        return $this->socialNetworks;
    }

    /**
     * @param ArrayCollection $socialNetworks
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
        $socialNetwork->setCategory($this);
        $this->socialNetworks->add($socialNetwork);

        return $this;
    }

    /**
     * @param SocialNetwork $socialNetwork
     *
     * @return $this
     */
    public function removeSocialNetwork(SocialNetwork $socialNetwork)
    {
        $this->socialNetworks->removeElement($socialNetwork);

        return $this;
    }

    /**
     * @return string
     */
    public function getFontAwesomeClass()
    {
        return $this->fontAwesomeClass;
    }

    /**
     * @param string $fontAwesomeClass
     *
     * @return SocialNetworkCategory
     */
    public function setFontAwesomeClass($fontAwesomeClass)
    {
        $this->fontAwesomeClass = $fontAwesomeClass;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString() {

        return $this->getTitle() ? $this->getTitle() : '---';
    }
}

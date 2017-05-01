<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Traits\DescriptionTrait;
use AppBundle\Entity\Traits\SlugTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Class Coworker.
 *
 * @category Entity
 *
 * @author   Anton Serra <aserratorta@gmail.com>
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="name_unique", columns={"name", "surname"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CoworkerRepository")
 * @Vich\Uploadable
 * @UniqueEntity({"name", "surname"})
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
     * @var int
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
     * @var int
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $gender;

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
     * @var File
     *
     * @Vich\UploadableField(mapping="coworker", fileNameProperty="imageNameBW")
     * @Assert\File(
     *     maxSize="10M",
     *     mimeTypes={"image/jpg", "image/jpeg", "image/png", "image/gif"}
     * )
     * @Assert\Image(minWidth=1200)
     */
    private $imageFileBW;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $imageNameBW;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="date", nullable=true)
     */
    private $birthday;

    /**
     * @var File
     *
     * @Vich\UploadableField(mapping="coworker", fileNameProperty="gifName")
     * @Assert\File(
     *     maxSize="10M",
     *     mimeTypes={"image/gif"}
     * )
     * @Assert\Image(maxWidth="780", minWidth="780", maxHeight="1168", minHeight="1168")
     */
    private $gifFile;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $gifName;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="SocialNetwork", mappedBy="coworker", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $socialNetworks;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=5, nullable=true)
     * @Assert\Regex(pattern="/\d{5}/", htmlPattern=false, message="Només 5 dígits numèrics")
     */
    private $printerCode;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $bookCode;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $ticketOfficeCode;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=32, nullable=true)
     */
    private $token;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="date", nullable=true)
     */
    private $dischargeDate;

    /**
     * Methods.
     */

    /**
     * Coworker constructor.
     */
    public function __construct()
    {
        $this->socialNetworks = new ArrayCollection();
    }

    /**
     * Get slug.
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
     * @return string
     */
    public function getFullName()
    {
        return $this->name.' '.$this->surname;
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
     * @return int
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * @param int $gender
     *
     * @return Coworker
     */
    public function setGender($gender)
    {
        $this->gender = $gender;

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
     * Set imageFile.
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
     * Get imageFile.
     *
     * @return File|UploadedFile
     */
    public function getImageFile()
    {
        return $this->imageFile;
    }

    /**
     * Set imageName.
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
     * Get imageName.
     *
     * @return string
     */
    public function getImageName()
    {
        return $this->imageName;
    }

    /**
     * Set imageFileBW.
     *
     * @param File|UploadedFile $imageFileBW
     *
     * @return $this
     */
    public function setImageFileBW(File $imageFileBW = null)
    {
        $this->imageFileBW = $imageFileBW;
        if ($imageFileBW) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTime('now');
        }

        return $this;
    }

    /**
     * Get imageFileBW.
     *
     * @return File|UploadedFile
     */
    public function getImageFileBW()
    {
        return $this->imageFileBW;
    }

    /**
     * Set imageNameBW.
     *
     * @param string $imageNameBW
     *
     * @return $this
     */
    public function setImageNameBW($imageNameBW)
    {
        $this->imageNameBW = $imageNameBW;

        return $this;
    }

    /**
     * Get imageNameBW.
     *
     * @return string
     */
    public function getImageNameBW()
    {
        return $this->imageNameBW;
    }

    /**
     * @return \DateTime
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * @param \DateTime $birthday
     *
     * @return $this
     */
    public function setBirthday($birthday)
    {
        $this->birthday = $birthday;

        return $this;
    }

    /**
     * Get gifFile.
     *
     * @return File|UploadedFile
     */
    public function getGifFile()
    {
        return $this->gifFile;
    }

    /**
     * Set gifFile.
     *
     * @param File|UploadedFile $gifFile
     *
     * @return $this
     */
    public function setGifFile(File $gifFile = null)
    {
        $this->gifFile = $gifFile;
        if ($gifFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTime('now');
        }

        return $this;
    }

    /**
     * Get gitName.
     *
     * @return string
     */
    public function getGifName()
    {
        return $this->gifName;
    }

    /**
     * Set gifName.
     *
     * @param string $gifName
     *
     * @return $this
     */
    public function setGifName($gifName)
    {
        $this->gifName = $gifName;

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

    /**
     * @return string
     */
    public function getPrinterCode()
    {
        return $this->printerCode;
    }

    /**
     * @param string $printerCode
     *
     * @return Coworker
     */
    public function setPrinterCode($printerCode)
    {
        $this->printerCode = $printerCode;

        return $this;
    }

    /**
     * @return int
     */
    public function getBookCode()
    {
        return $this->bookCode;
    }

    /**
     * @param int $bookCode
     *
     * @return Coworker
     */
    public function setBookCode($bookCode)
    {
        $this->bookCode = $bookCode;

        return $this;
    }

    /**
     * @return int
     */
    public function getTicketOfficeCode()
    {
        return $this->ticketOfficeCode;
    }

    /**
     * @param int $ticketOfficeCode
     *
     * @return Coworker
     */
    public function setTicketOfficeCode($ticketOfficeCode)
    {
        $this->ticketOfficeCode = $ticketOfficeCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param string $token
     *
     * @return $this
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDischargeDate()
    {
        return $this->dischargeDate;
    }

    /**
     * @param \DateTime $dischargeDate
     *
     * @return $this
     */
    public function setDischargeDate($dischargeDate)
    {
        $this->dischargeDate = $dischargeDate;

        return $this;
    }

    /**
     * To string.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->id ? $this->getFullName() : '---';
    }
}

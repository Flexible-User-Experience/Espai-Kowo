<?php
namespace AppBundle\Entity;

use AppBundle\Entity\Traits\DescriptionTrait;
/**
 * Class Coworker
 *
 * @category Entity
 * @package  AppBundle\Entity
 * @author   Anton Serra <aserratorta@gmail.com>
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CoworkerRepository")
 */
class Coworker extends AbstractBase
{
    use DescriptionTrait;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @var Category
     *
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="coworkers")
     * @ORM\JoinColumn(name="Category_id", referencedColumnName="id")
     */
    private $category;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=true)
     * @Assert\Email(strict = true, checkMX = true, checkHost = true)
     */
    private $email;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $facebook;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $tweeter;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $linkedin;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $github;

    /**
     *
     *
     * Methods
     *
     *
     */

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Coworker
     */
    public function setName($name)
    {
        $this->name = $name;
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
     * @return Coworker
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
     * @return Coworker
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return string
     */
    public function getFacebook()
    {
        return $this->facebook;
    }

    /**
     * @param string $facebook
     * @return Coworker
     */
    public function setFacebook($facebook)
    {
        $this->facebook = $facebook;
        return $this;
    }

    /**
     * @return string
     */
    public function getTweeter()
    {
        return $this->tweeter;
    }

    /**
     * @param string $tweeter
     * @return Coworker
     */
    public function setTweeter($tweeter)
    {
        $this->tweeter = $tweeter;
        return $this;
    }

    /**
     * @return string
     */
    public function getLinkedin()
    {
        return $this->linkedin;
    }

    /**
     * @param string $linkedin
     * @return Coworker
     */
    public function setLinkedin($linkedin)
    {
        $this->linkedin = $linkedin;
        return $this;
    }

    /**
     * @return string
     */
    public function getGithub()
    {
        return $this->github;
    }

    /**
     * @param string $github
     * @return Coworker
     */
    public function setGithub($github)
    {
        $this->github = $github;
        return $this;
    }

}
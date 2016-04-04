<?php
namespace AppBundle\Entity;

use AppBundle\Entity\Traits\DescriptionTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

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
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Url(checkDNS = true)
     */
    private $facebook;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Url(checkDNS = true)
     */
    private $twitter;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Url(checkDNS = true)
     */
    private $linkedin;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Url(checkDNS = true)
     */
    private $github;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Url(checkDNS = true)
     */
    private $web;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Url(checkDNS = true)
     */
    private $blog;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Url(checkDNS = true)
     */
    private $instagram;

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
     * @return boolean
     */
    public function getFacebook()
    {
        return $this->facebook;
    }

    /**
     * @param boolean $facebook
     * @return Coworker
     */
    public function setFacebook($facebook)
    {
        $this->facebook = $facebook;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getTwitter()
    {
        return $this->twitter;
    }

    /**
     * @param boolean $twitter
     * @return Coworker
     */
    public function setTwitter($twitter)
    {
        $this->twitter = $twitter;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getLinkedin()
    {
        return $this->linkedin;
    }

    /**
     * @param boolean $linkedin
     * @return Coworker
     */
    public function setLinkedin($linkedin)
    {
        $this->linkedin = $linkedin;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getGithub()
    {
        return $this->github;
    }

    /**
     * @param boolean $github
     * @return Coworker
     */
    public function setGithub($github)
    {
        $this->github = $github;
        return $this;
    }

    /**
     * @return string
     */
    public function getWeb()
    {
        return $this->web;
    }

    /**
     * @param string $web
     * @return Coworker
     */
    public function setWeb($web)
    {
        $this->web = $web;
        return $this;
    }

    /**
     * @return string
     */
    public function getBlog()
    {
        return $this->blog;
    }

    /**
     * @param string $blog
     * @return Coworker
     */
    public function setBlog($blog)
    {
        $this->blog = $blog;
        return $this;
    }

    /**
     * @return string
     */
    public function getInstagram()
    {
        return $this->instagram;
    }

    /**
     * @param string $instagram
     * @return Coworker
     */
    public function setInstagram($instagram)
    {
        $this->instagram = $instagram;
        return $this;
    }
}
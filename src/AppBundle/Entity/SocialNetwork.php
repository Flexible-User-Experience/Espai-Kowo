<?php
namespace AppBundle\Entity;

use AppBundle\Entity\Traits\TitleTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class SocialNetwork
 *
 * @category Entity
 * @package  AppBundle\Entity
 * @author   Anton Serra <aserratorta@gmail.com>
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SocialNetworkRepository")
 */
class SocialNetwork extends AbstractBase
{
    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Url(checkDNS = true)
     */
    private $url;

    /**
     * @var SocialNetworkCategory
     * @ORM\ManyToOne(targetEntity="SocialNetworkCategory", inversedBy="socialNetworks")
     */
    private $category;

    /**
     * @var Coworker
     * @ORM\ManyToOne(targetEntity="Coworker", inversedBy="socialNetworks")
     */
    private $coworker;

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
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     * @return SocialNetwork
     */
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @return SocialNetworkCategory
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param SocialNetworkCategory $category
     * @return SocialNetwork
     */
    public function setCategory($category)
    {
        $this->category = $category;
        return $this;
    }

    /**
     * @return Coworker
     */
    public function getCoworker()
    {
        return $this->coworker;
    }

    /**
     * @param Coworker $coworker
     * @return SocialNetwork
     */
    public function setCoworker($coworker)
    {
        $this->coworker = $coworker;
        return $this;
    }

    public function __toString() {

        return $this->getUrl() ? $this->getUrl() : '---';
    }

}
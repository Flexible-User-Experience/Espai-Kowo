<?php
namespace AppBundle\Entity;

use AppBundle\Entity\Traits\TitleTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Category
 *
 * @category Entity
 * @package  AppBundle\Entity
 * @author   Anton Serra <aserratorta@gmail.com>
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CategoryRepository")
 */
class Category extends AbstractBase
{
    use TitleTrait;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Coworker", mappedBy="category")
     */
    private $coworkers;

    /**
     *
     *
     * Methods
     *
     *
     */

    /**
     * Category constructor.
     */
    public function __construct()
    {
        $this->coworkers = new ArrayCollection();
    }

    /**
     * @return ArrayCollection
     */
    public function getCoworkers()
    {
        return $this->coworkers;
    }

    /**
     * @param ArrayCollection $coworkers
     * @return $this
     */
    public function setCoworkers(ArrayCollection $coworkers)
    {
        $this->coworkers = $coworkers;
        return $this;
    }

    public function __toString() {

        return $this->getTitle() ? $this->getTitle() : '---';
    }

}
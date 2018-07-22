<?php

namespace AppBundle\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Title trait
 *
 * @category Trait
 * @author   David Romaní <david@flux.cat>
 */
Trait TitleTrait
{
    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $title;

    /**
     * @param string $title
     *
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }
}

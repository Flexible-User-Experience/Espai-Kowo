<?php

namespace AppBundle\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Date trait
 *
 * @category Trait
 * @author   David Romaní <david@flux.cat>
 */
Trait DateTrait
{
    /**
     * @var \DateTime
     *
     * @ORM\Column(type="date")
     */
    private $date;

    /**
     * @param \DateTime $date
     *
     * @return $this
     */
    public function setDate(\DateTime $date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @return string
     */
    public function getDateString()
    {
        return $this->getDate()->format('d/m/Y');
    }

}

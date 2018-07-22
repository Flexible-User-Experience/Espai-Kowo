<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Abstract entities base class
 *
 * @category Entity
 * @author   David Romaní <david@flux.cat>
 *
 * @Gedmo\SoftDeleteable(fieldName="removedAt", timeAware=false)
 */
abstract class AbstractBase
{
    /**
     * @var integer
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    protected $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="update")
     */
    protected $updatedAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $removedAt;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    protected $enabled = true;

    /**
     * Methods
     */

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param \DateTime $createdAt
     *
     * @return $this
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $updatedAt
     *
     * @return $this
     */
    public function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $removedAt
     *
     * @return $this
     */
    public function setRemovedAt(\DateTime $removedAt)
    {
        $this->removedAt = $removedAt;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getRemovedAt()
    {
        return $this->removedAt;
    }

    /**
     * @return $this
     */
    public function remove()
    {
        $this->setRemovedAt(new \DateTime());

        return $this;
    }

    /**
     * @return bool
     */
    public function isDeleted()
    {
        return $this->removedAt !== null;
    }

    /**
     * @param boolean $enabled
     *
     * @return $this
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->id ? $this->getId() . ' · ' . $this->getCreatedAt()->format('d/m/Y') : '---';
    }
}

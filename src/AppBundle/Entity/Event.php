<?php
namespace AppBundle\Entity;

use AppBundle\Entity\Traits\DateTrait;
use AppBundle\Entity\Traits\DescriptionTrait;
use AppBundle\Entity\Traits\TitleTrait;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Event
 *
 * @category Entity
 * @package  AppBundle\Entity
 * @author   Anton Serra <aserratorta@gmail.com>
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EventRepository")
 */
class Event extends AbstractBase
{
    use TitleTrait;
    use DescriptionTrait;
    use DateTrait;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=true)
     * @Assert\Email(strict = true, checkMX = true, checkHost = true)
     */
    private $email;

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
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return Event
     */
    public function setType($type)
    {
        $this->type = $type;
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
     * @return Event
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

}
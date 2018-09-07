<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Spending.
 *
 * @category Entity
 *
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SpendingRepository")
 */
class Spending extends AbstractBase
{
    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $anfixCode;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="date", nullable=false)
     */
    private $date;

    /**
     * @var SpendingCategory
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\SpendingCategory")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     */
    private $category;

    /**
     * @var Provider
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Provider")
     * @ORM\JoinColumn(name="provider_id", referencedColumnName="id")
     */
    private $provider;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $description;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=false)
     */
    private $baseAmount;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isPayed;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="date", nullable=true)
     */
    private $paymentDate;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", options={"default"=0})
     */
    private $paymentMethod;

    /**
     * Methods.
     */

    /**
     * @return string
     */
    public function getAnfixCode()
    {
        return $this->anfixCode;
    }

    /**
     * @param string $anfixCode
     *
     * @return $this
     */
    public function setAnfixCode($anfixCode)
    {
        $this->anfixCode = $anfixCode;

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
     * @return SpendingCategory
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param SpendingCategory $category
     *
     * @return $this
     */
    public function setCategory($category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Provider
     */
    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * @param Provider $provider
     *
     * @return $this
     */
    public function setProvider($provider)
    {
        $this->provider = $provider;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     *
     * @return $this
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return float
     */
    public function getBaseAmount()
    {
        return $this->baseAmount;
    }

    /**
     * @return float
     */
    public function getBaseAmountString()
    {
        return number_format($this->baseAmount, 2, ',', '.');
    }

    /**
     * @param float $baseAmount
     *
     * @return $this
     */
    public function setBaseAmount($baseAmount)
    {
        $this->baseAmount = $baseAmount;

        return $this;
    }

    /**
     * @return bool
     */
    public function isPayed()
    {
        return $this->isPayed;
    }

    /**
     * @param bool $isPayed
     *
     * @return $this
     */
    public function setIsPayed($isPayed)
    {
        $this->isPayed = $isPayed;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getPaymentDate()
    {
        return $this->paymentDate;
    }

    /**
     * @param \DateTime $paymentDate
     *
     * @return $this
     */
    public function setPaymentDate(\DateTime $paymentDate)
    {
        $this->paymentDate = $paymentDate;

        return $this;
    }

    /**
     * @return int
     */
    public function getPaymentMethod()
    {
        return $this->paymentMethod;
    }

    /**
     * @param int $paymentMethod
     *
     * @return $this
     */
    public function setPaymentMethod($paymentMethod)
    {
        $this->paymentMethod = $paymentMethod;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->id ? $this->getDate()->format('d/m/Y').' · '.$this->getProvider().' · '.$this->getBaseAmountString() : '---';
    }
}

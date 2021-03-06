<?php

namespace AppBundle\Entity;

use AppBundle\Enum\LanguageEnum;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Customer.
 *
 * @category Entity
 *
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CustomerRepository")
 * @UniqueEntity({"tic"})
 */
class Customer extends AbstractBase
{
    /**
     * @var string Tax Identification Number
     *
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $tic;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $alias;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $address;

    /**
     * @var City
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\City")
     */
    private $city;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $phone;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=true)
     * @Assert\Email(strict=true, checkMX=true, checkHost=true)
     */
    private $email;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean", options={"default"=0})
     */
    private $isEnterprise = false;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Coworker", mappedBy="customer", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $coworkers;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", options={"default"=0})
     */
    private $invoicesLanguage;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", options={"default"=0})
     */
    private $paymentMethod;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $ibanForBankDraftPayment;

    /**
     * Methods.
     */

    /**
     * Customer constructor.
     */
    public function __construct()
    {
        $this->coworkers = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getTic()
    {
        return $this->tic;
    }

    /**
     * @param string $tic
     *
     * @return $this
     */
    public function setTic($tic)
    {
        $this->tic = $tic;

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
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * @param string $alias
     *
     * @return $this
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;

        return $this;
    }

    /**
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param string $address
     *
     * @return $this
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @return City
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param City $city
     *
     * @return $this
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     *
     * @return $this
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

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
     * @return bool
     */
    public function isEnterprise()
    {
        return $this->isEnterprise;
    }

    /**
     * @return bool
     */
    public function getEnterprise()
    {
        return $this->isEnterprise();
    }

    /**
     * @param bool $isEnterprise
     *
     * @return $this
     */
    public function setIsEnterprise($isEnterprise)
    {
        $this->isEnterprise = $isEnterprise;

        return $this;
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
     *
     * @return $this
     */
    public function setCoworkers($coworkers)
    {
        $this->coworkers = $coworkers;

        return $this;
    }

    /**
     * @param Coworker $coworker
     *
     * @return $this
     */
    public function addCoworker(Coworker $coworker)
    {
        if (!$this->coworkers->contains($coworker)) {
            $this->coworkers->add($coworker);
            $coworker->setCustomer($this);
        }

        return $this;
    }

    /**
     * @param Coworker $coworker
     *
     * @return $this
     */
    public function removeCoworker(Coworker $coworker)
    {
        if ($this->coworkers->contains($coworker)) {
            $this->coworkers->removeElement($coworker);
        }

        return $this;
    }

    /**
     * @return int
     */
    public function getInvoicesLanguage()
    {
        return $this->invoicesLanguage;
    }

    /**
     * @return int
     */
    public function getInvoicesLanguageLocaleString()
    {
        return LanguageEnum::getLocalesEnumArray()[$this->invoicesLanguage];
    }

    /**
     * @param int $invoicesLanguage
     *
     * @return $this
     */
    public function setInvoicesLanguage($invoicesLanguage)
    {
        $this->invoicesLanguage = $invoicesLanguage;

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
    public function getIbanForBankDraftPayment()
    {
        return $this->ibanForBankDraftPayment;
    }

    /**
     * @return string
     */
    public function getIbanForBankDraftPaymentString()
    {
        $result = $this->getIbanForBankDraftPayment();
        if (strlen($this->getIbanForBankDraftPayment()) == 24) {
            $result = substr($result, 0, 4).' '.substr($result, 4, 4).' '.substr($result, 8, 4).' '.substr($result, 12, 4).' '.substr($result, 16, 4).' '.substr($result, 20, 4);
        }

        return $result;
    }

    /**
     * @param string $ibanForBankDraftPayment
     *
     * @return $this
     */
    public function setIbanForBankDraftPayment($ibanForBankDraftPayment)
    {
        $this->ibanForBankDraftPayment = $ibanForBankDraftPayment;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->id ? ($this->alias ? $this->getAlias() : $this->getTic().' · '.$this->getName()) : '---';
    }
}

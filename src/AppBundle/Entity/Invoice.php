<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Class Invoice.
 *
 * @category Entity
 *
 * @ORM\Entity(repositoryClass="AppBundle\Repository\InvoiceRepository")
 * @UniqueEntity(fields={"number", "year"})
 */
class Invoice extends AbstractBase
{
    const TAX_IVA = 21;
    const TAX_IRPF = 0;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=false)
     */
    private $number;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=false)
     */
    private $year;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="date", nullable=false)
     */
    private $date;

    /**
     * @var Customer
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Customer")
     * @ORM\JoinColumn(name="customer_id", referencedColumnName="id")
     */
    private $customer;

    /**
     * @var ArrayCollection|array|InvoiceLine[]
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\InvoiceLine", mappedBy="invoice", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $lines;

    /**
     * @var float
     *
     * @ORM\Column(type="float", options={"default"=21})
     */
    private $taxPercentage = self::TAX_IVA;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true, options={"default"=0})
     */
    private $irpfPercentage = self::TAX_IRPF;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private $baseAmount;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private $totalAmount;

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
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isSended;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="date", nullable=true)
     */
    private $sendDate;

    /**
     * Methods.
     */

    /**
     * Invoice constructor.
     */
    public function __construct()
    {
        $this->lines = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @param int $number
     *
     * @return $this
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * @return int
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * @param int $year
     *
     * @return $this
     */
    public function setYear($year)
    {
        $this->year = $year;

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
     * @return Customer
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * @param Customer $customer
     *
     * @return $this
     */
    public function setCustomer($customer)
    {
        $this->customer = $customer;

        return $this;
    }

    /**
     * @return InvoiceLine[]|array|ArrayCollection
     */
    public function getLines()
    {
        return $this->lines;
    }

    /**
     * @param InvoiceLine[]|array|ArrayCollection $lines
     *
     * @return $this
     */
    public function setLines($lines)
    {
        $this->lines = $lines;

        return $this;
    }

    /**
     * @param InvoiceLine $line
     *
     * @return $this
     */
    public function basicAddLine(InvoiceLine $line)
    {
        $this->lines->add($line);

        return $this;
    }

    /**
     * @param InvoiceLine $line
     *
     * @return $this
     */
    public function addLine(InvoiceLine $line)
    {
        if (!$this->lines->contains($line)) {
            $line->setInvoice($this);
            $this->lines->add($line);
            $this->setBaseAmount($this->getBaseAmount() + $line->getTotal());
        }

        return $this;
    }

    /**
     * @param InvoiceLine $line
     *
     * @return $this
     */
    public function removeLine(InvoiceLine $line)
    {
        if ($this->lines->contains($line)) {
            $this->lines->removeElement($line);
            $this->setBaseAmount($this->getBaseAmount() - $line->getTotal());
        }

        return $this;
    }

    /**
     * @return float
     */
    public function getTaxPercentage()
    {
        return $this->taxPercentage;
    }

    /**
     * @param float $taxPercentage
     *
     * @return Invoice
     */
    public function setTaxPercentage($taxPercentage)
    {
        $this->taxPercentage = $taxPercentage;

        return $this;
    }

    /**
     * @return float
     */
    public function getIrpfPercentage()
    {
        return $this->irpfPercentage;
    }

    /**
     * @param float $irpfPercentage
     *
     * @return Invoice
     */
    public function setIrpfPercentage($irpfPercentage)
    {
        $this->irpfPercentage = $irpfPercentage;

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
     * @return float
     */
    public function getTotalAmount()
    {
        return $this->totalAmount;
    }

    /**
     * @return string
     */
    public function getTotalAmountString()
    {
        return number_format($this->totalAmount, 2, ',', '.').'€';
    }

    /**
     * @param float $totalAmount
     *
     * @return Invoice
     */
    public function setTotalAmount($totalAmount)
    {
        $this->totalAmount = $totalAmount;

        return $this;
    }

    /**
     * @return string
     */
    public function getInvoiceNumber()
    {
        return $this->year.'/'.$this->number;
    }

    /**
     * @return string
     */
    public function getInvoiceNumberWithF()
    {
        return 'F'.$this->getInvoiceNumber();
    }

    /**
     * @return string
     */
    public function getSluggedInvoiceNumber()
    {
        return $this->year.'-'.$this->number;
    }

    /**
     * @return string
     */
    public function getUnderscoredInvoiceNumber()
    {
        return $this->year.'_'.$this->number;
    }

    /**
     * @return float
     */
    public function calculateBaseAmount()
    {
        $result = 0.0;
        /** @var InvoiceLine $line */
        foreach ($this->lines as $line) {
            $result = $result + $line->calculateBaseAmount();
        }

        return $result;
    }

    /**
     * @return float|int
     */
    public function calculateTaxPercentage()
    {
        return $this->getBaseAmount() * ($this->getTaxPercentage() / 100);
    }

    /**
     * @return float|int
     */
    public function calculateIrpfPercentatge()
    {
        return $this->getBaseAmount() * ($this->getIrpfPercentage() / 100);
    }

    /**
     * @return float|int
     */
    public function calculateTotalAmount()
    {
        return $this->getBaseAmount() + $this->calculateTaxPercentage() - $this->calculateIrpfPercentatge();
    }

    /**
     * @param int|float $value
     *
     * @return float
     */
    public function calculateIrpfOverhead($value)
    {
        return $value / (1 - ($this->getIrpfPercentage() / 100));
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
    public function setPaymentDate($paymentDate)
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
     * @return bool
     */
    public function isSended()
    {
        return $this->isSended;
    }

    /**
     * @param bool $isSended
     *
     * @return $this
     */
    public function setIsSended($isSended)
    {
        $this->isSended = $isSended;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getSendDate()
    {
        return $this->sendDate;
    }

    /**
     * @param \DateTime $sendDate
     *
     * @return $this
     */
    public function setSendDate($sendDate)
    {
        $this->sendDate = $sendDate;

        return $this;
    }

    /**
     * @return string
     */
    public function getDebtorMandate()
    {
        return $this->getCustomer()->getTic().'-'.strtoupper(substr($this->getCustomer()->getName(), 0, 1)).'-'.uniqid();
    }


    /**
     * @return string
     */
    public function getDebtorMandateSignDate()
    {
        return $this->getCustomer()->getCreatedAt()->format('d-m-Y');
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->id ? $this->getInvoiceNumber().' · '.$this->getCustomer().' · '.$this->getTotalAmountString() : '---';
    }
}

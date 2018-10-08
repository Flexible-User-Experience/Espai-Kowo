<?php

namespace AppBundle\Model;

/**
 * Class EstimatedQuarterTax.
 * 
 * @category Model
 */
class EstimatedQuarterTax
{
    /**
     * @var int
     */
    private $quarter;

    /**
     * @var float
     */
    private $salesTaxPortion;
    
    /**
     * @var float
     */
    private $expensesTaxPortion;

    /**
     * Methods
     */

    /**
     * EstimatedQuarterTax constructor.
     *
     * @param int $quarter
     * @param float $salesTaxPortion
     * @param float $expensesTaxPortion
     */
    public function __construct($quarter, $salesTaxPortion, $expensesTaxPortion)
    {
        $this->quarter = $quarter;
        $this->salesTaxPortion = $salesTaxPortion;
        $this->expensesTaxPortion = $expensesTaxPortion;
    }

    /**
     * @return int
     */
    public function getQuarter()
    {
        return $this->quarter;
    }

    /**
     * @param int $quarter
     *
     * @return $this
     */
    public function setQuarter($quarter)
    {
        $this->quarter = $quarter;

        return $this;
    }

    /**
     * @return float
     */
    public function getSalesTaxPortion()
    {
        return $this->salesTaxPortion;
    }

    /**
     * @return string
     */
    public function getSalesTaxPortionString()
    {
        return number_format($this->getSalesTaxPortion(), 2, '\'', '.');
    }

    /**
     * @param float $salesTaxPortion
     *
     * @return $this
     */
    public function setSalesTaxPortion($salesTaxPortion)
    {
        $this->salesTaxPortion = $salesTaxPortion;

        return $this;
    }

    /**
     * @return float
     */
    public function getExpensesTaxPortion()
    {
        return $this->expensesTaxPortion;
    }

    /**
     * @return string
     */
    public function getExpensesTaxPortionString()
    {
        return number_format($this->getExpensesTaxPortion(), 2, '\'', '.');
    }

    /**
     * @param float $expensesTaxPortion
     *
     * @return $this
     */
    public function setExpensesTaxPortion($expensesTaxPortion)
    {
        $this->expensesTaxPortion = $expensesTaxPortion;

        return $this;
    }

    /**
     * @return float
     */
    public function getResultTaxPortion()
    {
        return $this->getSalesTaxPortion() - $this->getExpensesTaxPortion();
    }

    /**
     * @return string
     */
    public function getResultTaxPortionString()
    {
        return number_format($this->getResultTaxPortion() * -1, 2, '\'', '.');
    }
}

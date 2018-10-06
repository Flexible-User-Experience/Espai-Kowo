<?php

namespace AppBundle\Service;

use AppBundle\Repository\InvoiceRepository;
use AppBundle\Repository\SpendingRepository;

/**
 * Class AccountingService.
 *
 * @category Service
 */
class AccountingService
{
    const LOCAL_GOVERNMENT_TAX = 1.21;

    /**
     * @var AccountingCalendarService
     */
    private $acs;

    /**
     * @var InvoiceRepository
     */
    private $ir;

    /**
     * @var SpendingRepository
     */
    private $sr;

    /**
     * Methods.
     */

    /**
     * ChartsFactoryService constructor.
     *
     * @param AccountingCalendarService $acs
     * @param InvoiceRepository         $ir
     * @param SpendingRepository        $sr
     */
    public function __construct(AccountingCalendarService $acs, InvoiceRepository $ir, SpendingRepository $sr)
    {
        $this->acs = $acs;
        $this->ir = $ir;
        $this->sr = $sr;
    }

    /**
     * @return int
     */
    public function getCurrentQuarterForLocalGovernmentAccounting()
    {
        return $this->acs->getCurrentQuarterForLocalGovernmentAccounting();
    }

    /**
     * @param \DateTime $date
     *
     * @return int
     */
    public function getMonthlySalesAmountForDate(\DateTime $date)
    {
        return $this->ir->getMonthlySalesAmountForDate($date);
    }

    /**
     * @return int
     */
    public function getQuarterSalesAmount()
    {
        $quarter = $this->acs->getCurrentQuarterForLocalGovernmentAccounting();
        $begin = $this->acs->getFirstDateForQuarter($quarter);
        $end = $this->acs->getLastDateForQuarter($quarter);

        return $this->ir->getSalesAmountForInterval($begin, $end);
    }

    /**
     * @return float
     */
    public function getEstimatedQuarterSalesTaxPortion()
    {
        return $this->calculateTaxPortionOfValue($this->getQuarterSalesAmount());
    }

    /**
     * @param \DateTime $date
     *
     * @return int
     */
    public function getMonthlyExpensesAmountForDate(\DateTime $date)
    {
        return $this->sr->getMonthlyExpensesAmountForDate($date);
    }

    /**
     * @return int
     */
    public function getQuarterExpensesAmount()
    {
        $quarter = $this->acs->getCurrentQuarterForLocalGovernmentAccounting();
        $begin = $this->acs->getFirstDateForQuarter($quarter);
        $end = $this->acs->getLastDateForQuarter($quarter);

        return $this->sr->getExpensesAmountForInterval($begin, $end);
    }

    /**
     * @return float
     */
    public function getEstimatedQuarterExpensesTaxPortion()
    {
        return $this->calculateTaxPortionOfValue($this->getQuarterExpensesAmount());
    }

    /**
     * @param int|float $value
     *
     * @return float
     */
    private function calculateTaxPortionOfValue($value)
    {
        $result = 0.0;
        if ($value > 0) {
            $result = $value / self::LOCAL_GOVERNMENT_TAX;
        }

        return $result;
    }
}

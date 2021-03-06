<?php

namespace AppBundle\Service;

use AppBundle\Enum\MonthEnum;
use AppBundle\Model\EstimatedQuarterTax;
use SaadTazi\GChartBundle\DataTable\DataRow;
use SaadTazi\GChartBundle\DataTable\DataCell;
use SaadTazi\GChartBundle\DataTable\DataTable;
use SaadTazi\GChartBundle\DataTable\DataColumn;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class ChartsFactoryService.
 *
 * @category Service
 */
class ChartsFactoryService
{
    /**
     * @var Translator
     */
    private $ts;

    /**
     * @var AccountingService
     */
    private $as;

    /**
     * Methods.
     */

    /**
     * ChartsFactoryService constructor.
     *
     * @param TranslatorInterface $ts
     * @param AccountingService   $as
     */
    public function __construct(TranslatorInterface $ts, AccountingService $as)
    {
        $this->ts = $ts;
        $this->as = $as;
    }

    /**
     * @return EstimatedQuarterTax
     */
    public function getEstimatedQuarterTaxes()
    {
        $result = new EstimatedQuarterTax(
            $this->as->getCurrentQuarterForLocalGovernmentAccounting(),
            $this->as->getEstimatedQuarterSalesTaxPortion(),
            $this->as->getEstimatedQuarterExpensesTaxPortion()
        );

        return $result;
    }

    /**
     * @return DataTable
     *
     * @throws \SaadTazi\GChartBundle\DataTable\Exception\InvalidColumnTypeException
     * @throws \Exception
     */
    public function buildLastYearBalanceChart()
    {
        $dt = new DataTable();
        $dt->addColumnObject(new DataColumn('id1', 'title', 'string'));
        $dt->addColumnObject(new DataColumn('id2', $this->ts->trans('backend.admin.block.charts.sales', array(), 'messages'), 'number'));
        $dt->addColumnObject(new DataColumn('id3', $this->ts->trans('backend.admin.block.charts.expenses', array(), 'messages'), 'number'));
        $dt->addColumnObject(new DataColumn('id4', $this->ts->trans('backend.admin.block.charts.results', array(), 'messages'), 'number'));

        $date = new \DateTime();
        $date->sub(new \DateInterval('P12M'));
        $interval = new \DateInterval('P1M');
        for ($i = 0; $i <= 12; ++$i) {
            $sales = $this->as->getMonthlySalesAmountForDate($date);
            $expenses = $this->as->getMonthlyExpensesAmountForDate($date);
            $results = $sales - $expenses;
            $dt->addRowObject($this->buildIncomingCellsRow($date, $sales, $expenses, $results));
            $date->add($interval);
        }

        return $dt;
    }

    /**
     * @param \DateTime $key
     * @param float|int $sales
     * @param float|int $expenses
     * @param float|int $results
     *
     * @return DataRow
     */
    private function buildIncomingCellsRow($key, $sales, $expenses, $results)
    {
        return new DataRow(array(
                new DataCell(MonthEnum::getShortTranslatedMonthEnumArray()[intval($key->format('n'))].'. '.$key->format('y')),
                new DataCell($sales, number_format($sales, 0, ',', '.').'€'),
                new DataCell($expenses, number_format($expenses, 0, ',', '.').'€'),
                new DataCell($results, number_format($results, 0, ',', '.').'€'),
            )
        );
    }
}

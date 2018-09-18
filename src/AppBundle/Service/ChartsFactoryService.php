<?php

namespace AppBundle\Service;

use AppBundle\Enum\MonthEnum;
use AppBundle\Repository\InvoiceRepository;
use AppBundle\Repository\SpendingRepository;
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
     * @param TranslatorInterface $ts
     * @param InvoiceRepository   $ir
     * @param SpendingRepository  $sr
     */
    public function __construct(TranslatorInterface $ts, InvoiceRepository $ir, SpendingRepository $sr)
    {
        $this->ts = $ts;
        $this->ir = $ir;
        $this->sr = $sr;
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
        $dt->addColumnObject(new DataColumn('id2', $this->ts->trans('backend.admin.block.charts.incomings', array(), 'messages'), 'number'));
        $dt->addColumnObject(new DataColumn('id3', $this->ts->trans('backend.admin.block.charts.expenses', array(), 'messages'), 'number'));

        $date = new \DateTime();
        $date->sub(new \DateInterval('P12M'));
        $interval = new \DateInterval('P1M');
        for ($i = 0; $i <= 12; ++$i) {
            $sales = $this->ir->getMonthlySalesAmountForDate($date);
            $expenses = $this->sr->getMonthlyExpensesAmountForDate($date);
            $dt->addRowObject($this->buildIncomingCellsRow($date, $sales, $expenses));
            $date->add($interval);
        }

        return $dt;
    }

    /**
     * @param \DateTime $key
     * @param float|int $sales
     * @param float|int $expenses
     *
     * @return DataRow
     */
    private function buildIncomingCellsRow($key, $sales, $expenses)
    {
        return new DataRow(array(
                new DataCell(MonthEnum::getShortTranslatedMonthEnumArray()[intval($key->format('n'))].'. '.$key->format('y')),
                new DataCell($sales, number_format($sales, 0, ',', '.').'€'),
                new DataCell($expenses, number_format($expenses, 0, ',', '.').'€'),
            )
        );
    }
}

<?php

namespace AppBundle\Xls\Filter;

use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;

/**
 * Class ReadXlsFilter.
 *
 * @category Xls
 */
class ReadXlsFilter implements IReadFilter
{
    /**
     * @var int
     */
    private $startRow;

    /**
     * @var int
     */
    private $endRow;

    /**
     * ReadFilterXls constructor.
     *
     * @param int $startRow
     * @param int $endRow
     */
    public function __construct($startRow = 3, $endRow = 0)
    {
        $this->startRow = $startRow;
        $this->endRow = $endRow;
    }

    /**
     * @param string $column
     * @param int    $row
     * @param string $worksheetName
     *
     * @return bool
     */
    public function readCell($column, $row, $worksheetName = '')
    {
        if ($this->endRow > 0) {
            return $row >= $this->startRow && $row <= $this->endRow;
        }

        return $row >= $this->startRow;
    }
}

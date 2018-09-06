<?php

namespace AppBundle\Service;

use AppBundle\Xls\Filter\ReadXlsFilter;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Exception;
use PhpOffice\PhpSpreadsheet\Reader\Xls as XlsReader;
use PhpOffice\PhpSpreadsheet\Reader\Xml as XmlXlsReader;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

/**
 * Class SpreadsheetService.
 *
 * @category Service
 */
class SpreadsheetService
{
    /**
     * @param string $filepath
     *
     * @return Spreadsheet
     *
     * @throws Exception
     */
    public function loadSpreadsheet($filepath)
    {
        return IOFactory::load($filepath);
    }

    /**
     * @param string $filepath
     *
     * @return Spreadsheet
     *
     * @throws Exception
     */
    public function loadXlsSpreadsheetReadOnly($filepath)
    {
        $reader = new XlsReader();
        $reader->setReadDataOnly(true);
        $reader->setReadFilter(new ReadXlsFilter());

        return $reader->load($filepath);
    }

    /**
     * @param string                $filepath
     * @param array|string[]|string $worksheets
     *
     * @return Spreadsheet
     *
     * @throws Exception
     */
    public function loadWorksheetsXlsSpreadsheetReadOnly($filepath, $worksheets)
    {
        $reader = new XlsReader();
        $reader->setReadDataOnly(true);
        $reader->setLoadSheetsOnly($worksheets);
        $reader->setReadFilter(new ReadXlsFilter());

        return $reader->load($filepath);
    }

    /**
     * @param string $filepath
     *
     * @return Spreadsheet
     *
     * @throws Exception
     */
    public function loadXmlXlsSpreadsheetReadOnly($filepath)
    {
        $reader = new XmlXlsReader();
        $reader->setReadDataOnly(true);
        $reader->setReadFilter(new ReadXlsFilter());

        return $reader->load($filepath);
    }

    /**
     * @param string                $filepath
     * @param array|string[]|string $worksheets
     *
     * @return Spreadsheet
     *
     * @throws Exception
     */
    public function loadWorksheetsXmlXlsSpreadsheetReadOnly($filepath, $worksheets)
    {
        $reader = new XmlXlsReader();
        $reader->setReadDataOnly(true);
        $reader->setLoadSheetsOnly($worksheets);
        $reader->setReadFilter(new ReadXlsFilter());

        return $reader->load($filepath);
    }
}

<?php

namespace AppBundle\Pdf;

use AppBundle\Service\SmartAssetsHelperService;

/**
 * Class BasePdf.
 *
 * @category Pdf
 */
class BasePdf extends \TCPDF
{
    const PDF_WIDTH = 210;
    const PDF_HEIGHT = 297;
    const PDF_MARGIN_LEFT = 20;
    const PDF_MARGIN_RIGHT = 20;
    const PDF_MARGIN_TOP = 20;
    const PDF_MARGIN_BOTTOM = 0;
    const MARGIN_VERTICAL_SMALL = 3;
    const MARGIN_VERTICAL_BIG = 12;
    const BRAND_COLOR = array(168, 208, 25);

    /**
     * @var SmartAssetsHelperService
     */
    private $sahs;

    /**
     * Methods.
     */

    /**
     * BasePdf constructor.
     *
     * @param SmartAssetsHelperService $sahs
     */
    public function __construct(SmartAssetsHelperService $sahs)
    {
        parent::__construct();
        $this->sahs = $sahs;
    }

    /**
     * Page header.
     */
    public function Header()
    {
        // logo
        $this->Image($this->sahs->getAbsoluteAssetPathByContext('/bundles/app/img/logo-espai-kowo.png'), self::PDF_MARGIN_LEFT, self::PDF_MARGIN_TOP, 30);
        $this->setFontStyle(null, 'I', 8);
    }

    /**
     * Page footer.
     *
     * @param string $txt
     */
    public function drawFooter($txt)
    {
        $this->SetXY(self::PDF_MARGIN_LEFT, self::PDF_HEIGHT - self::PDF_MARGIN_BOTTOM - 8);
        $this->setFontStyle(null, '', 8);
        $this->Cell(0, 0, $txt, 0, 0, 'C');
    }

    /**
     * @param string $font
     * @param string $style
     * @param int    $size
     */
    public function setFontStyle($font = 'dejavusans', $style = '', $size = 12)
    {
        // dejavusans is a UTF-8 Unicode font, if you only need to
        // print standard ASCII chars, you can use core fonts like
        // helvetica or times to reduce file size.
        $this->SetFont($font, $style, $size, '', true);
    }

    /**
     * @param float $y
     */
    public function drawInvoiceLineSeparator($y)
    {
        $this->Line(
            self::PDF_MARGIN_LEFT,
            $y,
            self::PDF_WIDTH - self::PDF_MARGIN_RIGHT,
            $y,
            array(
                'width' => 0.8,
                'cap' => 'butt',
                'join' => 'miter',
                'dash' => 0,
                'color' => self::BRAND_COLOR,
            )
        );
    }

    /**
     * Set brand color
     */
    public function setBrandColor()
    {
        $this->setColor('draw', self::BRAND_COLOR[0], self::BRAND_COLOR[1], self::BRAND_COLOR[2]);
        $this->setColor('fill', self::BRAND_COLOR[0], self::BRAND_COLOR[1], self::BRAND_COLOR[2]);
        $this->setColor('text', self::BRAND_COLOR[0], self::BRAND_COLOR[1], self::BRAND_COLOR[2]);
    }

    /**
     * Set brand color
     */
    public function setBlackColor()
    {
        $this->setColor('draw', 0, 0, 0);
        $this->setColor('fill', 0, 0, 0);
        $this->setColor('text', 0, 0, 0);
    }

    /**
     * @param float $x
     * @param float $y
     * @param float $w
     * @param float $h
     */
    public function drawSvg($x, $y, $w, $h)
    {
        $this->ImageSVG($this->sahs->getAbsoluteAssetPathByContext('/bundles/app/svg/logo-main-homepage-green.svg'), $x, $y, $w, $h, '', '', '', 0, false);
    }

    /**
     * @param float $val
     *
     * @return string
     */
    public function floatStringFormat($val)
    {
        return number_format($val, 2, ',', '.');
    }

    /**
     * @param float $val
     *
     * @return string
     */
    public function floatMoneyFormat($val)
    {
        return $this->floatStringFormat($val).' €';
    }
}

<?php

namespace AppBundle\Pdf;

use AppBundle\Entity\Invoice;
use AppBundle\Entity\InvoiceLine;
use AppBundle\Enum\LanguageEnum;
use AppBundle\Service\SmartAssetsHelperService;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use WhiteOctober\TCPDFBundle\Controller\TCPDFController;

/**
 * Class InvoiceBuilderPdf.
 *
 * @category Pdf
 */
class InvoiceBuilderPdf
{
    /**
     * @var TCPDFController
     */
    private $tcpdf;

    /**
     * @var SmartAssetsHelperService
     */
    private $sahs;

    /**
     * @var Translator
     */
    private $ts;

    /**
     * @var string project web title
     */
    private $pwt;

    /**
     * @var array fiscal data
     */
    private $ekfd;

    /**
     * InvoiceBuilderPdf constructor.
     *
     * @param TCPDFController          $tcpdf
     * @param SmartAssetsHelperService $sahs
     * @param Translator               $ts
     * @param string                   $pwt
     * @param array                    $ekfd
     */
    public function __construct(TCPDFController $tcpdf, SmartAssetsHelperService $sahs, Translator $ts, $pwt, $ekfd)
    {
        $this->tcpdf = $tcpdf;
        $this->sahs = $sahs;
        $this->ts = $ts;
        $this->pwt = $pwt;
        $this->ekfd = $ekfd;
    }

    /**
     * @param Invoice $invoice
     *
     * @return \TCPDF
     */
    public function build(Invoice $invoice)
    {
        $this->ts->setLocale($invoice->getCustomer()->getInvoicesLanguageLocaleString());

        /** @var BasePdf $pdf */
        $pdf = $this->tcpdf->create($this->sahs);

        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor($this->pwt);
        $pdf->SetTitle($this->ts->trans('backend.admin.invoice.invoice').' '.$invoice->getInvoiceNumber());
        $pdf->SetSubject($this->ts->trans('backend.admin.invoice.detail').' '.$this->ts->trans('backend.admin.invoice.invoice').' '.$invoice->getInvoiceNumber());
        // set default font subsetting mode
        $pdf->setFontSubsetting(true);
        // remove default header/footer
        $pdf->setPrintHeader(true);
        $pdf->setPrintFooter(false);
        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        // set margins
        $pdf->SetMargins(BasePdf::PDF_MARGIN_LEFT, BasePdf::PDF_MARGIN_TOP, BasePdf::PDF_MARGIN_RIGHT);
        $pdf->SetAutoPageBreak(true, BasePdf::PDF_MARGIN_BOTTOM);
        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        // Add start page
        $pdf->AddPage(PDF_PAGE_ORIENTATION, PDF_PAGE_FORMAT, true, true);
        $pdf->drawFooter($this->ekfd['invoice_footer']);
        $pdf->SetXY(BasePdf::PDF_MARGIN_LEFT, BasePdf::PDF_MARGIN_TOP + BasePdf::MARGIN_VERTICAL_SMALL * 11);

        // gaps
        $column2Gap = 114;
        $interliner = 1;
        $verticalTableGapSmall = 5.5;
        $pdf->setBrandColor();

        // invoice number & date
        $pdf->setFontStyle(null, 'B', 9);
        $pdf->Write(0, $this->ts->trans('backend.admin.invoice.pdf.invoice_number').' '.$invoice->getInvoiceNumberWithF(), '', false, 'L', true);
        $pdf->Write(0, $this->ts->trans('backend.admin.invoice.pdf.invoice_date').' '.$invoice->getDateString(), '', false, 'L', true);
        $pdf->Ln(BasePdf::MARGIN_VERTICAL_BIG);

        // invoice header
        $pdf->setFontStyle(null, '', 14);
        $pdf->Write(0, $this->ts->trans('backend.admin.invoice.pdf.invoice_data'), '', false, 'L', false);
        $pdf->SetX($column2Gap);
        $pdf->Write(0, $this->ts->trans('backend.admin.invoice.pdf.customer_data'), '', false, 'L', true);
        $pdf->drawInvoiceLineSeparator($pdf->GetY() + 1);
        $pdf->Ln(BasePdf::MARGIN_VERTICAL_SMALL * 1.5);

        // invoice fiscal data
        $pdf->setFontStyle(null, 'B', 9);
        $pdf->setBlackColor();

        $pdf->Write(0, $this->ekfd['company_name'], '', false, 'L', false);
        $pdf->SetX($column2Gap);
        $pdf->Write(0, $invoice->getCustomer()->getName(), '', false, 'L', true);
        $pdf->Ln($interliner);

        $pdf->setFontStyle(null, '', 9);
        $pdf->setBlackColor();

        $pdf->Write(0, $this->ekfd['vat_number'], '', false, 'L', false);
        $pdf->SetX($column2Gap);
        $pdf->Write(0, $invoice->getCustomer()->getTic(), '', false, 'L', true);
        $pdf->Ln($interliner);

        $pdf->Write(0, $this->ekfd['address'], '', false, 'L', false);
        $pdf->SetX($column2Gap);
        $pdf->Write(0, $invoice->getCustomer()->getAddress(), '', false, 'L', true);
        $pdf->Ln($interliner);

        $pdf->Write(0, $this->ekfd['city'], '', false, 'L', false);
        $pdf->SetX($column2Gap);
        $pdf->Write(0, $invoice->getCustomer()->getCity()->getCanonicalPostalString(), '', false, 'L', true);
        $pdf->Ln($interliner);

        $pdf->Write(0, $this->ekfd['province'], '', false, 'L', false);
        $pdf->SetX($column2Gap);
        $pdf->Write(0, $invoice->getCustomer()->getCity()->getProvince()->getSurroundedName(), '', false, 'L', true);
        $pdf->Ln($interliner);

        if ($invoice->getCustomer()->getCity()->getProvince()->getCountry() == LanguageEnum::EN) {
            $pdf->Write(0, 'SPAIN', '', false, 'L', false);
            $pdf->SetX($column2Gap);
            $pdf->Write(0, $invoice->getCustomer()->getCity()->getProvince()->getCountry(), '', false, 'L', true);
            $pdf->Ln($interliner);
        }

        // invoice lines table header
        $pdf->setBrandColor();
        $pdf->setFontStyle(null, '', 14);
        $pdf->Ln(BasePdf::MARGIN_VERTICAL_BIG);
        $pdf->Write(0, $this->ts->trans('backend.admin.invoice.pdf.table_header'), '', false, 'L', true);
        $pdf->drawInvoiceLineSeparator($pdf->GetY() + 1);
        $pdf->Ln(BasePdf::MARGIN_VERTICAL_SMALL);

        $pdf->setFontStyle(null, 'B', 9);
        $pdf->Cell(110, $verticalTableGapSmall, $this->ts->trans('backend.admin.invoiceLine.description'), false, 0, 'L');
        $pdf->Cell(15, $verticalTableGapSmall, $this->ts->trans('backend.admin.invoiceLine.units'), false, 0, 'R');
        $pdf->Cell(25, $verticalTableGapSmall, $this->ts->trans('backend.admin.invoiceLine.priceUnit'), false, 0, 'R');
        $pdf->Cell(20, $verticalTableGapSmall, $this->ts->trans('backend.admin.invoiceLine.total'), false, 1, 'R');

        // invoice lines table rows
        $pdf->setBlackColor();
        $pdf->setFontStyle(null, '', 9);
        /** @var InvoiceLine $line */
        foreach ($invoice->getLines() as $line) {
            // MultiCell($w, $h, $txt, $border=0, $align='J', $fill=0, $ln=1, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0)
            $pdf->MultiCell(110, $verticalTableGapSmall, $line->getDescription(), 0, 'L', 0, 0, '', '', true, 0, false, true, 0, 'M');
            $pdf->MultiCell(15, $verticalTableGapSmall, ($line->getUnits() ? $pdf->floatStringFormat($line->getUnits()) : ''), 0, 'R', 0, 0, '', '', true, 0, false, true, 0, 'M');
            $pdf->MultiCell(25, $verticalTableGapSmall, ($line->getUnits() ? $pdf->floatMoneyFormat($line->getPriceUnit()) : ''), 0, 'R', 0, 0, '', '', true, 0, false, true, 0, 'M');
            $pdf->MultiCell(20, $verticalTableGapSmall, ($line->getUnits() ? $pdf->floatMoneyFormat($line->calculateBaseAmount()) : ''), 0, 'R', 0, 1, '', '', true, 0, false, true, 0, 'M');
        }

        // horitzonal divider
        $pdf->Ln(BasePdf::MARGIN_VERTICAL_SMALL);
        $pdf->drawInvoiceLineSeparator($pdf->GetY());
        $pdf->Ln(BasePdf::MARGIN_VERTICAL_SMALL);

        // invoice table footer
        // base
        $pdf->MultiCell(150, $verticalTableGapSmall, $this->ts->trans('backend.admin.invoice.baseAmount'), 0, 'R', 0, 0, '', '', true, 0, false, true, 0, 'M');
        $pdf->MultiCell(20, $verticalTableGapSmall, $pdf->floatMoneyFormat($invoice->calculateBaseAmount()), 0, 'R', 0, 1, '', '', true, 0, false, true, 0, 'M');
        // iva tax
        $pdf->MultiCell(150, $verticalTableGapSmall, '+'.$invoice->getTaxPercentage().'% IVA', 0, 'R', 0, 0, '', '', true, 0, false, true, 0, 'M');
        $pdf->MultiCell(20, $verticalTableGapSmall, $pdf->floatMoneyFormat($invoice->calculateTaxPercentage()), 0, 'R', 0, 1, '', '', true, 0, false, true, 0, 'M');

        // horitzonal divider
        $pdf->Ln(BasePdf::MARGIN_VERTICAL_SMALL);
        $pdf->drawInvoiceLineSeparator($pdf->GetY());
        $pdf->Ln(BasePdf::MARGIN_VERTICAL_SMALL);

        // total
        $pdf->setFontStyle(null, 'B', 9);
        $pdf->MultiCell(150, $verticalTableGapSmall, strtoupper($this->ts->trans('backend.admin.invoiceLine.total')), 0, 'R', 0, 0, '', '', true, 0, false, true, 0, 'M');
        $pdf->MultiCell(20, $verticalTableGapSmall, $pdf->floatMoneyFormat($invoice->getTotalAmount()), 0, 'R', 0, 1, '', '', true, 0, false, true, 0, 'M');

        // invoice payment type section
        $pdf->setBrandColor();
        $pdf->setFontStyle(null, '', 14);
        $pdf->Ln(BasePdf::MARGIN_VERTICAL_BIG);
        $pdf->Write(0, $this->ts->trans('backend.admin.invoice.pdf.payment_type'), '', false, 'L', true);
        $pdf->drawInvoiceLineSeparator($pdf->GetY() + 1);
        $pdf->Ln(BasePdf::MARGIN_VERTICAL_SMALL);

        // payment method
        $pdf->setFontStyle(null, '', 9);
        $pdf->setBlackColor();
        $pdf->Write(0, $this->ts->trans('backend.admin.invoice.pdf.account_number'), '', false, 'L', true);
        $pdf->Ln($interliner);
        $pdf->Write(0, $this->ekfd['bank_account'], '', false, 'L', true);

        return $pdf;
    }
}

<?php

namespace AppBundle\Service;

use AppBundle\Entity\InvoiceLine;
use AppBundle\Entity\Invoice;
use AppBundle\Enum\PaymentMethodEnum;
use Digitick\Sepa\TransferFile\Facade\CustomerDirectDebitFacade;
use Digitick\Sepa\TransferFile\Factory\TransferFileFacadeFactory;
use Digitick\Sepa\PaymentInformation;
use Digitick\Sepa\GroupHeader;
use Digitick\Sepa\Exception\InvalidArgumentException;
use Digitick\Sepa\Exception\InvalidPaymentMethodException;
use Digitick\Sepa\Util\StringHelper;

/**
 * Class XmlSepaBuilderService.
 *
 * @category Service
 */
class XmlSepaBuilderService
{
    const DIRECT_DEBIT_PAIN_CODE = 'pain.008.001.02';
    const DIRECT_DEBIT_LI_CODE = 'CORE';
    const DEFAULT_REMITANCE_INFORMATION = 'Import mensual';

    /**
     * @var SpanishSepaHelperService
     */
    private $sshs;

    /**
     * @var array fiscal data array
     */
    private $fda;

    /**
     * @var string fiscal name
     */
    private $fname;

    /**
     * @var string FIC fiscal identification code
     */
    private $fic;

    /**
     * @var string IBAN code
     */
    private $iban;

    /**
     * @var string BIC number
     */
    private $bic;

    /**
     * Methods.
     */

    /**
     * XmlSepaBuilderService constructor.
     *
     * @param SpanishSepaHelperService $sshs
     * @param array $fda
     */
    public function __construct(SpanishSepaHelperService $sshs, $fda)
    {
        $this->sshs = $sshs;
        $this->fda = $fda;
        $this->fname = $fda['company_name'];
        $this->fic = $fda['vat_number'];
        $this->iban = $this->removeSpacesFrom($fda['bank_account']);
        $this->bic = $this->removeSpacesFrom($fda['bic_number']);
    }

    /**
     * @param string    $paymentId
     * @param \DateTime $dueDate
     * @param Invoice   $invoice
     *
     * @return string
     *
     * @throws InvalidArgumentException
     * @throws InvalidPaymentMethodException
     */
    public function buildDirectDebitSingleInvoiceXml($paymentId, \DateTime $dueDate, Invoice $invoice)
    {
        $directDebit = $this->buildDirectDebit($paymentId);
        $this->addPaymentInfo($directDebit, $paymentId, $dueDate);
        $this->validate($invoice);
        $this->addTransfer($directDebit, $paymentId, $invoice);

        return $directDebit->asXML();
    }

    /**
     * @param string          $paymentId
     * @param \DateTime       $dueDate
     * @param Invoice[]|array $invoices
     *
     * @return string
     *
     * @throws InvalidArgumentException
     * @throws InvalidPaymentMethodException
     */
    public function buildDirectDebitInvoicesXml($paymentId, \DateTime $dueDate, $invoices)
    {
        $directDebit = $this->buildDirectDebit($paymentId);
        $this->addPaymentInfo($directDebit, $paymentId, $dueDate);
        /** @var Invoice $invoice */
        foreach ($invoices as $invoice) {
            $this->validate($invoice);
            $this->addTransfer($directDebit, $paymentId, $invoice);
        }

        return $directDebit->asXML();
    }

    /**
     * @param string $paymentId
     * @param bool   $isTest
     *
     * @return CustomerDirectDebitFacade
     */
    private function buildDirectDebit($paymentId, $isTest = false)
    {
        $msgId = 'MID'.StringHelper::sanitizeString($paymentId);
        $header = new GroupHeader($msgId, strtoupper(StringHelper::sanitizeString($this->fname)), $isTest);
        $header->setCreationDateTimeFormat('Y-m-d\TH:i:s');
        $header->setInitiatingPartyId($this->sshs->getSpanishCreditorIdFromNif($this->fic));

        return TransferFileFacadeFactory::createDirectDebitWithGroupHeader($header, self::DIRECT_DEBIT_PAIN_CODE);
    }

    /**
     * @param CustomerDirectDebitFacade $directDebit
     * @param string                    $paymentId
     * @param \DateTime                 $dueDate
     *
     * @throws InvalidArgumentException
     */
    private function addPaymentInfo(CustomerDirectDebitFacade &$directDebit, $paymentId, \DateTime $dueDate)
    {
        $directDebit->addPaymentInfo($paymentId, array(
            'id' => StringHelper::sanitizeString($paymentId),
            'dueDate' => $dueDate,
            'creditorName' => strtoupper(StringHelper::sanitizeString($this->fname)),
            'creditorAccountIBAN' => $this->iban,
            'creditorAgentBIC' => $this->bic,
            'seqType' => PaymentInformation::S_ONEOFF,
            'creditorId' => $this->sshs->getSpanishCreditorIdFromNif($this->fic),
            'localInstrumentCode' => self::DIRECT_DEBIT_LI_CODE,
        ));
    }

    /**
     * @param CustomerDirectDebitFacade $directDebit
     * @param string                    $paymentId
     * @param Invoice                   $invoice
     *
     * @throws InvalidArgumentException
     */
    private function addTransfer(CustomerDirectDebitFacade &$directDebit, $paymentId, $invoice)
    {
        $remitanceInformation = self::DEFAULT_REMITANCE_INFORMATION;
        if (count($invoice->getLines()) > 0) {
            /** @var InvoiceLine $firstLine */
            $firstLine = $invoice->getLines()[0];
            $remitanceInformation = $firstLine->getDescription();
        }

        $directDebit->addTransfer($paymentId, array(
            'amount' => $invoice->getTotalAmount(),
            'debtorIban' => $this->removeSpacesFrom($invoice->getCustomer()->getIbanForBankDraftPayment()),
            'debtorName' => $invoice->getCustomer()->getName(),
            'debtorMandate' => $invoice->getDebtorMandate(),
            'debtorMandateSignDate' => $invoice->getDebtorMandateSignDate(),
            'remittanceInformation' => $remitanceInformation,
            'endToEndId' => StringHelper::sanitizeString($invoice->getInvoiceNumberWithF()),
        ));
    }

    /**
     * @param string $value
     *
     * @return string
     */
    private function removeSpacesFrom($value)
    {
        return str_replace(' ', '', $value);
    }

    /**
     * @param Invoice $invoice
     *
     * @throws InvalidPaymentMethodException
     */
    private function validate(Invoice $invoice)
    {
        if ($invoice->getPaymentMethod() != PaymentMethodEnum::BANK_DRAFT) {
            throw new InvalidPaymentMethodException('Invalid payment method found in invoice ID# '.$invoice->getId());
        }
        if (!$invoice->getCustomer()->getIbanForBankDraftPayment()) {
            throw new InvalidPaymentMethodException('No IBAN found in customer ID# '.$invoice->getCustomer()->getId());
        }
    }
}

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

/**
 * Class XmlSepaBuilderService.
 *
 * @category Service
 */
class XmlSepaBuilderService
{
    const DIRECT_DEBIT_PAIN_CODE = 'pain.008.001.02';
    const DEFAULT_REMITANCE_INFORMATION = 'Import mensual';

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
     * @param array $fda
     */
    public function __construct($fda)
    {
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
        $this->validateInvoice($invoice);
        $directDebit = $this->buildDirectDebit($paymentId);
        $this->addPaymentInfo($directDebit, $paymentId, $dueDate);
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
            $this->validateInvoice($invoice);
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
        $msgId = 'MID'.$this->transformInvalidSepaChars($paymentId);
        $header = new GroupHeader($msgId, $this->fname, $isTest);
        $header->setInitiatingPartyId($this->transformInvalidSepaChars($this->fic));

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
        // creates a payment, it's possible to create multiple payments, "$paymentId" is the identifier for the transactions
        $directDebit->addPaymentInfo($paymentId, array(
            'id' => $paymentId,
            'dueDate' => $dueDate, // optional. Otherwise default period is used
            'creditorName' => $this->fname,
            'creditorAccountIBAN' => $this->iban,
            'creditorAgentBIC' => $this->bic,
            'seqType' => PaymentInformation::S_ONEOFF,
            'creditorId' => $this->fic,
            'localInstrumentCode' => 'CORE', // default. optional.
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

        // add a Single Transaction to the named payment
        $directDebit->addTransfer($paymentId, array(
            'amount' => $invoice->getBaseAmount(),
            'debtorIban' => $this->removeSpacesFrom($invoice->getCustomer()->getIbanForBankDraftPayment()),
//            'debtorBic' => $this->removeSpacesFrom($invoice->getMainBank()->getSwiftCode()),
            'debtorName' => $invoice->getCustomer()->getName(),
            'debtorMandate' => $invoice->getDebtorMandate(),
            'debtorMandateSignDate' => $invoice->getDebtorMandateSignDate(),
            'remittanceInformation' => $remitanceInformation,
            'endToEndId' => 'Factura num. '.$invoice->getInvoiceNumberWithF(), // optional, if you want to provide additional structured info
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
     * @param string $value
     *
     * @return string
     */
    private function transformInvalidSepaChars($value)
    {
        $result = str_replace('Ñ', 'N', $value);
        $result = str_replace('ñ', 'n', $result);
        $result = str_replace('Ç', 'C', $result);
        $result = str_replace('ç', 'c', $result);
        $result = str_replace('_', '-', $result);
        $result = str_replace('À', 'A', $result);
        $result = str_replace('Á', 'A', $result);
        $result = str_replace('Ä', 'A', $result);
        $result = str_replace('È', 'E', $result);
        $result = str_replace('É', 'E', $result);
        $result = str_replace('Ë', 'E', $result);
        $result = str_replace('Ì', 'I', $result);
        $result = str_replace('Í', 'I', $result);
        $result = str_replace('Ï', 'I', $result);
        $result = str_replace('Ò', 'O', $result);
        $result = str_replace('Ó', 'O', $result);
        $result = str_replace('Ö', 'O', $result);
        $result = str_replace('Ù', 'U', $result);
        $result = str_replace('Ú', 'U', $result);
        $result = str_replace('Ü', 'U', $result);
        $result = str_replace('à', 'a', $result);
        $result = str_replace('á', 'a', $result);
        $result = str_replace('ä', 'a', $result);
        $result = str_replace('è', 'e', $result);
        $result = str_replace('é', 'e', $result);
        $result = str_replace('ë', 'e', $result);
        $result = str_replace('ì', 'i', $result);
        $result = str_replace('í', 'i', $result);
        $result = str_replace('ï', 'i', $result);
        $result = str_replace('ò', 'o', $result);
        $result = str_replace('ó', 'o', $result);
        $result = str_replace('ö', 'o', $result);
        $result = str_replace('ù', 'u', $result);
        $result = str_replace('ú', 'u', $result);
        $result = str_replace('ü', 'u', $result);
        $result = str_replace('&', '&amp;', $result);
        $result = str_replace('<', '&lt;', $result);
        $result = str_replace('>', '&gt;;', $result);
        $result = str_replace('"', '&quot;', $result);
        $result = str_replace("'", '&apos;', $result);

        return $result;
    }

    /**
     * @param Invoice $invoice
     *
     * @throws InvalidPaymentMethodException
     */
    private function validateInvoice(Invoice $invoice)
    {
        if ($invoice->getPaymentMethod() != PaymentMethodEnum::BANK_DRAFT) {
            throw new InvalidPaymentMethodException('Invalid payment method found in invoice ID# '.$invoice->getId());
        }
        if (!$invoice->getCustomer()->getIbanForBankDraftPayment()) {
            throw new InvalidPaymentMethodException('No IBAN found in customer ID# '.$invoice->getCustomer()->getId());
        }
    }
}

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
        if ($invoice->getPaymentMethod() != PaymentMethodEnum::BANK_DRAFT || !$invoice->getCustomer()->getIbanForBankDraftPayment()) {
            throw new InvalidPaymentMethodException('Invalid payment method found in invoice ID# '.$invoice->getId());
        }
        $directDebit = $this->buildDirectDebit();
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
        $directDebit = $this->buildDirectDebit();
        $this->addPaymentInfo($directDebit, $paymentId, $dueDate);
        /** @var Invoice $invoice */
        foreach ($invoices as $invoice) {
            if ($invoice->getPaymentMethod() != PaymentMethodEnum::BANK_DRAFT || !$invoice->getCustomer()->getIbanForBankDraftPayment()) {
                throw new InvalidPaymentMethodException('Invalid payment method found in invoice ID# '.$invoice->getId());
            }
            $this->addTransfer($directDebit, $paymentId, $invoice);
        }

        return $directDebit->asXML();
    }

    /**
     * @return CustomerDirectDebitFacade
     */
    private function buildDirectDebit()
    {
        $today = new \DateTime();
        $header = new GroupHeader($today->format('Y-m-d-H-i-s'), $this->fname);
        $header->setInitiatingPartyId($this->fic);

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
}

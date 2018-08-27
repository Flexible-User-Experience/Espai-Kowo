<?php

namespace AppBundle\Enum;

/**
 * PaymentMethodEnum class.
 *
 * @category Enum
 */
class PaymentMethodEnum
{
    const BANK_DRAFT = 0;
    const BANK_TRANSFER = 1;
    const CASH = 2;

    /**
     * @return array
     */
    public static function getEnumArray()
    {
        return array(
            self::BANK_DRAFT => 'backend.admin.invoice.pdf.payment_method.bank_draft',
            self::BANK_TRANSFER => 'backend.admin.invoice.pdf.payment_method.bank_transfer',
            self::CASH => 'backend.admin.invoice.pdf.payment_method.bank_cash',
        );
    }
}

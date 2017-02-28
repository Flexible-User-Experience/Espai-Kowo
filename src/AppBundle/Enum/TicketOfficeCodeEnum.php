<?php

namespace AppBundle\Enum;

/**
 * TicketOfficeCodeEnum class.
 *
 * @category Enum
 *
 * @author   David Romaní <david@flux.cat>
 */
class TicketOfficeCodeEnum
{
    const TICKET_OFFICE_CODE_1 = 0;
    const TICKET_OFFICE_CODE_2 = 1;
    const TICKET_OFFICE_CODE_3 = 2;
    const TICKET_OFFICE_CODE_4 = 3;
    const TICKET_OFFICE_CODE_5 = 4;
    const TICKET_OFFICE_CODE_6 = 5;
    const TICKET_OFFICE_CODE_7 = 6;
    const TICKET_OFFICE_CODE_8 = 7;
    const TICKET_OFFICE_CODE_11 = 8;
    const TICKET_OFFICE_CODE_12 = 9;
    const TICKET_OFFICE_CODE_13 = 10;
    const TICKET_OFFICE_CODE_14 = 11;

    /**
     * @return array
     */
    public static function getEnumArray()
    {
        return array(
            self::TICKET_OFFICE_CODE_1 => '1 (S080)',
            self::TICKET_OFFICE_CODE_2 => '2 (S300)',
            self::TICKET_OFFICE_CODE_3 => '3 S(502)',
            self::TICKET_OFFICE_CODE_4 => '4 (S629)',
            self::TICKET_OFFICE_CODE_5 => '5 (S407)',
            self::TICKET_OFFICE_CODE_6 => '6 (S637)',
            self::TICKET_OFFICE_CODE_7 => '7 (S043)',
            self::TICKET_OFFICE_CODE_8 => '8 (S500)',
            self::TICKET_OFFICE_CODE_11 => '11 (S381)',
            self::TICKET_OFFICE_CODE_12 => '12 (S215)',
            self::TICKET_OFFICE_CODE_13 => '13 (S170)',
            self::TICKET_OFFICE_CODE_14 => '14 (S436)',
        );
    }
}

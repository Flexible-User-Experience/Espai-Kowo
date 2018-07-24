<?php

namespace AppBundle\Enum;

/**
 * BookCodeEnum class.
 *
 * @category Enum
 */
class BookCodeEnum
{
    const BOOK_CODE_1 = 0;
    const BOOK_CODE_2 = 1;
    const BOOK_CODE_3 = 2;
    const BOOK_CODE_4 = 3;
    const BOOK_CODE_5 = 4;
    const BOOK_CODE_6 = 5;
    const BOOK_CODE_7 = 6;
    const BOOK_CODE_8 = 7;
    const BOOK_CODE_9 = 8;
    const BOOK_CODE_10 = 9;
    const BOOK_CODE_11 = 10;
    const BOOK_CODE_12 = 11;

    /**
     * @return array
     */
    public static function getEnumArray()
    {
        return array(
            self::BOOK_CODE_1 => '1 (S378)',
            self::BOOK_CODE_2 => '2 (S356)',
            self::BOOK_CODE_3 => '3 S(359)',
            self::BOOK_CODE_4 => '4 (S315)',
            self::BOOK_CODE_5 => '5 (S546)',
            self::BOOK_CODE_6 => '6 (S058)',
            self::BOOK_CODE_7 => '7 (S322)',
            self::BOOK_CODE_8 => '8 (S061)',
            self::BOOK_CODE_9 => '9 (S180)',
            self::BOOK_CODE_10 => '10 (S079)',
            self::BOOK_CODE_11 => '11 (S360)',
            self::BOOK_CODE_12 => '12 (S320)',
        );
    }
}

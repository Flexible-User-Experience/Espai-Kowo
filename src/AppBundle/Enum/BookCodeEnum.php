<?php

namespace AppBundle\Enum;

/**
 * BookCodeEnum class.
 *
 * @category Enum
 *
 * @author   David Romaní <david@flux.cat>
 */
class BookCodeEnum
{
    const S378 = 0;
    const S356 = 1;
    const S359 = 2;
    const S315 = 3;
    const S546 = 4;
    const S058 = 5;
    const S322 = 6;
    const S061 = 7;
    const S180 = 8;
    const S079 = 9;
    const S360 = 10;
    const S320 = 11;

    /**
     * @return array
     */
    public static function getEnumArray()
    {
        return array(
            self::S378 => '1',
            self::S356 => '2',
            self::S359 => '3',
            self::S315 => '4',
            self::S546 => '5',
            self::S058 => '6',
            self::S322 => '7',
            self::S061 => '8',
            self::S180 => '9',
            self::S079 => '10',
            self::S360 => '11',
            self::S320 => '12',
        );
    }
}

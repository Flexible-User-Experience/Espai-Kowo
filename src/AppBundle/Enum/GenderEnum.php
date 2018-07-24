<?php

namespace AppBundle\Enum;

/**
 * GenderEnum class.
 *
 * @category Enum
 */
class GenderEnum
{
    const MALE = 0;
    const FEMALE = 1;

    /**
     * @return array
     */
    public static function getEnumArray()
    {
        return array(
            self::MALE => 'Home',
            self::FEMALE => 'Dona',
        );
    }
}

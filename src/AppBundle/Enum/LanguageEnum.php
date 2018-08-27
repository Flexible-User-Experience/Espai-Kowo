<?php

namespace AppBundle\Enum;

/**
 * LanguageEnum class.
 *
 * @category Enum
 */
class LanguageEnum
{
    const CA = 0;
    const ES = 1;
    const EN = 2;

    /**
     * @return array
     */
    public static function getEnumArray()
    {
        return array(
            self::CA => 'Català',
            self::ES => 'Español',
            self::EN => 'English',
        );
    }

    /**
     * @return array
     */
    public static function getLocalesEnumArray()
    {
        return array(
            self::CA => 'ca',
            self::ES => 'es',
            self::EN => 'en',
        );
    }
}

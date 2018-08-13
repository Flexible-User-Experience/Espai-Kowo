<?php

namespace AppBundle\Enum;

/**
 * Class ReceiptYearMonthEnum.
 *
 * @category Enum
 */
class YearEnum
{
    const APP_FIRST_YEAR = 2015;

    /**
     * @return array
     */
    public static function getYearEnumArray()
    {
        $result = array();
        $now = new \DateTime();
        $currentYear = intval($now->format('Y'));
        $steps = $currentYear - self::APP_FIRST_YEAR + 1;
        for ($i = 0; $i < $steps; ++$i) {
            $year = $currentYear - $i;
            $result["$year"] = $year;
        }

        return $result;
    }
}

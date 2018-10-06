<?php

namespace AppBundle\Service;

/**
 * Class AccountingCalendarService.
 *
 * @category Service
 */
class AccountingCalendarService
{
    /**
     * @return int
     */
    public function getCurrentQuarterForLocalGovernmentAccounting()
    {
        $result = 4;
        $today = new \DateTime();
        $year = $today->format('Y');
        $breakpointDay1 = (new \DateTime($year.'-01-30'))->format('Y-m-d');
        $breakpointDay2 = (new \DateTime($year.'-04-20'))->format('Y-m-d');
        $breakpointDay3 = (new \DateTime($year.'-07-20'))->format('Y-m-d');
        $breakpointDay4 = (new \DateTime($year.'-10-20'))->format('Y-m-d');
        $today = $today->format('Y-m-d');

        if ($breakpointDay1 < $today && $today <= $breakpointDay2) {
            $result = 1;
        } elseif ($breakpointDay2 < $today && $today <= $breakpointDay3) {
            $result = 2;
        } elseif ($breakpointDay3 < $today && $today <= $breakpointDay4) {
            $result = 3;
        }

        return $result;
    }

    /**
     * @param int $quarter
     *
     * @return \DateTime
     */
    public function getFirstDateForQuarter($quarter)
    {
        $date = new \DateTime('first day of january this year');
        if ($quarter == 2) {
            $interval = new \DateInterval('P3M');
            $date->add($interval);
        } elseif ($quarter == 3) {
            $interval = new \DateInterval('P6M');
            $date->add($interval);
        } elseif ($quarter == 4) {
            $interval = new \DateInterval('P9M');
            $date->add($interval);
        }

        return $date;
    }

    /**
     * @param int $quarter
     *
     * @return \DateTime
     */
    public function getLastDateForQuarter($quarter)
    {
        $interval = new \DateInterval('P3M');
        $oneDayInterval = new \DateInterval('P1D');
        $date = new \DateTime('first day of january this year');
        $date->add($interval);
        $date->sub($oneDayInterval);

        if ($quarter == 2) {
            $interval = new \DateInterval('P3M');
            $date->add($interval);
            $date->sub($oneDayInterval);
        } elseif ($quarter == 3) {
            $interval = new \DateInterval('P6M');
            $date->add($interval);
            $date->sub($oneDayInterval);
        } elseif ($quarter == 4) {
            $interval = new \DateInterval('P9M');
            $date->add($interval);
            $date->sub($oneDayInterval);
        }

        return $date;
    }
}

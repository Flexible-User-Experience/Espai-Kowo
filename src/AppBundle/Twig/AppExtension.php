<?php

namespace AppBundle\Twig;

use AppBundle\Entity\Category;
use AppBundle\Entity\Coworker;
use AppBundle\Entity\Invoice;
use AppBundle\Entity\User;
use AppBundle\Enum\UserRolesEnum;
use AppBundle\Model\CategoryHistogramHelperModel;

/**
 * Class AppExtension.
 *
 * @category Twig
 */
class AppExtension extends \Twig_Extension
{
    /**
     * Twig Functions.
     */

    /**
     * @return array
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('randomErrorText', array($this, 'randomErrorTextFunction')),
            new \Twig_SimpleFunction('drawProgress', array($this, 'drawProgress')),
            new \Twig_SimpleFunction('drawAgesList', array($this, 'drawAgesList')),
            new \Twig_SimpleFunction('drawCategoryProgress', array($this, 'drawCategoryProgress')),
        );
    }

    /**
     * @param int $length length of Random String returned
     *
     * @return string
     */
    public function randomErrorTextFunction($length = 1024)
    {
        // Character List to Pick from
        $chrList = '012 3456 789 abcdef ghij klmno pqrs tuvwxyz ABCD EFGHIJK LMN OPQ RSTU VWXYZ';
        // Minimum/Maximum times to repeat character List to seed from
        $chrRepeatMin = 1; // Minimum times to repeat the seed string
        $chrRepeatMax = 30; // Maximum times to repeat the seed string

        return substr(str_shuffle(str_repeat($chrList, mt_rand($chrRepeatMin, $chrRepeatMax))), 1, $length);
    }

    /**
     * @param string $month
     * @param int $takeUp
     * @param int $discharge
     * @param bool $isGreenBar
     *
     * @return string
     */
    public function drawProgress($month, $takeUp, $discharge, $isGreenBar = true)
    {
        $color = $isGreenBar ? 'success' : 'danger';
        $divider = $takeUp + $discharge;
        $result = '<h6 class="box-title">'.$month.'</h6>'.
                    '<div class="progress">'.
                        '<div class="progress-bar progress-bar-'.$color.'" style="width:'.$this->solveAverage($takeUp, $divider).'%">'.
                            ($takeUp ? $takeUp : '').
                        '</div>'.
                    '</div>'
            ;

        return $result;
    }

    /**
     * @param array $agesList["cby"] (string|null) with coworker birthday year value
     *
     * @return string
     */
    public function drawAgesList($agesList)
    {
        $min = 0;
        $max = 0;
        /** @var array $age */
        foreach ($agesList as $age) {
            if (!is_null($age['cby'])) {
                $value = intval($age['cby']);
                if ($max <= $value) {
                    $max = $value;

                }
                if ($min == 0) {
                    $min = $value;
                }
            }
        }
        $now = new \DateTime();
        $currentYear = intval($now->format('Y'));
        $maxAge = $currentYear - $min;
        $minAge = $currentYear - $max;
        $middleAge = round(($maxAge + $minAge) / 2, 1);

        $result = '<h6 class="box-title">Mínima | Mitjana | Màxima</h6>'.
                    '<div class="progress">'.
                        '<div class="progress-bar progress-bar-warning" style="width:'.$minAge.'%">'.
                            $minAge.
                        '</div>'.
                        '<div class="progress-bar progress-bar-success progress-bar-striped" style="width:'.($maxAge - $minAge).'%">'.
                            $middleAge.
                        '</div>'.
                        '<div class="progress-bar progress-bar-warning" style="width:'.(100 - $maxAge).'%">'.
                            $maxAge.
                        '</div>'.
                    '</div>';

        return $result;
    }

    /**
     * @param Category[]|array $items
     * @param int $divider amount to solve histogram value
     * @param bool $onlyEnabled filter by current or all coworkers
     *
     * @return string
     */
    public function drawCategoryProgress($items, $divider, $onlyEnabled = false)
    {
        $result = '';
        /** @var Category $item */
        foreach ($items as $item) {
            $amount = 0;
            if ($onlyEnabled) {
                /** @var Coworker $coworker */
                foreach ($item->getCoworkers() as $coworker) {
                    if ($coworker->getEnabled()) {
                        $amount++;
                    }
                }
            } else {
                $amount = count($item->getCoworkers());
            }
            if ($amount) {
                $result .= '<h6 class="box-title">'.$item->getTitle().'</h6>'.
                    '<div class="progress progress-bar-vertical">'.
                        '<div class="progress-bar progress-bar-success" style="width:'.$this->solveAverage($amount, $divider).'%">'.
                            $amount.
                        '</div>'.
                    '</div>'
                ;
            }

        }

        return $result;
    }

    /**
     * Twig Filters.
     */

    /**
     * @return array
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('draw_role_span', array($this, 'drawRoleSpan')),
            new \Twig_SimpleFilter('age', array($this, 'ageCalculate')),
            new \Twig_SimpleFilter('draw_money', array($this, 'drawMoney')),
        );
    }

    /**
     * @param User $object
     *
     * @return string
     */
    public function drawRoleSpan($object)
    {
        $span = '';
        if ($object instanceof User && count($object->getRoles()) > 0) {
            /** @var string $role */
            foreach ($object->getRoles() as $role) {
                if ($role == UserRolesEnum::ROLE_CMS) {
                    $span .= '<span class="label label-warning" style="margin-right:10px">editor</span>';
                } elseif ($role == UserRolesEnum::ROLE_ADMIN) {
                    $span .= '<span class="label label-primary" style="margin-right:10px">administrador</span>';
                } elseif ($role == UserRolesEnum::ROLE_SUPER_ADMIN) {
                    $span .= '<span class="label label-danger" style="margin-right:10px">superadministrador</span>';
                }
            }
        } else {
            $span = '<span class="label label-success" style="margin-right:10px">---</span>';
        }

        return $span;
    }

    /**
     * @param \DateTime $birthday
     *
     * @return int
     */
    public function ageCalculate(\DateTime $birthday)
    {
        $now = new \DateTime();
        $interval = $now->diff($birthday);

        return $interval->y;
    }

    /**
     * @param Invoice $object
     *
     * @return string
     */
    public function drawMoney($object)
    {
        $result = '<span class="text text-info">0,00 €</span>';
        if (is_numeric($object)) {
            if ($object < 0) {
                $result = '<span class="text text-danger">'.number_format($object, 2, ',', '.').' €</span>';
            } elseif ($object > 0) {
                $result = '<span class="text text-success">'.number_format($object, 2, ',', '.').' €</span>';
            } else {
                $result = '<span class="text text-info">0,00 €</span>';
            }
        }

        return $result;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'app_extension';
    }

    /**
     * @param float|int $dividend
     * @param float|int $divider
     *
     * @return float|int
     */
    private function solveAverage($dividend, $divider)
    {
        if ($divider == 0) {
            return 0;
        }

        return round(($dividend / $divider) * 100, 0);
    }
}

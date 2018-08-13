<?php

namespace AppBundle\Twig;

use AppBundle\Entity\Invoice;
use AppBundle\Entity\User;
use AppBundle\Enum\UserRolesEnum;

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
}

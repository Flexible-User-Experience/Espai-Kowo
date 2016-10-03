<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

/**
 * Class CoworkerRepository
 *
 * @category Repository
 * @package  AppBundle\Repository
 * @author   Anton Serra <aserratorta@gmail.com>
 */
class CoworkerRepository extends EntityRepository
{
    /**
     * @param null   $limit
     * @param string $order
     *
     * @return QueryBuilder
     */
    public function findAllEnabledSortedBySurnameQB($limit = null, $order = 'ASC')
    {
        $query = $this
            ->createQueryBuilder('c')
            ->where('c.enabled = :enabled')
            ->setParameter('enabled', true)
            ->orderBy('c.surname', $order);

        if (!is_null($limit)) {
            $query->setMaxResults($limit);
        }

        return $query;
    }

    /**
     * @param null   $limit
     * @param string $order
     *
     * @return Query
     */
    public function findAllEnabledSortedBySurnameQ($limit = null, $order = 'ASC')
    {
        return $this->findAllEnabledSortedBySurnameQB($limit, $order)->getQuery();
    }

    /**
     * @param null   $limit
     * @param string $order
     *
     * @return array
     */
    public function findAllEnabledSortedBySurname($limit = null, $order = 'ASC')
    {
        return $this->findAllEnabledSortedBySurnameQ($limit, $order)->getResult();
    }

    /**
     * @param int $day
     * @param int $month
     *
     * @return QueryBuilder
     */
    public function getAllEnabledCoworkersBirthdayByDayAndMonthQB($day, $month)
    {
        $query = $this->createQueryBuilder('coworker')
            ->where('coworker.enabled = :enabled')
            ->andWhere('DAY(coworker.birthday) = :day')
            ->andWhere('MONTH(coworker.birthday) = :month')
            ->setParameter('enabled', true)
            ->setParameter('day', $day)
            ->setParameter('month', $month);

        return $query;
    }

    /**
     * @param int $day
     * @param int $month
     *
     * @return Query
     */
    public function getAllEnabledCoworkersBirthdayByDayAndMonthQ($day, $month)
    {
        return $this->getAllEnabledCoworkersBirthdayByDayAndMonthQB($day, $month)->getQuery();
    }

    /**
     * @param int $day
     * @param int $month
     *
     * @return array
     */
    public function getAllEnabledCoworkersBirthdayByDayAndMonth($day, $month)
    {
        return $this->getAllEnabledCoworkersBirthdayByDayAndMonthQ($day, $month)->getResult();
    }
}

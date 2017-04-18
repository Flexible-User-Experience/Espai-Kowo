<?php

namespace AppBundle\Repository;

use AppBundle\Enum\GenderEnum;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

/**
 * Class CoworkerRepository.
 *
 * @category Repository
 *
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
    public function getAllCoworkersBirthdayByDayAndMonthQB($day, $month)
    {
        $query = $this->createQueryBuilder('coworker')
            ->where('DAY(coworker.birthday) = :day')
            ->andWhere('MONTH(coworker.birthday) = :month')
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
    public function getAllCoworkersBirthdayByDayAndMonthQ($day, $month)
    {
        return $this->getAllCoworkersBirthdayByDayAndMonthQB($day, $month)->getQuery();
    }

    /**
     * @param int $day
     * @param int $month
     *
     * @return array
     */
    public function getAllCoworkersBirthdayByDayAndMonth($day, $month)
    {
        return $this->getAllCoworkersBirthdayByDayAndMonthQ($day, $month)->getResult();
    }

    /**
     * @return int
     */
    public function getEnabledMaleCoworkersAmount()
    {
        return $this->getEnabledCoworkersAmountByGender(GenderEnum::MALE);
    }

    /**
     * @return int
     */
    public function getEnabledFemaleCoworkersAmount()
    {
        return $this->getEnabledCoworkersAmountByGender(GenderEnum::FEMALE);
    }

    /**
     * @return int
     */
    private function getEnabledCoworkersAmountByGender($gender)
    {
        $qb = $this->findAllEnabledSortedBySurnameQB();
        $qb->andWhere('c.gender = :gender')
            ->setParameter('gender', $gender);

        return count($qb->getQuery()->getResult());
    }

    /**
     * @param $month
     *
     * @return int
     */
    public function getNewCoworkerAmountByMonth($month)
    {
        $qb = $this->createQueryBuilder('coworker')
            ->where('MONTH(coworker.createdAt) = :month')
            ->setParameter('month', $month);

        return count($qb->getQuery()->getResult());
    }
}

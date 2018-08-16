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
     * @param $month
     *
     * @return QueryBuilder
     */
    public function getAllCoworkersBirthdayByMonthQB($month)
    {
        $qb = $this->createQueryBuilder('coworker')
            ->where('MONTH(coworker.birthday) = :month')
            ->setParameter('month', $month)
            ->orderBy('DAY(coworker.birthday)', 'ASC')
        ;

        return $qb;
    }

    /**
     * @param $month
     *
     * @return array
     */
    public function getAllCoworkersBirthdayByMonth($month)
    {
        return $this->getAllCoworkersBirthdayByMonthQB($month)->getQuery()->getResult();
    }

    /**
     * @param int $day
     * @param int $month
     *
     * @return QueryBuilder
     */
    public function getAllCoworkersBirthdayByDayAndMonthQB($day, $month)
    {
        $query = $this->getAllCoworkersBirthdayByMonthQB($month)
            ->andWhere('DAY(coworker.birthday) = :day')
            ->setParameter('day', $day);

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
     * @param int $gender
     *
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
     * @return QueryBuilder
     */
    public function getAllCoworkersAgeListQB()
    {
        $qb = $this->createQueryBuilder('coworker')
            ->select('coworker.id, coworker.enabled, YEAR(coworker.birthday) as cby')
            ->orderBy('cby', 'ASC');

        return $qb;
    }

    /**
     * @return Query
     */
    public function getAllCoworkersAgeListQ()
    {
        return $this->getAllCoworkersAgeListQB()->getQuery();
    }

    /**
     * @return array
     */
    public function getAllCoworkersAgeList()
    {
        return $this->getAllCoworkersAgeListQ()->getScalarResult();
    }

    /**
     * @return array
     */
    public function getCurrentCoworkersAgeList()
    {
        $qb = $this->getAllCoworkersAgeListQB()
            ->where('coworker.enabled = :enabled')
            ->setParameter('enabled', true);

        return $qb->getQuery()->getScalarResult();
    }

    /**
     * @param int $month
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

    /**
     * @param int $month
     *
     * @return int
     */
    public function getDischargeCoworkerAmountByMonth($month)
    {
        $qb = $this->createQueryBuilder('coworker')
            ->where('MONTH(coworker.dischargeDate) = :month')
            ->setParameter('month', $month);

        return count($qb->getQuery()->getResult());
    }
}

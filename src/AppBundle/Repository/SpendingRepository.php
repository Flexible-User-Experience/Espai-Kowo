<?php

namespace AppBundle\Repository;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

/**
 * Class SpendingRepository.
 *
 * @category Repository
 */
class SpendingRepository extends EntityRepository
{
    /**
     * @param \DateTime $begin
     * @param \DateTime $end
     *
     * @return QueryBuilder
     */
    public function getExpensesForIntervalQB(\DateTime $begin, \DateTime $end) {
        $query = $this->createQueryBuilder('s')
            ->select('SUM(s.baseAmount) as amount')
            ->where('s.date >= :begin')
            ->andWhere('s.date <= :end')
            ->setParameter('begin', $begin->format('Y-m-d'))
            ->setParameter('end', $end->format('Y-m-d'))
        ;

        return $query;
    }

    /**
     * @param \DateTime $begin
     * @param \DateTime $end
     *
     * @return Query
     */
    public function getExpensesForIntervalQ(\DateTime $begin, \DateTime $end) {
        return $this->getExpensesForIntervalQB($begin, $end)->getQuery();
    }

    /**
     * @param \DateTime $begin
     * @param \DateTime $end
     *
     * @return null|array|int
     */
    public function getExpensesForInterval(\DateTime $begin, \DateTime $end) {
        return $this->getExpensesForIntervalQ($begin, $end)->getOneOrNullResult(AbstractQuery::HYDRATE_SINGLE_SCALAR);
    }

    /**
     * @param \DateTime $begin
     * @param \DateTime $end
     *
     * @return float
     */
    public function getExpensesAmountForInterval(\DateTime $begin, \DateTime $end) {
        $result = $this->getExpensesForInterval($begin, $end);

        return is_null($result) ? 0 : floatval($result);
    }

    /**
     * @param \DateTime $date
     *
     * @return float
     */
    public function getMonthlyExpensesAmountForDate(\DateTime $date)
    {
        $begin = clone $date;
        $end = clone $date;
        $begin->modify('first day of this month');
        $end->modify('last day of this month');

        return $this->getExpensesAmountForInterval($begin, $end);
    }
}

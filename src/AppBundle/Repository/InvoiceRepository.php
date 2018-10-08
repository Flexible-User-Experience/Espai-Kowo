<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Invoice;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query;

/**
 * Class InvoiceRepository.
 *
 * @category Repository
 */
class InvoiceRepository extends EntityRepository
{
    /**
     * @return QueryBuilder
     */
    public function getLastInvoiceQB()
    {
        $qb = $this
            ->createQueryBuilder('i')
            ->orderBy('i.date', 'DESC')
            ->addOrderBy('i.number', 'DESC')
            ->setMaxResults(1)
        ;

        return $qb;
    }

    /**
     * @return Query
     */
    public function getLastInvoiceQ()
    {
        return $this->getLastInvoiceQB()->getQuery();
    }

    /**
     * @return Invoice|null
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getLastInvoice()
    {
        return $this->getLastInvoiceQ()->getOneOrNullResult();
    }

    /**
     * @param \DateTime $begin
     * @param \DateTime $end
     *
     * @return QueryBuilder
     */
    public function getSalesForIntervalQB(\DateTime $begin, \DateTime $end) {
        $query = $this->createQueryBuilder('i')
            ->select('SUM(i.baseAmount) as amount')
            ->where('i.date >= :begin')
            ->andWhere('i.date <= :end')
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
    public function getSalesForIntervalQ(\DateTime $begin, \DateTime $end) {
        return $this->getSalesForIntervalQB($begin, $end)->getQuery();
    }

    /**
     * @param \DateTime $begin
     * @param \DateTime $end
     *
     * @return null|array|int
     */
    public function getSalesForInterval(\DateTime $begin, \DateTime $end) {
        return $this->getSalesForIntervalQ($begin, $end)->getOneOrNullResult(AbstractQuery::HYDRATE_SINGLE_SCALAR);
    }

    /**
     * @param \DateTime $begin
     * @param \DateTime $end
     *
     * @return float
     */
    public function getSalesAmountForInterval(\DateTime $begin, \DateTime $end) {
        $result = $this->getSalesForInterval($begin, $end);

        return is_null($result) ? 0 : floatval($result);
    }

    /**
     * @param \DateTime $date
     *
     * @return int
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getMonthlySalesAmountForDate(\DateTime $date)
    {
        $begin = clone $date;
        $end = clone $date;
        $begin->modify('first day of this month');
        $end->modify('last day of this month');

        return $this->getSalesAmountForInterval($begin, $end);
    }
}

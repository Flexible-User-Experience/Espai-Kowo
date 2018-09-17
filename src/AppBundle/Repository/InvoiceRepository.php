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
     * @param \DateTime $date
     *
     * @return int
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getMonthlyIncomingsAmountForDate(\DateTime $date)
    {
        $begin = clone $date;
        $end = clone $date;
        $begin->modify('first day of this month');
        $end->modify('last day of this month');
        $query = $this->createQueryBuilder('r')
            ->select('SUM(r.baseAmount) as amount')
            ->where('r.date >= :begin')
            ->andWhere('r.date <= :end')
            ->setParameter('begin', $begin->format('Y-m-d'))
            ->setParameter('end', $end->format('Y-m-d'))
            ->getQuery()
        ;

        return is_null($query->getOneOrNullResult(AbstractQuery::HYDRATE_SINGLE_SCALAR)) ? 0 : floatval($query->getOneOrNullResult(AbstractQuery::HYDRATE_SINGLE_SCALAR));
    }
}

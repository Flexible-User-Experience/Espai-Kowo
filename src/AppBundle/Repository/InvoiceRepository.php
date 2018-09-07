<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Invoice;
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
}

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
    public function findAllEnabledSortedByPositionQB($limit = null, $order = 'ASC')
    {
        $query = $this
            ->createQueryBuilder('c')
            ->where('c.enabled = :enabled')
            ->setParameter('enabled', true)
            ->orderBy('c.position', $order);

        if (!is_null($limit)) {
            $query->setMaxResults($limit);
        }

        return $query;    }

    /**
     * @param null   $limit
     * @param string $order
     *
     * @return Query
     */
    public function findAllEnabledSortedByPositionQ($limit = null, $order = 'ASC')
    {
        return $this->findAllEnabledSortedByPositionQB($limit, $order)->getQuery();
    }

    /**
     * @param null   $limit
     * @param string $order
     *
     * @return array
     */
    public function findAllEnabledSortedByPosition($limit = null, $order = 'ASC')
    {
        return $this->findAllEnabledSortedByPositionQ($limit, $order)->getResult();
    }

    /**
     * @param null   $limit
     * @param string $order
     *
     * @return QueryBuilder
     */
    public function findAllEnabledSortedBySurnameQB($limit = null, $order = 'ASC')
    {
        $query = $this
            ->findAllEnabledSortedByPositionQB($limit, $order)
            ->orderBy('c.surname');

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
}

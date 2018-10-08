<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

/**
 * Class EventCategoryRepository
 *
 * @category Repository
 */
class EventCategoryRepository extends EntityRepository
{
    /**
     * @return QueryBuilder
     */
    public function getAllSortedByTitleQB()
    {
        return $this->createQueryBuilder('c')
            ->orderBy('c.title', 'ASC');
    }

    /**
     * @return array
     */
    public function getAllEnabledSortedByTitle()
    {
        $query = $this->createQueryBuilder('c')
            ->where('c.enabled = :enabled')
            ->setParameter('enabled', true)
            ->orderBy('c.title', 'ASC')
            ->getQuery();

        return $query->getResult();
    }

    /**
     * @return array
     */
    public function getAllEnabledSortedByTitleWithJoin()
    {
        $query = $this->createQueryBuilder('c')
            ->select('c, e')
            ->join('c.events', 'e')
            ->where('c.enabled = :enabled')
            ->setParameter('enabled', true)
            ->orderBy('c.title', 'ASC')
            ->getQuery();

        return $query->getResult();
    }
}

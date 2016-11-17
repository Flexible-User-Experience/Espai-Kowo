<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class EventCategoryRepository
 *
 * @category Repository
 * @package  AppBundle\Repository
 * @author   Anton Serra <aserratorta@gmail.com>
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
     * @return ArrayCollection
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
     * @return ArrayCollection
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

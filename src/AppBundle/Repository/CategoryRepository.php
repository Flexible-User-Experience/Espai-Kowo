<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

/**
 * Class CategoryRepository
 *
 * @category Repository
 */
class CategoryRepository extends EntityRepository
{
    /**
     * @return QueryBuilder
     */
    public function getAllEnabledCategorySortedByTitleQB()
    {
        $query = $this->createQueryBuilder('category')
            ->where('category.enabled = :enabled')
            ->setParameter('enabled', true)
            ->orderBy('category.title', 'ASC');

        return $query;
    }

    /**
     * @return Query
     */
    public function getAllEnabledCategorySortedByTitleQ()
    {
        return $this->getAllEnabledCategorySortedByTitleQB()->getQuery();
    }

    /**
     * @return array
     */
    public function getAllEnabledCategorySortedByTitle()
    {
        return $this->getAllEnabledCategorySortedByTitleQ()->getResult();
    }

    /**
     * @return QueryBuilder
     */
    public function getAllCoworkerCategoryHistogramQB()
    {
        $query = $this->createQueryBuilder('category, coworker, coworker.id AS qty')
            ->join('category.coworkers', 'coworker')
            ->groupBy('qty')
            ->orderBy('category.title', 'ASC');

        return $query;
    }

    /**
     * @return Query
     */
    public function getAllCoworkerCategoryHistogramQ()
    {
        return $this->getAllEnabledCategorySortedByTitleQB()->getQuery();
    }

    /**
     * @return array
     */
    public function getAllCoworkerCategoryHistogram()
    {
        return $this->getAllEnabledCategorySortedByTitleQ()->getResult();
    }

    /**
     * @return QueryBuilder
     */
    public function getCurrentCoworkerCategoryHistogramQB()
    {
        $query = $this->getAllCoworkerCategoryHistogramQB()
            ->where('coworker.enabled = :enabled')
            ->setParameter('enabled', true);

        return $query;
    }

    /**
     * @return Query
     */
    public function getCurrentCoworkerCategoryHistogramQ()
    {
        return $this->getCurrentCoworkerCategoryHistogramQB()->getQuery();
    }

    /**
     * @return array
     */
    public function getCurrentCoworkerCategoryHistogram()
    {
        return $this->getCurrentCoworkerCategoryHistogramQ()->getResult();
    }
}

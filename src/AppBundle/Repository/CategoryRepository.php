<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Category;
use AppBundle\Model\CategoryHistogramHelperModel;
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
    public function getAllCategorySortedByTitleQB()
    {
        $query = $this->createQueryBuilder('category')->orderBy('category.title', 'ASC');

        return $query;
    }

    /**
     * @return Query
     */
    public function getAllCategorySortedByTitleQ()
    {
        return $this->getAllCategorySortedByTitleQB()->getQuery();
    }

    /**
     * @return array
     */
    public function getAllCategorySortedByTitle()
    {
        return $this->getAllCategorySortedByTitleQ()->getResult();
    }

    /**
     * @return QueryBuilder
     */
    public function getAllEnabledCategorySortedByTitleQB()
    {
        $query = $this->getAllCategorySortedByTitleQB()
            ->where('category.enabled = :enabled')
            ->setParameter('enabled', true);

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
}

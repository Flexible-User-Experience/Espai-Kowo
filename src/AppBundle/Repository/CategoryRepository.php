<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

/**
 * Class CategoryRepository
 *
 * @category Repository
 * @package  AppBundle\Repository
 * @author   Anton Serra <aserratorta@gmail.com>
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
}

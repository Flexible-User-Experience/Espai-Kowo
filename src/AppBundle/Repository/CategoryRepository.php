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
     * @param Category $category
     * @param bool $onlyEnabledFilter
     *
     * @return QueryBuilder
     */
    public function getCoworkersAmountByCategoryQB(Category $category, $onlyEnabledFilter = false)
    {
        $query = $this->createQueryBuilder('category')
            ->select('category', 'coworker')
            ->join('category.coworkers', 'coworker')
            ->where('category.id = :id')
            ->setParameter('id', $category->getId());

        if ($onlyEnabledFilter) {
            $query->andWhere('coworker.enabled = :enabled')->setParameter('enabled', true);
        }

        return $query;
    }

    /**
     * @param Category $category
     * @param bool $onlyEnabledFilter
     *
     * @return Query
     */
    public function getCoworkersAmountByCategoryQ(Category $category, $onlyEnabledFilter = false)
    {
        return $this->getCoworkersAmountByCategoryQB($category, $onlyEnabledFilter)->getQuery();
    }

    /**
     * @param Category $category
     * @param bool $onlyEnabledFilter
     *
     * @return int
     */
    public function getCoworkersAmountByCategory(Category $category, $onlyEnabledFilter = false)
    {
        return count($this->getCoworkersAmountByCategoryQ($category, $onlyEnabledFilter)->getResult());
    }

    /**
     * @return array
     */
    public function getAllCoworkerCategoryHistogram()
    {
        $result = array();
        $categories = $this->createQueryBuilder('category')->orderBy('category.title', 'ASC')->getQuery()->getResult();
        /** @var Category $category */
        foreach ($categories as $category) {
            $helper = new CategoryHistogramHelperModel($category->getTitle(), $this->getCoworkersAmountByCategory($category, false));
            $result[] = $helper;
        }

        return $result;
    }

    /**
     * @return array
     */
    public function getCurrentCoworkerCategoryHistogram()
    {
        $result = array();
        $categories = $this->createQueryBuilder('category')->orderBy('category.title', 'ASC')->getQuery()->getResult();
        /** @var Category $category */
        foreach ($categories as $category) {
            $helper = new CategoryHistogramHelperModel($category->getTitle(), $this->getCoworkersAmountByCategory($category, true));
            $result[] = $helper;
        }

        return $result;
    }
}

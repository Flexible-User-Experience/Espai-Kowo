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
//    /**
//     * @return QueryBuilder
//     */
//    public function getAllSortedByTitleQB()
//    {
//        return $this->createQueryBuilder('t')
//            ->orderBy('t.title', 'ASC');
//    }
//
//    /**
//     * @return ArrayCollection
//     */
//    public function getAllEnabledSortedByTitle()
//    {
//        $query = $this->createQueryBuilder('t')
//            ->where('t.enabled = :enabled')
//            ->setParameter('enabled', true)
//            ->orderBy('t.title', 'ASC')
//            ->getQuery();
//
//        return $query->getResult();
//    }
//
//    /**
//     * @return ArrayCollection
//     */
//    public function getAllEnabledSortedByTitleWithJoin()
//    {
//        $query = $this->createQueryBuilder('t')
//            ->select('t, p')
//            ->join('t.posts', 'p')
//            ->where('t.enabled = :enabled')
//            ->setParameter('enabled', true)
//            ->orderBy('t.title', 'ASC')
//            ->getQuery();
//
//        return $query->getResult();
//    }
}

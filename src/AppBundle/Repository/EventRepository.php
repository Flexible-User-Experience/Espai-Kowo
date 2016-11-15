<?php

namespace AppBundle\Repository;

use AppBundle\Entity\EventCategory;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;

/**
 * Class EventRepository
 *
 * @category Repository
 * @package  AppBundle\Repository
 * @author   Anton Serra <aserratorta@gmail.com>
 */
class EventRepository extends EntityRepository
{
    public function findAllEnabledSortedByDate()
    {
        $query = $this->createQueryBuilder('e')
            ->where('e.enabled = :enabled')
            ->setParameter('enabled', true)
            ->orderBy('e.date', 'DESC');

        return $query->getQuery()->getResult();
    }

    /**
     * @param EventCategory $category
     * @return ArrayCollection
     */
    public function getEventsByCategoryEnabledSortedByDateWithJoinUntilNow(EventCategory $category)
    {
        $now = new \DateTime();
        $query = $this->createQueryBuilder('e')
            ->select('e, c')
            ->join('e.categories', 'c')
            ->where('e.enabled = :enabled')
            ->andWhere('c.id = :cid')
            ->andWhere('e.date <= :date')
            ->setParameter('enabled', true)
            ->setParameter('date', $now->format('Y-m-d'))
            ->setParameter('cid', $category->getId())
            ->orderBy('e.date', 'DESC')
            ->addOrderBy('e.title', 'ASC')
            ->getQuery();

        return $query->getResult();
    }

}

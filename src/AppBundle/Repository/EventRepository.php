<?php

namespace AppBundle\Repository;

use AppBundle\Entity\EventCategory;
use Doctrine\ORM\EntityRepository;

/**
 * Class EventRepository
 *
 * @category Repository
 */
class EventRepository extends EntityRepository
{
    /**
     * @return array
     */
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
     *
     * @return array
     */
    public function getEventsByCategoryEnabledSortedByDateWithJoin(EventCategory $category)
    {
        $query = $this->createQueryBuilder('e')
            ->select('e, c')
            ->join('e.categories', 'c')
            ->where('e.enabled = :enabled')
            ->andWhere('c.id = :cid')
            ->setParameter('enabled', true)
            ->setParameter('cid', $category->getId())
            ->orderBy('e.date', 'DESC')
            ->addOrderBy('e.title', 'ASC')
            ->getQuery();

        return $query->getResult();
    }
}

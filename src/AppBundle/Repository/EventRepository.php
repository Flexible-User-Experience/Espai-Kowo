<?php

namespace AppBundle\Repository;

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
}

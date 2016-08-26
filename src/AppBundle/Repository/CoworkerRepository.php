<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Class CoworkerRepository
 *
 * @category Repository
 * @package  AppBundle\Repository
 * @author   Anton Serra <aserratorta@gmail.com>
 */
class CoworkerRepository extends EntityRepository
{
    public function findAllEnabledSortedByPosition()
    {
        $query = $this->createQueryBuilder('c')
            ->where('c.enabled = :enabled')
            ->setParameter('enabled', true)
            ->orderBy('c.position');

        return $query->getQuery()->getResult();
    }

    public function findAllEnabledSortedBySurname()
    {
        $query = $this->createQueryBuilder('c')
            ->where('c.enabled = :enabled')
            ->setParameter('enabled', true)
            ->orderBy('c.surname');

        return $query->getQuery()->getResult();
    }
}

<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Class CategoryRepository
 *
 * @category Repository
 * @package  AppBundle\Repository
 * @author   Anton Serra <aserratorta@gmail.com>
 */
class CategoryRepository extends EntityRepository
{
    public function getAllEnabledCategorySortedByTitle()
    {
        $query = $this->createQueryBuilder('cat')
            ->where('cat.enabled = :enabled')
            ->setParameter('enabled', true)
            ->orderBy('cat.title');

        return $query->getQuery()->getResult();
    }
}

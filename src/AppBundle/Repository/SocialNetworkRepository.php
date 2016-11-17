<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Coworker;
use Doctrine\ORM\EntityRepository;

/**
 * Class SocialNetworkRepository
 *
 * @category Repository
 * @package  AppBundle\Repository
 * @author   Anton Serra <aserratorta@gmail.com>
 */
class SocialNetworkRepository extends EntityRepository
{
    /**
     * @param Coworker $coworker
     *
     * @return array
     */
    public function getCoworkerSocialNetworksSortedByTitle(Coworker $coworker)
    {
        $query = $this->createQueryBuilder('sn')
            ->where('sn.coworker = :coworker')
            ->andWhere('sn.enabled = :enabled')
            ->setParameter('coworker', $coworker)
            ->setParameter('enabled', true)
            ->join('sn.category', 'snc')
            ->orderBy('snc.title');

        return $query->getQuery()->getResult();
    }
}

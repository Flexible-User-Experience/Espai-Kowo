<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Tag;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;

/**
 * Class PostRepository
 *
 * @category Repository
 * @package  AppBundle\Repository
 * @author   Anton Serra <aserratorta@gmail.com>
 */
class PostRepository extends EntityRepository
{
    /**
     * @return ArrayCollection
     */
    public function getAllEnabledSortedByPublishedDate()
    {
        $query = $this->createQueryBuilder('p')
            ->where('p.enabled = :enabled')
            ->setParameter('enabled', true)
            ->orderBy('p.publishedAt', 'DESC')
            ->addOrderBy('p.title', 'ASC')
            ->getQuery();

        return $query->getResult();
    }

    /**
     * @return ArrayCollection
     */
    public function getAllEnabledSortedByPublishedDateWithJoin()
    {
        $query = $this->createQueryBuilder('p')
            ->select('p, t')
            ->join('p.tags', 't')
            ->where('p.enabled = :enabled')
            ->setParameter('enabled', true)
            ->orderBy('p.publishedAt', 'DESC')
            ->addOrderBy('p.title', 'ASC')
            ->getQuery();

        return $query->getResult();
    }

    /**
     * @return ArrayCollection
     */
    public function getAllEnabledSortedByPublishedDateWithJoinUntilNow()
    {
        $now = new \DateTime();
        $query = $this->createQueryBuilder('p')
            ->select('p, t')
            ->join('p.tags', 't')
            ->where('p.enabled = :enabled')
            ->andWhere('p.publishedAt <= :published')
            ->setParameter('enabled', true)
            ->setParameter('published', $now->format('Y-m-d'))
            ->orderBy('p.publishedAt', 'DESC')
            ->addOrderBy('p.title', 'ASC')
            ->getQuery();

        return $query->getResult();
    }

    /**
     * @param Tag $category
     * @return ArrayCollection
     */
    public function getPostsByTagEnabledSortedByPublishedDate(Tag $tag)
    {
        $query = $this->createQueryBuilder('p')
            ->select('p')
            ->join('p.tags', 't')
            ->where('p.enabled = :enabled')
            ->andWhere('t.id = :tid')
            ->setParameter('enabled', true)
            ->setParameter('tid', $tag->getId())
            ->orderBy('p.publishedAt', 'DESC')
            ->addOrderBy('p.title', 'ASC')
            ->getQuery();

        return $query->getResult();
    }

    /**
     * @param Tag $tag
     * @return ArrayCollection
     */
    public function getPostsByTagEnabledSortedByPublishedDateWithJoin(Tag $tag)
    {
        $query = $this->createQueryBuilder('p')
            ->select('p, t')
            ->join('p.categories', 't')
            ->where('p.enabled = :enabled')
            ->andWhere('t.id = :tid')
            ->setParameter('enabled', true)
            ->setParameter('tid', $tag->getId())
            ->orderBy('p.publishedAt', 'DESC')
            ->addOrderBy('p.title', 'ASC')
            ->getQuery();

        return $query->getResult();
    }

    /**
     * @param Tag $tag
     * @return ArrayCollection
     */
    public function getPostsByTagEnabledSortedByPublishedDateWithJoinUntilNow(Tag $tag)
    {
        $now = new \DateTime();
        $query = $this->createQueryBuilder('p')
            ->select('p, t')
            ->join('p.tags', 't')
            ->where('p.enabled = :enabled')
            ->andWhere('t.id = :tid')
            ->andWhere('p.publishedAt <= :published')
            ->setParameter('enabled', true)
            ->setParameter('published', $now->format('Y-m-d'))
            ->setParameter('tid', $tag->getId())
            ->orderBy('p.publishedAt', 'DESC')
            ->addOrderBy('p.title', 'ASC')
            ->getQuery();

        return $query->getResult();
    }
}

<?php

namespace AppBundle\Entity\Traits;

/**
 * Slug trait
 *
 * @category Trait
 * @author   David Romaní <david@flux.cat>
 */
Trait SlugTrait
{
    /**
     * @param string $slug
     *
     * @return $this
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }
}

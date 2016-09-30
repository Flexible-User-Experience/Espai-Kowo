<?php

namespace AppBundle\Manager;

use AppBundle\Repository\CategoryRepository;

/**
 * Class RepositoriesManager
 *
 * @category Manager
 * @package  AppBundle\Manager
 * @author   David Romaní <david@flux.cat>
 */
class RepositoriesManager
{
    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    /**
     *
     *
     * Methods
     *
     *
     */

    /**
     * RepositoriesManager constructor.
     *
     * @param CategoryRepository $categoryRepository
     */
    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @return CategoryRepository
     */
    public function getCategoryRepository()
    {
        return $this->categoryRepository;
    }
}

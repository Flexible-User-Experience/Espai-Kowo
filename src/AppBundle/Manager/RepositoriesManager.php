<?php

namespace AppBundle\Manager;

use AppBundle\Repository\CategoryRepository;
use AppBundle\Repository\ProvinceRepository;

/**
 * Class RepositoriesManager
 *
 * @category Manager
 */
class RepositoriesManager
{
    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    /**
     * @var ProvinceRepository
     */
    private $provinceRepository;

    /**
     * Methods
     */

    /**
     * RepositoriesManager constructor.
     *
     * @param CategoryRepository $categoryRepository
     * @param ProvinceRepository $provinceRepository
     */
    public function __construct(CategoryRepository $categoryRepository, ProvinceRepository $provinceRepository)
    {
        $this->categoryRepository = $categoryRepository;
        $this->provinceRepository = $provinceRepository;
    }

    /**
     * @return CategoryRepository
     */
    public function getCategoryRepository()
    {
        return $this->categoryRepository;
    }

    /**
     * @return ProvinceRepository
     */
    public function getProvinceRepository()
    {
        return $this->provinceRepository;
    }
}

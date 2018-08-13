<?php

namespace AppBundle\Manager;

use AppBundle\Repository\CategoryRepository;
use AppBundle\Repository\CityRepository;
use AppBundle\Repository\CustomerRepository;
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
     * @var CityRepository
     */
    private $cityRepository;

    /**
     * @var CustomerRepository
     */
    private $customerRepository;

    /**
     * Methods
     */

    /**
     * RepositoriesManager constructor.
     *
     * @param CategoryRepository $categoryRepository
     * @param ProvinceRepository $provinceRepository
     * @param CityRepository     $cityRepository
     * @param CustomerRepository $customerRepository
     */
    public function __construct(CategoryRepository $categoryRepository, ProvinceRepository $provinceRepository, CityRepository $cityRepository, CustomerRepository $customerRepository)
    {
        $this->categoryRepository = $categoryRepository;
        $this->provinceRepository = $provinceRepository;
        $this->cityRepository = $cityRepository;
        $this->customerRepository = $customerRepository;
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

    /**
     * @return CityRepository
     */
    public function getCityRepository()
    {
        return $this->cityRepository;
    }

    /**
     * @return CustomerRepository
     */
    public function getCustomerRepository()
    {
        return $this->customerRepository;
    }
}

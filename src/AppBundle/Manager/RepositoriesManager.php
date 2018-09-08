<?php

namespace AppBundle\Manager;

use AppBundle\Repository\CategoryRepository;
use AppBundle\Repository\CityRepository;
use AppBundle\Repository\CustomerRepository;
use AppBundle\Repository\ProviderRepository;
use AppBundle\Repository\ProvinceRepository;
use AppBundle\Repository\SpendingCategoryRepository;

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
     * @var ProviderRepository
     */
    private $providerRepository;

    /**
     * @var SpendingCategoryRepository
     */
    private $spendingCategoryRepository;

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
     * @param ProviderRepository $providerRepository
     * @param SpendingCategoryRepository $spendingCategoryRepository
     */
    public function __construct(CategoryRepository $categoryRepository, ProvinceRepository $provinceRepository, CityRepository $cityRepository, CustomerRepository $customerRepository, ProviderRepository $providerRepository, SpendingCategoryRepository $spendingCategoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
        $this->provinceRepository = $provinceRepository;
        $this->cityRepository = $cityRepository;
        $this->customerRepository = $customerRepository;
        $this->providerRepository = $providerRepository;
        $this->spendingCategoryRepository = $spendingCategoryRepository;
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

    /**
     * @return ProviderRepository
     */
    public function getProviderRepository()
    {
        return $this->providerRepository;
    }

    /**
     * @return SpendingCategoryRepository
     */
    public function getSpendingCategoryRepository()
    {
        return $this->spendingCategoryRepository;
    }
}

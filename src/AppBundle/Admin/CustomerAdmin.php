<?php

namespace AppBundle\Admin;

use AppBundle\Enum\LanguageEnum;
use AppBundle\Enum\PaymentMethodEnum;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

/**
 * Class CustomerAdmin.
 *
 * @category Admin
 */
class CustomerAdmin extends AbstractBaseAdmin
{
    protected $classnameLabel = 'Customer';
    protected $baseRoutePattern = 'invoicing/customer';
    protected $datagridValues = array(
        '_sort_by' => 'name',
        '_sort_order' => 'asc',
    );

    /**
     * Configure route collection.
     *
     * @param RouteCollection $collection
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        parent::configureRoutes($collection);
        $collection->remove('delete');
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('backend.admin.general', $this->getFormMdSuccessBoxArray(6))
            ->add(
                'tic',
                null,
                array(
                    'label' => 'backend.admin.customer.tic',
                    'required' => true,
                )
            )
            ->add(
                'name',
                null,
                array(
                    'label' => 'backend.admin.customer.name',
                    'required' => true,
                )
            )
            ->add(
                'alias',
                null,
                array(
                    'label' => 'backend.admin.customer.alias',
                )
            )
            ->add(
                'address',
                null,
                array(
                    'label' => 'backend.admin.customer.address',
                )
            )
            ->add(
                'city',
                null,
                array(
                    'label' => 'backend.admin.customer.city',
                    'query_builder' => $this->rm->getCityRepository()->getEnabledSortedByNameQB(),
                    'required' => true,
                )
            )
            ->add(
                'phone',
                null,
                array(
                    'label' => 'backend.admin.customer.phone',
                )
            )
            ->add(
                'email',
                null,
                array(
                    'label' => 'backend.admin.customer.email',
                )
            )
            ->end()
            ->with('backend.admin.controls', $this->getFormMdSuccessBoxArray(3))
            ->add(
                'coworkers',
                null,
                array(
                    'label' => 'backend.admin.customer.coworkers',
                    'disabled' => true,
                )
            )
            ->add(
                'invoicesLanguage',
                ChoiceType::class,
                array(
                    'label' => 'backend.admin.customer.invoices_language',
                    'choices' => LanguageEnum::getEnumArray(),
                    'required' => true,
                )
            )
            ->add(
                'paymentMethod',
                ChoiceType::class,
                array(
                    'label' => 'backend.admin.customer.payment_method',
                    'choices' => PaymentMethodEnum::getEnumArray(),
                    'required' => true,
                )
            )
            ->add(
                'isEnterprise',
                CheckboxType::class,
                array(
                    'label' => 'backend.admin.customer.is_enterprise',
                    'required' => false,
                )
            )
            ->add(
                'enabled',
                CheckboxType::class,
                array(
                    'label' => 'backend.admin.enabled',
                    'required' => false,
                )
            )
            ->end();
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add(
                'tic',
                null,
                array(
                    'label' => 'backend.admin.customer.tic',
                )
            )
            ->add(
                'name',
                null,
                array(
                    'label' => 'backend.admin.customer.name',
                )
            )
            ->add(
                'alias',
                null,
                array(
                    'label' => 'backend.admin.customer.alias',
                )
            )
            ->add(
                'address',
                null,
                array(
                    'label' => 'backend.admin.customer.address',
                )
            )
            ->add(
                'city',
                null,
                array(
                    'label' => 'backend.admin.customer.city',
                )
            )
            ->add(
                'phone',
                null,
                array(
                    'label' => 'backend.admin.customer.phone',
                )
            )
            ->add(
                'email',
                null,
                array(
                    'label' => 'backend.admin.customer.email',
                )
            )
            ->add(
                'isEnterprise',
                null,
                array(
                    'label' => 'backend.admin.customer.is_enterprise',
                )
            )
            ->add(
                'invoicesLanguage',
                null,
                array(
                    'label' => 'backend.admin.customer.invoices_language',
                )
            )
            ->add(
                'paymentMethod',
                null,
                array(
                    'label' => 'backend.admin.customer.payment_method',
                )
            )
            ->add(
                'coworkers',
                null,
                array(
                    'label' => 'backend.admin.customer.coworkers',
                )
            )
            ->add(
                'enabled',
                null,
                array(
                    'label' => 'backend.admin.enabled',
                )
            )
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        unset($this->listModes['mosaic']);
        $listMapper
            ->add(
                'tic',
                null,
                array(
                    'label' => 'backend.admin.customer.tic',
                    'editable' => true,
                )
            )
            ->add(
                'name',
                null,
                array(
                    'label' => 'backend.admin.customer.name',
                    'editable' => true,
                )
            )
            ->add(
                'city',
                null,
                array(
                    'label' => 'backend.admin.customer.city',
                    'editable' => true,
                )
            )
            ->add(
                'phone',
                null,
                array(
                    'label' => 'backend.admin.customer.phone',
                    'editable' => true,
                )
            )
            ->add(
                'email',
                null,
                array(
                    'label' => 'backend.admin.customer.email',
                    'editable' => true,
                )
            )
            ->add(
                'isEnterprise',
                null,
                array(
                    'label' => 'backend.admin.customer.is_enterprise',
                    'editable' => true,
                )
            )
            ->add(
                'enabled',
                null,
                array(
                    'label' => 'backend.admin.enabled',
                    'editable' => true,
                )
            )
            ->add(
                '_action',
                'actions',
                array(
                    'label' => 'backend.admin.actions',
                    'actions' => array(
                        'edit' => array('template' => '::Admin/Buttons/list__action_edit_button.html.twig'),
                    ),
                )
            );
    }
}

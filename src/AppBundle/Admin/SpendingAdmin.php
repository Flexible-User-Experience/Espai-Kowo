<?php

namespace AppBundle\Admin;

use AppBundle\Entity\Provider;
use AppBundle\Entity\SpendingCategory;
use AppBundle\Enum\PaymentMethodEnum;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\CoreBundle\Form\Type\DatePickerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

/**
 * Class SpendingAdmin.
 *
 * @category Admin
 */
class SpendingAdmin extends AbstractBaseAdmin
{
    protected $classnameLabel = 'Spending';
    protected $baseRoutePattern = 'purchases/spending';
    protected $datagridValues = array(
        '_sort_by' => 'name',
        '_sort_order' => 'asc',
    );

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('backend.admin.general', $this->getFormMdSuccessBoxArray(5))
            ->add(
                'date',
                DatePickerType::class,
                array(
                    'label' => 'backend.admin.spending.date',
                    'format' => 'd/M/y',
                    'required' => true,

                )
            )
            ->add(
                'category',
                EntityType::class,
                array(
                    'label' => 'backend.admin.spending.category',
                    'required' => false,
                    'class' => SpendingCategory::class,
                    'query_builder' => $this->rm->getSpendingCategoryRepository()->getEnabledSortedByNameQB(),
                )
            )
            ->add(
                'provider',
                EntityType::class,
                array(
                    'label' => 'backend.admin.spending.provider',
                    'required' => true,
                    'class' => Provider::class,
                    'query_builder' => $this->rm->getProviderRepository()->getEnabledSortedByNameQB(),
                )
            )
            ->add(
                'description',
                null,
                array(
                    'label' => 'backend.admin.spending.description',
                    'required' => false,
                )
            )
            ->end()
            ->with('backend.admin.controls', $this->getFormMdSuccessBoxArray(3))
            ->add(
                'baseAmount',
                null,
                array(
                    'label' => 'backend.admin.invoice.baseAmount',
                    'required' => true,
                )
            )
            ->add(
                'isPayed',
                CheckboxType::class,
                array(
                    'label' => 'backend.admin.invoice.isPayed',
                    'required' => false,
                )
            )
            ->add(
                'paymentDate',
                DatePickerType::class,
                array(
                    'label' => 'backend.admin.invoice.paymentDate',
                    'required' => false,
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
            ->end()
        ;
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add(
                'date',
                null,
                array(
                    'label' => 'backend.admin.spending.date',
                    'format' => 'd/M/y',

                )
            )
            ->add(
                'category',
                null,
                array(
                    'label' => 'backend.admin.spending.category',
                )
            )
            ->add(
                'provider',
                null,
                array(
                    'label' => 'backend.admin.spending.provider',
                )
            )
            ->add(
                'description',
                null,
                array(
                    'label' => 'backend.admin.spending.description',
                )
            )
            ->add(
                'baseAmount',
                null,
                array(
                    'label' => 'backend.admin.invoice.baseAmount',
                )
            )
            ->add(
                'isPayed',
                null,
                array(
                    'label' => 'backend.admin.invoice.isPayed',
                )
            )
            ->add(
                'paymentDate',
                null,
                array(
                    'label' => 'backend.admin.invoice.paymentDate',
                )
            )
            ->add(
                'paymentMethod',
                null,
                array(
                    'label' => 'backend.admin.customer.payment_method',
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
                'date',
                null,
                array(
                    'label' => 'backend.admin.spending.date',
                    'format' => 'd/m/Y',
                    'editable' => true,
                )
            )
            ->add(
                'category',
                null,
                array(
                    'label' => 'backend.admin.spending.category',
                    'editable' => false,
                    'associated_property' => 'name',
                    'sortable' => true,
                    'sort_field_mapping' => array('fieldName' => 'name'),
                    'sort_parent_association_mappings' => array(array('fieldName' => 'category')),
                )
            )
            ->add(
                'provider',
                null,
                array(
                    'label' => 'backend.admin.spending.provider',
                    'editable' => false,
                    'associated_property' => 'name',
                    'sortable' => true,
                    'sort_field_mapping' => array('fieldName' => 'name'),
                    'sort_parent_association_mappings' => array(array('fieldName' => 'category')),
                )
            )
            ->add(
                'description',
                null,
                array(
                    'label' => 'backend.admin.spending.description',
                    'editable' => true,
                )
            )
            ->add(
                'isPayed',
                null,
                array(
                    'label' => 'backend.admin.invoice.isPayed',
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
                        'delete' => array('template' => '::Admin/Buttons/list__action_delete_button.html.twig'),
                    ),
                )
            )
        ;
    }
}

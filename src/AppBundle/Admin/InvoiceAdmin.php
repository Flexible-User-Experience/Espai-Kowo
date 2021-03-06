<?php

namespace AppBundle\Admin;

use AppBundle\Entity\Customer;
use AppBundle\Entity\Invoice;
use AppBundle\Enum\PaymentMethodEnum;
use AppBundle\Enum\YearEnum;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\Form\Type\DatePickerType;
use Sonata\Form\Type\CollectionType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

/**
 * Class InvoiceAdmin.
 *
 * @category Admin
 */
class InvoiceAdmin extends AbstractBaseAdmin
{
    protected $classnameLabel = 'Invoice';
    protected $baseRoutePattern = 'sales/invoice';
    protected $datagridValues = array(
        '_sort_by' => 'id',
        '_sort_order' => 'desc',
    );

    /**
     * Configure route collection.
     *
     * @param RouteCollection $collection
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection
            ->add('pdf', $this->getRouterIdParameter().'/pdf')
            ->add('send', $this->getRouterIdParameter().'/send')
            ->add('xml', $this->getRouterIdParameter().'/xml')
            ->add('duplicate', $this->getRouterIdParameter().'/duplicate')
            ->remove('show')
            ->remove('delete');
    }

    /**
     * @param array $actions
     *
     * @return array
     */
    public function configureBatchActions($actions)
    {
        if ($this->hasRoute('edit') && $this->hasAccess('edit')) {
            $actions['generatesepaxmls'] = array(
                'label' => 'backend.admin.invoice.batch_action',
                'translation_domain' => 'messages',
                'ask_confirmation' => false,
            );
        }

        return $actions;
    }

    /**
     * @param FormMapper $formMapper
     * @throws \Exception
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $now = new \DateTime();
        $currentYear = $now->format('Y');

        $formMapper
            ->with('backend.admin.invoice.invoice', $this->getFormMdSuccessBoxArray(3))
            ->add(
                'number',
                IntegerType::class,
                array(
                    'label' => 'backend.admin.invoice.number',
                    'required' => $this->id($this->getSubject()) ? false : true,
                    'disabled' => $this->id($this->getSubject()) ? true : false,
                )
            )
            ->add(
                'year',
                ChoiceType::class,
                array(
                    'label' => 'backend.admin.invoice.year',
                    'choices' => YearEnum::getYearEnumArray(),
                    'preferred_choices' => $currentYear,
                    'required' => $this->id($this->getSubject()) ? false : true,
                    'disabled' => $this->id($this->getSubject()) ? true : false,
                )
            )
            ->add(
                'date',
                DatePickerType::class,
                array(
                    'label' => 'backend.admin.invoice.date',
                    'format' => 'd/M/y',
                    'required' => true,
                    'disabled' => false,
                )
            )
            ->add(
                'customer',
                EntityType::class,
                array(
                    'label' => 'backend.admin.invoice.customer',
                    'required' => true,
                    'class' => Customer::class,
                    'choice_label' => 'alias',
                    'query_builder' => $this->rm->getCustomerRepository()->getEnabledSortedByNameQB(),
                )
            )
            ->end()
            ->with('backend.admin.invoice.detail', $this->getFormMdSuccessBoxArray(3))
            ->add(
                'taxPercentage',
                NumberType::class,
                array(
                    'label' => 'backend.admin.invoice.taxPercentage',
                    'required' => false,
                    'help' => '%',
                )
            )
            ->add(
                'irpfPercentage',
                NumberType::class,
                array(
                    'label' => 'backend.admin.invoice.irpfPercentage',
                    'required' => false,
                    'help' => '%',
                )
            )
            ->add(
                'baseAmount',
                NumberType::class,
                array(
                    'label' => 'backend.admin.invoice.baseAmount',
                    'required' => false,
                    'disabled' => true,
                )
            )
            ->add(
                'totalAmount',
                NumberType::class,
                array(
                    'label' => 'backend.admin.invoice.totalAmount',
                    'required' => false,
                    'disabled' => true,
                )
            )
            ->end()
            ->with('backend.admin.controls', $this->getFormMdSuccessBoxArray(3))
            ->add(
                'isSended',
                CheckboxType::class,
                array(
                    'label' => 'backend.admin.invoice.isSended',
                    'required' => false,
                    'disabled' => true,
                )
            )
            ->add(
                'sendDate',
                DatePickerType::class,
                array(
                    'label' => 'backend.admin.invoice.sendDate',
                    'format' => 'd/M/y',
                    'required' => false,
                    'disabled' => true,
                )
            )
            ->add(
                'isSepaXmlGenerated',
                CheckboxType::class,
                array(
                    'label' => 'backend.admin.invoice.isSepaXmlGenerated',
                    'required' => false,
                    'disabled' => true,
                )
            )
            ->add(
                'sepaXmlGenerationDate',
                DatePickerType::class,
                array(
                    'label' => 'backend.admin.invoice.sepaXmlGenerationDate',
                    'format' => 'd/M/y',
                    'required' => false,
                    'disabled' => true,
                )
            )
            ->add(
                'isPayed',
                CheckboxType::class,
                array(
                    'label' => 'backend.admin.invoice.isPayed',
                    'required' => false,
                    'disabled' => false,
                )
            )
            ->add(
                'paymentDate',
                DatePickerType::class,
                array(
                    'label' => 'backend.admin.invoice.paymentDate',
                    'format' => 'd/M/y',
                    'required' => false,
                    'disabled' => false,
                )
            );
        if ($this->id($this->getSubject())) { // is edit mode
            /** @var Customer $customer */
            $customer = $this->getSubject()->getCustomer();
            $formMapper
                ->add(
                    'paymentMethod',
                    ChoiceType::class,
                    array(
                        'label' => 'backend.admin.customer.payment_method',
                        'choices' => PaymentMethodEnum::getEnumArray(),
                        'preferred_choices' => array($customer->getPaymentMethod()),
                        'required' => true,
                    )
                )
            ;
        } else {
            $formMapper
                ->add(
                    'paymentMethod',
                    ChoiceType::class,
                    array(
                        'label' => 'backend.admin.customer.payment_method',
                        'choices' => PaymentMethodEnum::getEnumArray(),
                        'empty_data' => PaymentMethodEnum::BANK_DRAFT,
                        'preferred_choices' => array(PaymentMethodEnum::BANK_DRAFT),
                        'required' => true,
                    )
                )
            ;
        }
        $formMapper->end();
        if ($this->id($this->getSubject())) { // is edit mode, disable on new subjetcs
            $formMapper
                ->with('backend.admin.invoice.lines', $this->getFormMdSuccessBoxArray(12))
                ->add(
                    'lines',
                    CollectionType::class,
                    array(
                        'label' => 'backend.admin.invoice.line',
                        'required' => true,
                        'cascade_validation' => true,
                        'error_bubbling' => true,
                        'by_reference' => false,
                    ),
                    array(
                        'edit' => 'inline',
                        'inline' => 'table',
                    )
                )
                ->end()
            ;
        }
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add(
                'number',
                null,
                array(
                    'label' => 'backend.admin.invoice.number',
                )
            )
            ->add(
                'year',
                null,
                array(
                    'label' => 'backend.admin.invoice.year',
                )
            )
            ->add(
                'date',
                null,
                array(
                    'label' => 'backend.admin.invoice.date',
                )
            )
            ->add(
                'customer',
                null,
                array(
                    'label' => 'backend.admin.invoice.customer',
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
                'taxPercentage',
                null,
                array(
                    'label' => 'backend.admin.invoice.taxPercentage',
                )
            )
            ->add(
                'irpfPercentage',
                null,
                array(
                    'label' => 'backend.admin.invoice.irpfPercentage',
                )
            )
            ->add(
                'totalAmount',
                null,
                array(
                    'label' => 'backend.admin.invoice.totalAmount',
                )
            )
            ->add(
                'isSended',
                null,
                array(
                    'label' => 'backend.admin.invoice.isSended',
                )
            )
            ->add(
                'sendDate',
                'doctrine_orm_date',
                array(
                    'label' => 'backend.admin.invoice.sendDate',
                    'field_type' => DatePickerType::class,
                    'format' => 'd-m-Y',
                )
            )
            ->add(
                'isSepaXmlGenerated',
                null,
                array(
                    'label' => 'backend.admin.invoice.isSepaXmlGenerated',
                )
            )
            ->add(
                'sepaXmlGenerationDate',
                'doctrine_orm_date',
                array(
                    'label' => 'backend.admin.invoice.sepaXmlGenerationDate',
                    'field_type' => DatePickerType::class,
                    'format' => 'd-m-Y',
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
                'doctrine_orm_date',
                array(
                    'label' => 'backend.admin.invoice.paymentDate',
                    'field_type' => DatePickerType::class,
                    'format' => 'd-m-Y',
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
                'id',
                null,
                array(
                    'label' => 'backend.admin.invoice.id',
                    'template' => '::Admin/Cells/list__cell_invoice_number.html.twig',
                )
            )
            ->add(
                'date',
                null,
                array(
                    'label' => 'backend.admin.invoice.date',
                    'template' => '::Admin/Cells/list__cell_invoice_date.html.twig',
                    'editable' => true,
                )
            )
            ->add(
                'customer',
                null,
                array(
                    'label' => 'backend.admin.invoice.customer',
                    'editable' => false,
                    'associated_property' => 'alias',
                    'sortable' => true,
                    'sort_field_mapping' => array('fieldName' => 'alias'),
                    'sort_parent_association_mappings' => array(array('fieldName' => 'customer')),
                )
            )
            ->add(
                'baseAmount',
                null,
                array(
                    'label' => 'backend.admin.invoice.baseAmount',
                    'template' => '::Admin/Cells/list__cell_invoice_base_amount.html.twig',
                    'editable' => false,
                )
            )
            ->add(
                'totalAmount',
                null,
                array(
                    'label' => 'backend.admin.invoice.totalAmount',
                    'template' => '::Admin/Cells/list__cell_invoice_total_amount.html.twig',
                    'editable' => false,
                )
            )
            ->add(
                'isSended',
                null,
                array(
                    'label' => 'backend.admin.invoice.isSended',
                    'editable' => false,
                )
            )
            ->add(
                'isSepaXmlGenerated',
                null,
                array(
                    'label' => 'backend.admin.invoice.isSepaXmlGenerated',
                    'editable' => false,
                )
            )
            ->add(
                'isPayed',
                null,
                array(
                    'label' => 'backend.admin.invoice.isPayed',
                    'editable' => false,
                )
            )
            ->add(
                '_action',
                'actions',
                array(
                    'actions' => array(
                        'edit' => array('template' => '::Admin/Buttons/list__action_edit_button.html.twig'),
                        'pdf' => array('template' => '::Admin/Buttons/list__action_invoice_direct_download_pdf_button.html.twig'),
                        'send' => array('template' => '::Admin/Buttons/list__action_invoice_send_button.html.twig'),
                        'xml' => array('template' => '::Admin/Buttons/list__action_invoice_xml_button.html.twig'),
                        'duplicate' => array('template' => '::Admin/Buttons/list__action_invoice_duplicate_button.html.twig'),
                    ),
                    'label' => 'backend.admin.actions',
                )
            );
    }

    /**
     * @param Invoice $object
     */
    public function prePersist($object)
    {
        $this->commonPreActions($object);
    }

    /**
     * @param Invoice $object
     */
    public function preUpdate($object)
    {
        $this->commonPreActions($object);
    }

    /**
     * @param Invoice $object
     */
    private function commonPreActions($object)
    {
        $object
            ->setBaseAmount($object->calculateBaseAmount())
            ->setTotalAmount($object->calculateTotalAmount())
        ;
    }
}

<?php

namespace AppBundle\Admin;

use AppBundle\Enum\BookCodeEnum;
use AppBundle\Enum\GenderEnum;
use AppBundle\Enum\TicketOfficeCodeEnum;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\CoreBundle\Form\Type\DatePickerType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

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
                'address',
                null,
                array(
                    'label' => 'backend.admin.customer.address',
                )
            )
            ->add(
                'email',
                null,
                array(
                    'label' => 'backend.admin.coworker.email',
                    'required' => true,
                )
            )
            ->add(
                'description',
                CKEditorType::class,
                array(
                    'label' => 'backend.admin.coworker.description',
                    'config_name' => 'my_config',
                    'required' => true,
                )
            )
            ->end()
            ->with('backend.admin.pictures', $this->getFormMdSuccessBoxArray(3))
            ->add(
                'imageFile',
                FileType::class,
                array(
                    'label' => 'backend.admin.post.image',
                    'help' => $this->getImageHelperFormMapperWithThumbnail(),
                    'required' => false,
                )
            )
//            ->add(
//                'imageFileBW',
//                'file',
//                array(
//                    'label' => 'backend.admin.post.imageBW',
//                    'help' => $this->getImageHelperFormMapperWithThumbnailBW(),
//                    'required' => false,
//                )
//            )
            ->add(
                'gifFile',
                FileType::class,
                array(
                    'label' => 'backend.admin.coworker.gif',
                    'help' => $this->getImageHelperFormMapperWithThumbnailGif(),
                    'required' => false,
                )
            )
            ->end()
            ->with('backend.admin.controls', $this->getFormMdSuccessBoxArray(3))
            ->add(
                'category',
                null,
                array(
                    'label' => 'backend.admin.category.category',
                    'query_builder' => $this->rm->getCategoryRepository()->getAllEnabledCategorySortedByTitleQB(),
                )
            )
            ->add(
                'gender',
                ChoiceType::class,
                array(
                    'label' => 'backend.admin.coworker.gender',
                    'choices' => GenderEnum::getEnumArray(),
                    'multiple' => false,
                    'expanded' => false,
                    'required' => true,
                )
            )
            ->add(
                'printerCode',
                null,
                array(
                    'label' => 'backend.admin.coworker.printerCode',
                )
            )
            ->add(
                'bookCode',
                ChoiceType::class,
                array(
                    'label' => 'backend.admin.coworker.bookCode',
                    'choices' => BookCodeEnum::getEnumArray(),
                    'multiple' => false,
                    'expanded' => false,
                    'required' => false,
                )
            )
            ->add(
                'ticketOfficeCode',
                ChoiceType::class,
                array(
                    'label' => 'backend.admin.coworker.ticketOfficeCode',
                    'choices' => TicketOfficeCodeEnum::getEnumArray(),
                    'multiple' => false,
                    'expanded' => false,
                    'required' => false,
                )
            )
            ->add(
                'birthday',
                DatePickerType::class,
                array(
                    'label' => 'Aniversari',
                    'format' => 'd/M/y',
                    'required' => false,
                )
            )
            ->add(
                'dischargeDate',
                DatePickerType::class,
                array(
                    'label' => 'Data baixa',
                    'format' => 'd/M/y',
                    'required' => false,
                )
            )
            ->add(
                'hideEmailFromWebpage',
                CheckboxType::class,
                array(
                    'label' => 'backend.admin.coworker.hide_email_from_webpage',
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
                        'delete' => array('template' => '::Admin/Buttons/list__action_delete_button.html.twig'),
                    ),
                )
            );
    }
}

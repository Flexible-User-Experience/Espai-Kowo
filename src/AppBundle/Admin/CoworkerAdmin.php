<?php

namespace AppBundle\Admin;

use AppBundle\Entity\Coworker;
use AppBundle\Enum\BookCodeEnum;
use AppBundle\Enum\GenderEnum;
use AppBundle\Enum\TicketOfficeCodeEnum;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\Form\Type\DatePickerType;
use Sonata\Form\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

/**
 * Class CoworkerAdmin.
 *
 * @category Admin
 */
class CoworkerAdmin extends AbstractBaseAdmin
{
    protected $classnameLabel = 'Coworker';
    protected $baseRoutePattern = 'coworkers/coworker';
    protected $datagridValues = array(
        '_sort_by' => 'surname',
        '_sort_order' => 'asc',
    );

    /**
     * Configure route collection.
     *
     * @param RouteCollection $collection
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection
            ->add('data', $this->getRouterIdParameter().'/data')
            ->remove('delete')
            ->remove('batch');
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('backend.admin.general', $this->getFormMdSuccessBoxArray(6))
            ->add(
                'name',
                null,
                array(
                    'label' => 'backend.admin.coworker.name',
                )
            )
            ->add(
                'surname',
                null,
                array(
                    'label' => 'backend.admin.coworker.surname',
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
                'customer',
                null,
                array(
                    'label' => 'backend.admin.coworker.customer',
                    'query_builder' => $this->rm->getCustomerRepository()->getEnabledSortedByNameQB(),
                )
            )
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
                'showInWebpage',
                CheckboxType::class,
                array(
                    'label' => 'backend.admin.coworker.show_in_webpage',
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
        if ($this->id($this->getSubject())) { // is edit mode, disable on new subjects
            $formMapper
                ->with('backend.admin.social_networks.social_networks', $this->getFormMdSuccessBoxArray(12))
                ->add(
                    'socialNetworks',
                    CollectionType::class,
                    array(
                        'label' => ' ',
                        'required' => false,
                        'cascade_validation' => true,
                    ),
                    array(
                        'edit' => 'inline',
                        'inline' => 'table',
                        'sortable' => 'position',
                    )
                )
                ->end();
        }
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add(
                'name',
                null,
                array(
                    'label' => 'backend.admin.coworker.name',
                )
            )
            ->add(
                'surname',
                null,
                array(
                    'label' => 'backend.admin.coworker.surname',
                )
            )
            ->add(
                'gender',
                'doctrine_orm_choice',
                array(
                    'label' => 'backend.admin.coworker.gender',
                    'field_type' => 'choice',
                    'field_options' => array(
                        'choices' => GenderEnum::getEnumArray(),
                    ),
                )
            )
            ->add(
                'email',
                null,
                array(
                    'label' => 'backend.admin.coworker.email',
                )
            )
            ->add(
                'category',
                null,
                array(
                    'label' => 'backend.admin.category.category',
                )
            )
            ->add(
                'customer',
                null,
                array(
                    'label' => 'backend.admin.coworker.customer',
                )
            )
            ->add(
                'hideEmailFromWebpage',
                null,
                array(
                    'label' => 'backend.admin.coworker.hide_email_from_webpage',
                )
            )
            ->add(
                'showInWebpage',
                null,
                array(
                    'label' => 'backend.admin.coworker.show_in_webpage',
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
                'image',
                null,
                array(
                    'label' => 'backend.admin.event.image',
                    'template' => '::Admin/Cells/list__cell_image_field.html.twig',
                )
            )
            ->add(
                'name',
                null,
                array(
                    'label' => 'backend.admin.coworker.name',
                    'editable' => true,
                )
            )
            ->add(
                'surname',
                null,
                array(
                    'label' => 'backend.admin.coworker.surname',
                    'editable' => true,
                )
            )
            ->add(
                'email',
                null,
                array(
                    'label' => 'backend.admin.coworker.email',
                    'editable' => true,
                )
            )
            ->add(
                'customer',
                null,
                array(
                    'label' => 'backend.admin.coworker.customer',
                )
            )
            ->add(
                'hideEmailFromWebpage',
                null,
                array(
                    'label' => 'backend.admin.coworker.hide_email_from_webpage',
                    'editable' => true,
                )
            )
            ->add(
                'showInWebpage',
                null,
                array(
                    'label' => 'backend.admin.coworker.show_in_webpage',
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
                        'show' => array('template' => '::Admin/Buttons/list__action_show_button.html.twig'),
                        'data' => array('template' => '::Admin/Buttons/list__action_data_button.html.twig'),
                        'edit' => array('template' => '::Admin/Buttons/list__action_edit_button.html.twig'),
                    ),
                )
            );
    }

    /**
     * @param Coworker $coworker
     */
    public function prePersist($coworker)
    {
        $coworker->setToken(md5(uniqid(rand(), true)));
    }
}

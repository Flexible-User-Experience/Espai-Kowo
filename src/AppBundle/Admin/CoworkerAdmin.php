<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;

/**
 * Class CoworkerAdmin
 *
 * @category Admin
 * @package  AppBundle\Admin
 * @author   Anton Serra <aserratorta@gmail.com>
 */
class CoworkerAdmin extends AbstractBaseAdmin
{
    protected $classnameLabel = 'Coworker';
    protected $baseRoutePattern = 'coworkers/coworker';
    protected $datagridValues = array(
        '_sort_by'    => 'name',
        '_sort_order' => 'desc',
    );

    /**
     * Configure route collection
     *
     * @param RouteCollection $collection
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection
            ->remove('batch');
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('backend.admin.general', $this->getFormMdSuccessBoxArray(7))
            ->add(
                'name',
                null,
                array(
                    'label' => 'backend.admin.coworker.name',
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
                'description',
                null,
                array(
                    'label' => 'backend.admin.coworker.description',
                )
            )
            ->add(
                'imageFile',
                'file',
                array(
                    'label'    => 'backend.admin.post.image',
                    'help'     => $this->getImageHelperFormMapperWithThumbnail(),
                    'required' => false,
                )
            )
            ->end()
            ->with('backend.admin.controls', $this->getFormMdSuccessBoxArray(5))
            ->add(
                'category',
                null,
                array(
                    'label' => 'backend.admin.category.category',
                )
            )
            ->add(
                'position',
                null,
                array(
                    'label' => 'backend.admin.coworker.position',
                )
            )
            ->add(
                'enabled',
                'checkbox',
                array(
                    'label'    => 'backend.admin.enabled',
                    'required' => false,
                )
            )
            ->end()
            ->with('backend.admin.coworker.social_networks', $this->getFormMdSuccessBoxArray(5))
            ->add(
                'facebook',
                null,
                array(
                    'label' => 'backend.admin.coworker.facebook',
                )
            )
            ->add(
                'twitter',
                null,
                array(
                    'label' => 'backend.admin.coworker.twitter',
                )
            )
            ->add(
                'github',
                null,
                array(
                    'label' => 'backend.admin.coworker.github',
                )
            )
            ->add(
                'linkedin',
                null,
                array(
                    'label' => 'backend.admin.coworker.linkedin',
                )
            )
            ->add(
                'blog',
                null,
                array(
                    'label' => 'backend.admin.coworker.blog',
                )
            )
            ->add(
                'web',
                null,
                array(
                    'label' => 'backend.admin.coworker.web',
                )
            )
            ->add(
                'instagram',
                null,
                array(
                    'label' => 'backend.admin.coworker.instagram',
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
                'name',
                null,
                array(
                    'label' => 'backend.admin.coworker.name',
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
                'enabled',
                null,
                array(
                    'label'    => 'backend.admin.enabled',
                    'editable' => true,
                )
            );
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
                    'label'    => 'backend.admin.event.image',
                    'template' => '::Admin/Cells/list__cell_image_field.html.twig'
                )
            )
            ->add(
                'position',
                null,
                array(
                    'label' => 'backend.admin.coworker.position',
                    'editable' => true,
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
                'email',
                null,
                array(
                    'label' => 'backend.admin.coworker.email',
                    'editable' => true,
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
                    'label'   => 'backend.admin.actions',
                    'actions' => array(
                        'edit'   => array('template' => '::Admin/Buttons/list__action_edit_button.html.twig'),
                        'delete' => array('template' => '::Admin/Buttons/list__action_delete_button.html.twig'),
                    )
                )
            );
    }
}

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
            ->remove('create')
            ->remove('edit')
            ->remove('delete')
            ->remove('batch');
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
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
            );
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
                    'label' => 'backend.admin.cart.customer.name',
                )
            )
            ->add(
                'email',
                null,
                array(
                    'label' => 'backend.admin.cart.customer.address',
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
                'name',
                null,
                array(
                    'label' => 'backend.admin.cart.customer.name',
                )
            )
            ->add(
                'email',
                null,
                array(
                    'label' => 'backend.admin.cart.customer.address',
                )
            )
            ->add(
                '_action',
                'actions',
                array(
                    'label'   => 'backend.admin.actions',
                    'actions' => array(
                        'show' => array(),
                    ),
                )
            );
    }
}

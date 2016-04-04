<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;

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
    protected $baseRoutePattern = 'coworker/details';
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
                'address',
                null,
                array(
                    'label' => 'backend.admin.cart.customer.address',
                )
            )
            ->add(
                'postalCode',
                null,
                array(
                    'label' => 'backend.admin.cart.customer.postal_code',
                )
            )
            ->add(
                'city',
                null,
                array(
                    'label' => 'backend.admin.cart.customer.city',
                )
            )
            ->add(
                'state',
                null,
                array(
                    'label' => 'backend.admin.cart.customer.state',
                )
            )
            ->add(
                'country',
                null,
                array(
                    'label' => 'backend.admin.cart.customer.country',
                )
            )
            ->add(
                'phone',
                null,
                array(
                    'label' => 'backend.admin.cart.customer.phone',
                )
            )
            ->add(
                'email',
                null,
                array(
                    'label' => 'backend.admin.cart.customer.email',
                )
            )
            ->add(
                '_action',
                'actions',
                array(
                    'label'   => 'backend.admin.actions',
                    'actions' => array(
                        'show' => array('template' => '::Admin/Buttons/list__action_show_button.html.twig'),
                    ),
                )
            );
    }

    protected function configureShowFields(ShowMapper $show)
    {
        $show
            ->with('backend.admin.general', $this->getFormMdSuccessBoxArray(6))
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
                    'label' => 'backend.admin.cart.customer.email',
                )
            )
            ->end()
            ->with('backend.admin.cart.customer.carts', $this->getFormMdSuccessBoxArray(6))
            ->add(
                'carts',
                null,
                array(
                    'label' => 'backend.admin.cart.customer.carts',
                )
            )
            ->end()
        ;
    }
}

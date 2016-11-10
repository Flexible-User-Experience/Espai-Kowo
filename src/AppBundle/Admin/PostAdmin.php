<?php

namespace AppBundle\Admin;

use AppBundle\Repository\TagRepository;
use Doctrine\ORM\QueryBuilder;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;

/**
 * Class PostAdmin
 *
 * @category Admin
 * @package  AppBundle\Admin
 * @author   David Romaní <david@flux.cat>
 */
class PostAdmin extends AbstractBaseAdmin
{
    protected $classnameLabel = 'Article';
    protected $baseRoutePattern = 'web/post';
    protected $datagridValues = array(
        '_sort_by'    => 'publishedAt',
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
     * Override query list to reduce queries amount on list view (apply join strategy)
     *
     * @param string $context context
     *
     * @return QueryBuilder
     */
    public function createQuery($context = 'list')
    {
        /** @var QueryBuilder $query */
        $query = parent::createQuery($context);
        $query
            ->select($query->getRootAliases()[0] . ', t')
            ->leftJoin($query->getRootAliases()[0] . '.tags', 't');

        return $query;
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('backend.admin.post.post', $this->getFormMdSuccessBoxArray(6))
            ->add(
                'publishedAt',
                'sonata_type_date_picker',
                array(
                    'label'    => 'backend.admin.published_date',
                    'format'   => 'd/M/y',
                    'required' => true,
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
            ->with('backend.admin.controls', $this->getFormMdSuccessBoxArray(6))
            ->add(
                'tags',
                null,
                array(
                    'label' => 'backend.admin.post.tags',
                    'query_builder' => function (TagRepository $repository) {
                        return $repository->getAllSortedByTitleQB();
                    },
                )
            )
            ->add(
                'metaKeywords',
                null,
                array(
                    'label' => 'backend.admin.post.metakeywords',
                    'help'  => 'backend.admin.post.metakeywordshelp',
                )
            )
            ->add(
                'metaDescription',
                null,
                array(
                    'label' => 'backend.admin.post.metadescription',
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
            ->with('backend.admin.post.content', $this->getFormMdSuccessBoxArray(12))
            ->add(
                'title',
                null,
                array(
                    'label' => 'backend.admin.post.title',
                )
            )
            ->add(
                'shortDescription',
                null,
                array(
                    'label' => 'backend.admin.post.shortdescription',
                )
            )
            ->add(
                'description',
                CKEditorType::class,
                array(
                    'label'       => 'backend.admin.post.description',
                    'config_name' => 'my_config',
                    'required'    => true,
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
                'publishedAt',
                'doctrine_orm_date',
                array(
                    'label'      => 'backend.admin.published_date',
                    'field_type' => 'sonata_type_date_picker',
                )
            )
            ->add(
                'title',
                null,
                array(
                    'label' => 'backend.admin.post.title',
                )
            )
            ->add(
                'tags',
                null,
                array(
                    'label' => 'backend.admin.post.tags',
                )
            )
            ->add(
                'description',
                null,
                array(
                    'label' => 'backend.admin.post.description',
                )
            )
            ->add(
                'metaKeywords',
                null,
                array(
                    'label' => 'backend.admin.post.metakeywords',
                )
            )
            ->add(
                'metaDescription',
                null,
                array(
                    'label' => 'backend.admin.post.metadescription',
                )
            )
            ->add(
                'enabled',
                null,
                array(
                    'label' => 'backend.admin.enabled',
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
                    'label'    => 'backend.admin.post.image',
                    'template' => '::Admin/Cells/list__cell_image_field.html.twig'
                )
            )
            ->add(
                'publishedAt',
                'date',
                array(
                    'label'    => 'backend.admin.published_date',
                    'format'   => 'd/m/Y',
                    'editable' => true,
                )
            )
            ->add(
                'title',
                null,
                array(
                    'label'    => 'backend.admin.post.title',
                    'editable' => true,
                )
            )
            ->add(
                'tags',
                null,
                array(
                    'label'    => 'backend.admin.post.tags',
                    'editable' => true,
                )
            )
            ->add(
                'enabled',
                null,
                array(
                    'label'    => 'backend.admin.enabled',
                    'editable' => true,
                )
            )
            ->add(
                '_action',
                'actions',
                array(
                    'actions' => array(
                        'show'   => array(
                            'template' => '::Admin/Buttons/list__action_show_button.html.twig'
                        ),
                        'edit'   => array('template' => '::Admin/Buttons/list__action_edit_button.html.twig'),
                        'delete' => array('template' => '::Admin/Buttons/list__action_delete_button.html.twig'),
                    ),
                    'label'   => 'backend.admin.actions',
                )
            );
    }
}

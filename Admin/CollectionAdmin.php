<?php

namespace Ok99\PrivateZoneCore\ClassificationBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class CollectionAdmin extends \Sonata\ClassificationBundle\Admin\CollectionAdmin
{
    protected $listModes = array(
        'tree' => array(
            'class' => 'fa fa-list fa-fw',
        ),
    );

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('enabled', null, array('required' => false))
            ->add('name')
            ->add('description', 'textarea', array('required' => false))
            ->add('context')
        ;
    }


    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name')
            ->add('enabled')
            ->add('context.site', null, array(
                'show_filter' => false,
            ))
            ->add('context')
        ;
    }
}

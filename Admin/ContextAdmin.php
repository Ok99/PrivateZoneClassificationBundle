<?php

namespace Ok99\PrivateZoneCore\ClassificationBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class ContextAdmin extends \Sonata\ClassificationBundle\Admin\ContextAdmin
{
    protected $listModes = array(
        'list' => array(
            'class' => 'fa fa-list fa-fw',
        ),
    );

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->ifTrue(!($this->hasSubject() && $this->getSubject()->getId() !== null))
            ->add('id')
            ->ifEnd()
            ->add('site')
            ->add('name')
            ->add('enabled', null, array('required' => false))
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('site', null, array(
                'show_filter' => false,
            ))
            ->add('name')
            ->add('enabled')
        ;
    }

    public function showInAddBlock()
    {
        return true;
    }

    public function isAdmin($object = null)
    {
        return ($object ? $this->isGranted('ADMIN', $object) : $this->isGranted('ADMIN'))
            || $this->isGranted('ROLE_SONATA_CLASSIFICATION_ADMIN_CONTEXT_ADMIN');
    }
}

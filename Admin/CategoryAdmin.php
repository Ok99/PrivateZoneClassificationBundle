<?php

namespace Ok99\PrivateZoneCore\ClassificationBundle\Admin;

use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\ClassificationBundle\Entity\ContextManager;
use Ok99\PrivateZoneCore\ClassificationBundle\Entity\Category;
use Ok99\PrivateZoneCore\PageBundle\Entity\SitePool;

class CategoryAdmin extends \Sonata\ClassificationBundle\Admin\CategoryAdmin
{
    protected $translationDomain = 'SonataClassificationBundle';

    protected $listModes = array(
        'tree' => array(
            'class' => 'fa fa-list fa-fw',
        ),
    );

    /**
     * @var SitePool
     */
    protected $sitePool;

    /**
     * @param string         $code
     * @param string         $class
     * @param string         $baseControllerName
     * @param ContextManager $contextManager
     */
    public function __construct($code, $class, $baseControllerName, ContextManager $contextManager, SitePool $sitePool)
    {
        parent::__construct($code, $class, $baseControllerName, $contextManager);

        $this->sitePool = $sitePool;
    }

    /**
     * Returns list of available contexts
     *
     * @return array
     */
    public function getContextList()
    {
        $criteria = array(
            'site' => $this->sitePool->getCurrentSite($this->getRequest())
        );

        return $this->contextManager->findBy($criteria, array('name' => 'asc'));
    }

    /**
     * {@inheritdoc}
     */
    public function getPersistentParameters()
    {
        $parameters = array(
            'site'         => '',
            'context'      => '',
            'hide_context' => $this->hasRequest() ? (int)$this->getRequest()->get('hide_context', 0) : 0
        );

        if ($this->getSubject()) {
            $parameters['context'] = $this->getSubject()->getContext() ? $this->getSubject()->getContext()->getId() : '';
            $parameters['site'] = $this->getSubject()->getContext() && $this->getSubject()->getContext()->getSite() ? $this->getSubject()->getContext()->getSite()->getId() : '';

            return $parameters;
        }

        if ($this->hasRequest()) {
            if ($filter = $this->getRequest()->get('filter') && isset($filter['context'])) {
                $context = $filter['context']['value'];
            } else {
                $context = $this->getRequest()->get('context', false);
                $available_contexts = array_map(function ($c) { return $c->getId(); }, $this->getContextList());
                if (!$context || !in_array($context, $available_contexts)) {
                    $context = $available_contexts[0];
                }
            }

            $parameters['context'] = $context;
            $parameters['site'] = $this->getRequest()->get('site');
        }

        return $parameters;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('enabled', null, array('label' => 'Aktivní'))
            ->add('name', null, array('label' => 'Název'))
        ;

        if ($this->getSubject()->getId()) {
            $formMapper->add('position', null, array('label' => 'Pozice'));
        }

        if ($this->hasSubject() && $this->getSubject()->getId() === null) { // root category cannot have a parent
            $this->getSubject()->setParent(
                $this->getConfigurationPool()->getContainer()->get('doctrine.orm.entity_manager')
                    ->getRepository('Ok99PrivateZoneClassificationBundle:Category')->findOneBy(array(
                        'context' => 'documents',
                        'parent' => null
                    ))
            );
            $this->getSubject()->setContext(
                $this->getConfigurationPool()->getContainer()->get('doctrine.orm.entity_manager')
                    ->getRepository('Ok99PrivateZoneClassificationBundle:Context')->find('documents')
            );
            $formMapper->add('parent', 'sonata_type_model_hidden', array(), array(
                'admin_code' => 'sonata.classification.admin.category'
            ));
            $formMapper->add('context', 'sonata_type_model_hidden');
        }

        /*$formMapper
            ->with('General', array('class' => 'col-md-6'))
            ->add('name')
            ->add('description', 'textarea', array('required' => false))
        ;

        if ($this->hasSubject()) {
            if ($this->getSubject()->getParent() !== null || $this->getSubject()->getId() === null) { // root category cannot have a parent
                $formMapper
                    ->add('parent', 'sonata_category_selector', array(
                        'category'      => $this->getSubject() ?: null,
                        'model_manager' => $this->getModelManager(),
                        'class'         => $this->getClass(),
                        'required'      => true,
                        'context'       => $this->getSubject()->getContext(),
                    ), array(
                        'admin_code' => 'sonata.classification.admin.category'
                    ));
            }
        }

        $formMapper->end()
            ->with('Options', array('class' => 'col-md-6'))
                ->add('enabled')
                ->add('position', 'integer', array('required' => false, 'data' => 0))
            ->end()
        ;

        if (interface_exists('Sonata\MediaBundle\Model\MediaInterface')) {
            $formMapper
                ->with('General')
                ->add('media', 'sonata_type_model_list',
                    array('required' => false),
                    array(
                        'link_parameters' => array(
                            'provider' => 'sonata.media.provider.image',
                            'context'  => 'sonata_category',
                        ),
                        'admin_code' => 'ok99.privatezone.media.admin.media'
                    )
                )
                ->end();
        }*/
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name', null, array('label' => 'list.label_name'))
            ->add('context')
            ->add('slug')
            ->add('description')
            ->add('enabled', null, array('label' => 'list.label_enabled', 'editable' => true))
            ->add('position')
            ->add('parent', null, array(
                'admin_code' => 'sonata.classification.admin.category'
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name', null, array('label' => 'list.label_name'))
            ->add('enabled', null, array('label' => 'list.label_enabled'))
            ->add('context')
        ;
    }

    public function prePersist($object)
    {
        // make sure that context is set
        // if not try to set it to same as got parent
        if (null === $object->getContext() && $object->getParent()->getContext()) {
            $object->setContext($object->getParent()->getContext());
        }

        /* @var $lastCategory Category */
        $lastCategory = $this->getConfigurationPool()->getContainer()->get('doctrine.orm.default_entity_manager')
            ->getRepository('Ok99PrivateZoneClassificationBundle:Category')
            ->createQueryBuilder('c')
            ->orderBy('c.position', 'desc')
            ->setMaxResults(1)
            ->getQuery()->getSingleResult();

        $object->setPosition($lastCategory->getPosition()+1);
    }

    public function preUpdate($object)
    {
        $this->prePersist($object);
    }

    public function showInAddBlock()
    {
        return true;
    }

    public function isAdmin($object = null)
    {
        return ($object ? $this->isGranted('ADMIN', $object) : $this->isGranted('ADMIN'))
            || $this->isGranted('ROLE_SONATA_CLASSIFICATION_ADMIN_CATEGORY_ADMIN');
    }
}

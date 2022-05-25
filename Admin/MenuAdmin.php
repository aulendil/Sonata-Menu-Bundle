<?php

namespace Prodigious\Sonata\MenuBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Prodigious\Sonata\MenuBundle\Model\MenuInterface;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class MenuAdmin extends AbstractAdmin
{
    protected $baseRoutePattern = 'sonata/menu';

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->with('config.label_menu', ['translation_domain' => 'ProdigiousSonataMenuBundle'])
                ->add('name', TextType::class,
                    [
                        'label' => 'config.label_name'
                    ],
                    [
                        'translation_domain' => 'ProdigiousSonataMenuBundle'
                    ]
                )
                ->add('alias', TextType::class,
                    [
                        'label' => 'config.label_alias'
                    ],
                    [
                        'translation_domain' => 'ProdigiousSonataMenuBundle'
                    ]
                )
            ->end()
        ->end();
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->add('id', null, ['label' => 'config.label_id', 'translation_domain' => 'ProdigiousSonataMenuBundle'])
            ->addIdentifier('alias', null, ['label' => 'config.label_alias', 'translation_domain' => 'ProdigiousSonataMenuBundle'])
            ->addIdentifier('name', null, ['label' => 'config.label_name', 'translation_domain' => 'ProdigiousSonataMenuBundle'])
        ;

        $list->add('_action', 'actions', [
            'label' => 'config.label_modify',
            'translation_domain' => 'ProdigiousSonataMenuBundle',
            'actions' => [
                'edit' => [],
                'delete' => [],
                'items' => ['template' => '@ProdigiousSonataMenu/CRUD/list__action_edit_items.html.twig', 'route' => 'items']
            ]
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('name')
            ->add('alias')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        $collection->add('items', $this->getRouterIdParameter().'/items');
    }

    /**
     * {@inheritdoc}
     */
    public function configure(): void
    {
        $this->setTemplate('edit', '@ProdigiousSonataMenu/CRUD/edit.html.twig');
    }

    /**
     * {@inheritdoc}
     */
    public function toString($object): string
    {
        return $object instanceof MenuInterface ? $object->getName() : $this->getTranslator()->trans("config.label_menu", array(), 'ProdigiousSonataMenuBundle');
    }

    /**
     * @inheritdoc
     */
    public function prePersist(object $object): void
    {
        parent::prePersist($object);
        foreach ($object->getMenuItems() as $menuItem) {
            $menuItem->setMenu($object);
        }
    }

    /**
     * @inheritdoc
     */
    public function preUpdate(object $object): void
    {
        parent::prePersist($object);
        foreach ($object->getMenuItems() as $menuItem) {
            $menuItem->setMenu($object);
        }
    }

}

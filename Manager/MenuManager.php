<?php

namespace Prodigious\Sonata\MenuBundle\Manager;

use Doctrine\ORM\EntityManagerInterface;
use Prodigious\Sonata\MenuBundle\Model\MenuInterface;
use Prodigious\Sonata\MenuBundle\Model\MenuItemInterface;
use Prodigious\Sonata\MenuBundle\Repository\MenuRepository;
use Prodigious\Sonata\MenuBundle\Repository\MenuitemRepository;

/**
 * Menu manager
 */
class MenuManager
{
	const STATUS_ENABLED = TRUE;
	const STATUS_DISABLED = FALSE;
	const STATUS_ALL = NULL;

	const ITEM_ROOT = TRUE;
	const ITEM_CHILD = FALSE;
	const ITEM_ALL = NULL;

	/**
	 * @var EntityManagerInterface
	 */
	protected $em;

	/**
	 * @var MenuRepository
	 */
	protected $menuRepository;

	/**
	 * @var MenuItemRepository
	 */
	protected $menuItemRepository;

	/**
	 * Constructor
	 * @param EntityManagerInterface $em
	 */
	public function __construct(EntityManagerInterface $em)
	{
		$this->em = $em;
		$this->menuRepository = $em->getRepository(MenuInterface::class);
		$this->menuItemRepository = $em->getRepository(MenuItemInterface::class);
	}

	/**
	 * Load menu by id
	 * @param int $id
	 * @return Menu
	 */
	public function load($id): MenuInterface
	{
		$menu = $this->menuRepository->find($id);

		return $menu;
	}

	/**
	 * Load menu by alias
	 * @param string $alias
	 * @return Menu
	 */
	public function loadByAlias($alias): MenuInterface
	{
		$menu = $this->menuRepository->findOneByAlias($alias);

		return $menu;
	}

	/**
	 * Remove a menu
	 * @param mixed $menu
	 */
	public function remove($menu)
	{
		$menu = $this->menuRepository->remove($menu);
	}

	/**
	 * Save a menu
	 * @param Menu $menu
	 */
	public function save(MenuInterface $menu)
	{
		$this->menuRepository->save($menu);
	}

	/**
	 * @return Menu[]
	 */
	public function findAll(): array
	{
		return $this->menuRepository->findAll();
	}

	/**
	 * Get first level menu items
	 * @param Menu $menu
	 * @return MenuItems[]
	 */
	public function getRootItems(MenuInterface $menu, $status): array
	{
		return $this->getMenuItems($menu, static::ITEM_ROOT, $status);
	}

	/**
	 * Get enabled menu items
	 * @param Menu $menu
	 * @return MenuItems[]
	 */
	public function getEnabledItems(MenuInterface $menu): array
	{
		return $this->getMenuItems($menu, static::ITEM_ALL, static::STATUS_ENABLED);
	}

	/**
	 * Get disabled menu items
	 * @param Menu $menu
	 * @return MenuItems[]
	 */
	public function getDisabledItems(MenuInterface $menu)
	{
		return $this->getMenuItems($menu, static::ITEM_ALL, static::STATUS_DISABLED);
	}

	/**
	 * Get menu items
	 * @return MenuItem[]
	 */
	public function getMenuItems(MenuInterface $menu, $root = self::ITEM_ALL, $status = self::STATUS_ALL)
	{
		$menuItems = $menu->getMenuItems()->toArray();

		return array_filter($menuItems, function(MenuItemInterface $menuItem) use ($root, $status) {
			// Check root parameter
			if ($root === static::ITEM_ROOT && NULL !== $menuItem->getParent()
				|| $root === static::ITEM_CHILD && NULL === $menuItem->getParent()
			) {
				return;
			}

			// Check status parameter
			if ($status === static::STATUS_ENABLED && !$menuItem->getEnabled()
				|| $status === static::STATUS_DISABLED && $menuItem->getEnabled()
			) {
				return;
			}

			return $menuItem;
		});
	}

	/**
	 * Update menu tree
	 * @param mixed $menu
	 * @param array $items
	 * @return bool
	 */
	public function updateMenuTree($menu, $items, $parent = NULL)
	{
		$update = FALSE;

		if (!($menu instanceof MenuInterface)) {
			$menu = $this->load($menu);
		}

		if (!empty($items) && $menu) {

			foreach ($items as $pos => $item) {
				/** @var MenuItem $menuItem */
				$menuItem = $this->menuItemRepository->findOneBy(['id' => $item->id, 'menu' => $menu]);

				if ($menuItem) {
					$menuItem
						->setPosition($pos)
						->setParent($parent);

					$this->em->persist($menuItem);
				}

				if (isset($item->children) && !empty($item->children)) {
					$this->updateMenuTree($menu, $item->children, $menuItem);
				}
			}

			$this->em->flush();

			$update = TRUE;
		}

		return $update;
	}

}

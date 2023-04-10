<?php

namespace Prodigious\Sonata\MenuBundle\Model;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Menu
 */
#[ORM\Table(name: 'sonata_menu')]
#[ORM\MappedSuperclass]
#[ORM\InheritanceType('SINGLE_TABLE')]
abstract class Menu implements MenuInterface
{
	#[ORM\Column(name: 'name', type: 'string', length: 255)]
	protected string $name;

	#[ORM\Column(name: 'alias', type: 'string', length: 255)]
	protected string $alias;

	#[ORM\OneToMany(mappedBy: 'menu', targetEntity: MenuItemInterface::class, cascade: ['persist'])]
	#[ORM\OrderBy(['position' => 'ASC'])]
	protected Collection $menuItems;

	public function __construct()
	{
		$this->menuItems = new ArrayCollection();
	}


	public function setName(string $name): MenuInterface
	{
		$this->name = $name;

		return $this;
	}

	public function getName(): string
	{
		return $this->name;
	}


	public function setAlias($alias): MenuInterface
	{
		$this->alias = $alias;
		return $this;
	}

	public function getAlias(): string
	{
		return $this->alias;
	}

	public function addMenuItem(MenuItemInterface $menuItem): MenuInterface
	{
		$this->menuItems[] = $menuItem;

		$menuItem->setMenu($this);

		return $this;
	}


	public function removeMenuItem(MenuItemInterface $menuItem): MenuInterface
	{
		$this->menuItems->removeElement($menuItem);

		return $this;
	}


	public function setMenuItems(ArrayCollection $menuItems): MenuInterface
	{
		$this->menuItems = $menuItems;

		return $this;
	}


	/**
	 * @return ArrayCollection<MenuItemInterface>
	 */
	public function getMenuItems(): ArrayCollection
	{
		return $this->menuItems;
	}

	public function __toString()
	{
		return $this->name ?? "";
	}
}

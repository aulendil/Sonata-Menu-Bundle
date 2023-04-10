<?php

namespace Prodigious\Sonata\MenuBundle\Model;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * MenuItem
 */
#[ORM\Table(name: 'sonata_menu_item')]
#[ORM\MappedSuperclass]
#[ORM\InheritanceType('SINGLE_TABLE')]
abstract class MenuItem implements MenuItemInterface
{
	#[ORM\Column(name: 'name', type: 'string', length: 255)]
	protected string $name;

	#[ORM\Column(name: 'url', type: 'string', length: 255, nullable: TRUE)]
	protected ?string $url = NULL;

	#[ORM\Column(name: 'class_attribute', type: 'string', length: 255, nullable: TRUE)]
	protected ?string $classAttribute = NULL;

	#[ORM\Column(name: 'position', type: 'smallint', nullable: TRUE, options: ['unsigned' => TRUE])]
	protected int $position;

	#[ORM\Column(name: 'target', type: 'boolean', nullable: TRUE, options: ['default' => FALSE])]
	protected bool $target;


	#[ORM\Column(name: 'enabled', type: 'boolean', nullable: TRUE, options: ['default' => TRUE])]
	protected bool $enabled;

	protected $page = NULL;


	#[ORM\ManyToOne(targetEntity: MenuItemInterface::class, cascade: ['persist'], inversedBy: 'children')]
	#[ORM\JoinColumn(name: 'parent', referencedColumnName: 'id', nullable: TRUE, onDelete: 'SET NULL')]
	protected ?MenuItemInterface $parent = NULL;


	#[ORM\OneToMany(mappedBy: 'parent', targetEntity: MenuItemInterface::class, cascade: ['all'])]
	#[ORM\OrderBy(['position' => 'ASC'])]
	protected Collection $children;

	#[ORM\ManyToOne(targetEntity: MenuInterface::class, inversedBy: 'menuItems')]
	#[ORM\JoinColumn(name: 'menu', referencedColumnName: 'id', nullable: FALSE, onDelete: 'CASCADE')]
	protected MenuInterface $menu;

	/**
	 * Class constructor
	 */
	public function __construct()
	{
		$this->children = new ArrayCollection();
		$this->position = 999;
		$this->enabled = TRUE;
	}

	public function setName(string $name): MenuItemInterface
	{
		$this->name = $name;

		return $this;
	}


	public function getName(): string
	{
		return $this->name;
	}

	public function setUrl(?string $url): MenuItemInterface
	{
		$this->url = $url;

		return $this;
	}


	public function getUrl(): ?string
	{
		return $this->url;
	}


	public function setClassAttribute(?string $classAttribute): MenuItemInterface
	{
		$this->classAttribute = $classAttribute;

		return $this;
	}


	public function getClassAttribute(): ?string
	{
		return $this->classAttribute;
	}

	public function setPosition(?int $position): MenuItemInterface
	{
		$this->position = $position;

		return $this;
	}


	public function getPosition(): int
	{
		return $this->position;
	}


	public function setTarget(bool $target): MenuItemInterface
	{
		$this->target = $target;

		return $this;
	}

	public function getTarget(): bool
	{
		return $this->target;
	}

	public function setEnabled(bool $enabled): MenuItemInterface
	{
		$this->enabled = $enabled;

		if (!$enabled && $this->hasChild()) {
			foreach ($this->children as $child) {
				if ($child->enabled) {
					$child->setEnabled(FALSE);
					$child->setParent(NULL);
				}
			}
			$this->children = new ArrayCollection();
		}

		return $this;
	}


	public function getEnabled(): bool
	{
		return $this->enabled;
	}

	public function getPage()
	{
		return $this->page;
	}

	public function setPage($page): MenuItemInterface
	{
		$this->page = $page;

		return $this;
	}

	public function getParent(): ?MenuItemInterface
	{
		return $this->parent;
	}

	public function setParent(?MenuItemInterface $parent): MenuItemInterface
	{
		$this->parent = $parent;

		if (!is_null($parent)) {
			$parent->addChild($this);
		}

		return $this;
	}


	public function addChild(MenuItemInterface $child): MenuItemInterface
	{
		$this->children[] = $child;

		return $this;
	}

	public function removeChild(MenuItemInterface $child): MenuItemInterface
	{
		$this->children->removeElement($child);
		return $this;
	}

	public function setChildren(ArrayCollection $children): MenuItemInterface
	{
		$this->children = $children;

		return $this;
	}


	public function getChildren(): Collection
	{
		return $this->children;
	}


	public function setMenu(MenuInterface $menu): MenuItemInterface
	{
		$this->menu = $menu;

		return $this;
	}


	public function getMenu(): MenuInterface
	{
		return $this->menu;
	}


	public function hasChild(): bool
	{
		return count($this->children) > 0;
	}

	/**
	 * Has parent
	 */
	public function hasParent(): bool
	{
		return !is_null($this->parent);
	}

	public function getActiveChildren(): Collection
	{
		$children = [];

		foreach ($this->children as $child) {
			if ($child->enabled) {
				$children[] = $child;
			}
		}

		return new ArrayCollection($children);
	}

	public function __toString(): string
	{
		return $this->name ?? "";
	}

}

<?php

namespace Prodigious\Sonata\MenuBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Prodigious\Sonata\MenuBundle\Model\MenuItem as BaseMenuItem;

#[ORM\Table(name: 'sonata_menu_item')]
#[ORM\Entity(repositoryClass: 'Prodigious\Sonata\MenuBundle\Repository\MenuItemRepository')]
class MenuItem extends BaseMenuItem
{
	#[ORM\Id]
	#[ORM\GeneratedValue]
	#[ORM\Column(type: 'integer')]
	private ?int $id = NULL;

	public function getId(): ?int
	{
		return $this->id;
	}

	/**
	 * Class constructor
	 */
	public function __construct()
	{
		parent::__construct();
	}
}

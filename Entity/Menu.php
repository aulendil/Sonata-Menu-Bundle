<?php

namespace Prodigious\Sonata\MenuBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Prodigious\Sonata\MenuBundle\Model\Menu as BaseMenu;

#[ORM\Table(name: 'sonata_menu')]
#[ORM\Entity(repositoryClass: 'Prodigious\Sonata\MenuBundle\Repository\MenuRepository')]
class Menu extends BaseMenu
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

<?php

namespace Prodigious\Sonata\MenuBundle\Controller;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Prodigious\Sonata\MenuBundle\Manager\MenuManager;
use Sonata\AdminBundle\Route\RouteGeneratorInterface;
use Prodigious\Sonata\MenuBundle\Model\MenuItemInterface;
use Sonata\AdminBundle\Controller\CRUDController as Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

class MenuItemController extends Controller
{
	private EntityManagerInterface $entityManager;
	private RouteGeneratorInterface $routeGenerator;

	public function __construct(EntityManagerInterface $entityManager, RouteGeneratorInterface $generator)
	{
		$this->entityManager = $entityManager;
		$this->routeGenerator = $generator;
	}

	/**
	 * @param integer $id
	 */
	public function toggleAction($id)
	{

		/** @var MenuItemInterface $object */
		$object = $this->admin->getSubject();

		if (!$object) {
			throw new NotFoundHttpException(sprintf('unable to find the object with id: %s', $id));
		}

		$object->setEnabled(!$object->getEnabled());

		$this->entityManager->persist($object);
		$this->entityManager->flush();




		return new RedirectResponse($this->routeGenerator
			->generateUrl(
				$this->admin->getParent(),
				'items',
				['id' => $object->getMenu()->getId()]
			)
		);
	}

	/**
	 * {@inheritdoc}
	 */
	protected function redirectTo(Request $request, object $object): RedirectResponse
	{
		$request = $this->admin->getRequest();
		$response = parent::redirectTo($request, $object);


		if (
			NULL !== $request->get('btn_update_and_list')
			|| NULL !== $request->get('btn_create_and_list')
			|| NULL !== $request->get('btn_update_and_edit')
			|| $this->admin->getRequest()->get('_method') === 'DELETE'
		) {
			$url = $this->admin->generateUrl('list');

			if (!empty($object) && $object instanceof MenuItemInterface) {
				$menu = $object->getMenu();

				if ($menu && $this->admin->isChild()) {
					$url = $this->admin->getParent()->generateObjectUrl('items', $menu, ['id' => $menu->getId()]);
				}
			}

			$response->setTargetUrl($url);
		}

		return $response;
	}
}

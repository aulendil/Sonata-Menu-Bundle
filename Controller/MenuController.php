<?php

namespace Prodigious\Sonata\MenuBundle\Controller;

use Prodigious\Sonata\MenuBundle\Manager\MenuManager;
use Sonata\AdminBundle\Controller\CRUDController as Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class MenuController extends Controller
{
    private MenuManager $menuManager;


    public function __construct(MenuManager $menuManager)
    {
        $this->menuManager = $menuManager;
    }


    /**
	 * Manager menu items
	 *
	 * @param $id
	 */
    public function itemsAction(Request $request, $id)
    {
    	$object = $this->admin->getSubject();

    	if (empty($object)) {
            throw new NotFoundHttpException(sprintf('unable to find the object with id : %s', $id));
        }


        if (null !== $request->get('btn_update') && $request->getMethod() == 'POST') {

            $menuId = $request->get('menu', null);
            $items = $request->get('items', null);

            if(!empty($items) && !empty(intval($menuId))) {
                $items = json_decode($items);

                $update = $this->menuManager->updateMenuTree($menuId, $items);
                /** @var TranslatorInterface $translator */
                $translator = $this->get('translator');

                $request->getSession()->getFlashBag()->add('notice',
                    $translator->trans(
                        $update ? 'config.label_saved' : 'config.label_error',
                        array(),
                        'ProdigiousSonataMenuBundle'
                    )
                );

                return new RedirectResponse($this->get('sonata.admin.route.default_generator')
                    ->generateUrl(
                        $this->get('prodigious_sonata_menu.admin.menu'),
                        'items',
                        ['id' => $menuId]
                    )
                );
            }
        }

        $menuItemsEnabled = $this->menuManager->getRootItems($object, MenuManager::STATUS_ENABLED);
        $menuItemsDisabled = $this->menuManager->getDisabledItems($object);

        $menus = $this->menuManager->findAll();

    	return $this->renderWithExtraParams('@ProdigiousSonataMenu/Menu/menu_edit_items.html.twig', array(
            'menus' => $menus,
    		'menu' => $object,
            'menuItemsEnabled' => $menuItemsEnabled,
            'menuItemsDisabled' => $menuItemsDisabled
        ));
    }
}

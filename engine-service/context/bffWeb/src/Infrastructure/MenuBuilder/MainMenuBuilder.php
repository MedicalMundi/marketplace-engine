<?php declare(strict_types=1);

/**
 * This file is part of the medicalmundi/marketplace-engine
 *
 * @copyright (c) 2024 MedicalMundi
 *
 * This software consists of voluntary contributions made by many individuals
 * {@link https://github.com/medicalmundi/marketplace-engine/graphs/contributors developer} and is licensed under the MIT license.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 * @license https://github.com/MedicalMundi/marketplace-engine/blob/main/LICENSE MIT
 */

namespace BffWeb\Infrastructure\MenuBuilder;

use Knp\Menu\Attribute\AsMenuBuilder;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;

final class MainMenuBuilder
{
    public function __construct(
        private FactoryInterface $factory
    ) {
    }

    #[AsMenuBuilder(name: 'main')]
    public function createMainMenu(array $options): ItemInterface
    {
        $menu = $this->factory->createItem('root');

        $menu->setChildrenAttribute('class', 'nav');

        $menu->addChild('Home', [
            'route' => 'app_home',
        ]);

        return $menu;
    }

    #[AsMenuBuilder(name: 'profile menu')]
    public function createProfileMenu(): ItemInterface
    {
        $menu = $this->factory->createItem('profile menu');
        $menu->setChildrenAttribute('class', 'navbar-nav me-auto mb-2 mb-lg-0');

        $this->addProfileMenu($menu);

        return $menu;
    }

    private function addProfileMenu(ItemInterface $menu): void
    {
        $menu->addChild('menu.profile', [
            //'label' => '<span class="icon-vcard"></span>' . 'menu.profile',
            'label' => 'menu.profile',
            'uri' => 'my_profile',
            'extras' => [
                'safe_label' => true,
            ],
        ]);
        $menu['menu.profile']->setAttribute('id', 'back_to_homepage');
        $menu['menu.profile']->setAttribute('class', 'nav-item');
        $menu['menu.profile']->setLinkAttribute('class', 'nav-link');

        $menu->addChild('menu.settings', [
            'label' => '<span class="icon-tools"></span>' . 'menu.settings',
            'uri' => 'edit_profile',
            'extras' => [
                'safe_label' => true,
            ],
        ]);
        $menu->addChild('menu.change_password', [
            'label' => '<span class="icon-key"></span>' . 'menu.change_password',
            'uri' => 'change_password',
            'extras' => [
                'safe_label' => true,
            ],
        ]);
    }

    #[AsMenuBuilder(name: 'administration menu')]
    public function createAdministrationMenu(): ItemInterface
    {
        $menu = $this->factory->createItem('root');
        $menu->setChildrenAttribute('class', 'navbar-nav me-auto mb-2 mb-lg-0');

        $menu->addChild('home', [
            'label' => 'Home',
            'route' => 'app_home',
            'extras' => [
                'safe_label' => true,
            ],
        ]);
        $menu['home']->setAttribute('class', 'nav-item');
        $menu['home']->setLinkAttribute('class', 'nav-link');

        $this->addCatalogMenu($menu);

        return $menu;
    }

    private function addCatalogMenu(ItemInterface $menu): void
    {
        $menu->addChild('catalog', [
            //'label' => '<span class="icon-vcard"></span>' . 'menu.profile',
            'label' => 'Modules',
            'route' => 'web_admin_catalog_index',
            'extras' => [
                'safe_label' => true,
            ],
        ]);
        $menu['catalog']->setAttribute('class', 'nav-item');
        $menu['catalog']->setLinkAttribute('class', 'nav-link');
    }

}

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

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;

final class MainMenuBuilder
{
    public function __construct(
        private FactoryInterface $factory
    ) {
    }

    public function createMainMenu(array $options): ItemInterface
    {
        $menu = $this->factory->createItem('root');
        $menu->setChildrenAttribute('class', 'nav');

        $menu->addChild('Home', [
            'route' => 'app_home',
        ]);

        return $menu;
    }

    public function createProfileMenu(): ItemInterface
    {
        $menu = $this->factory->createItem('root');
        $menu->setChildrenAttribute('class', 'nav nav-tabs nav-stacked');

        $this->addProfileMenu($menu);

        return $menu;
    }

    private function addProfileMenu(ItemInterface $menu): void
    {
        $menu->addChild('menu.profile', [
            'label' => '<span class="icon-vcard"></span>' . 'menu.profile',
            'uri' => 'my_profile',
            'extras' => [
                'safe_label' => true,
            ],
        ]);
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
}

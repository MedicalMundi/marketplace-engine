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

namespace BffApiTests\Unit\Framework;

use BffApi\Infrastructure\Framework\Extension\BffApiModuleExtension;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(BffApiModuleExtension::class)]
class FrameworkExtensionTest extends TestCase
{
    #[Test]
    public function should_have_the_correct_alias_name()
    {
        $extension = new BffApiModuleExtension();

        self::assertEquals('module_bff_api', $extension->getAlias());
    }
}

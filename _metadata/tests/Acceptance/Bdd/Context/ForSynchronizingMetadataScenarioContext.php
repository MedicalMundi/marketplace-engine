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

namespace Metadata\Tests\Acceptance\Bdd\Context;

use Behat\Behat\Context\Context;
use Metadata\AdapterForStoringMetadataFake\FakeForStoringMetadata;
use Metadata\Core\MetadataModule;
use Metadata\Core\Port\Driven\ModuleMetadata;
use Metadata\Core\Port\Driver\ForConfiguringModule\ForConfiguringModule;
use Metadata\Core\Port\Driver\ForSynchronizingMetadata\ForSynchronizingMetadata;
use PHPUnit\Framework\Assert;

final class ForSynchronizingMetadataScenarioContext implements Context
{
    private readonly ForConfiguringModule $moduleConfigurator;

    private ForSynchronizingMetadata $metadataUpdater;

    private ?ModuleMetadata $currentModuleMetadata = null;

    public function __construct()
    {
        $module = new MetadataModule(metadataStore: new FakeForStoringMetadata());
        $this->metadataUpdater = $module->metadataUpdater();
        $this->moduleConfigurator = $module->moduleConfigurator();
    }

    /**
     * @Given there is no metadata for module with code :moduleId at metadata repository
     */
    public function thereIsNoMetadataForModuleWithCodeAtMetadataRepository(string $moduleId)
    {
        $this->moduleConfigurator->eraseMetadata($moduleId);
    }

    /**
     * @When I ask for getting the metadata for module with code :moduleId
     */
    public function iAskForGettingTheMetadataForModuleWithCode(string $moduleId)
    {
        $this->currentModuleMetadata = $this->metadataUpdater->getMetadataForModule($moduleId);
    }

    /**
     * @Then I should obtain no module
     */
    public function iShouldObtainNoModule()
    {
        Assert::assertNull($this->currentModuleMetadata);
    }
}

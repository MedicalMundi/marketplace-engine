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
use Behat\Gherkin\Node\TableNode;
use Metadata\AdapterForReadingMetadataFromOriginalSourceStub\StubAdapterForReadingExternalMetadataSource;
use Metadata\AdapterForStoringMetadataFake\FakeForStoringMetadata;
use Metadata\Core\MetadataModule;
use Metadata\Core\Port\Driven\ForReadingExternalMetadataSource\ExternalMetadataDto;
use Metadata\Core\Port\Driven\ModuleMetadata;
use Metadata\Core\Port\Driver\ForConfiguringModule\ForConfiguringModule;
use Metadata\Core\Port\Driver\ForSynchronizingMetadata\ForSynchronizingMetadata;
use PHPUnit\Framework\Assert;
use Ramsey\Uuid\Uuid;

final class ForSynchronizingMetadataScenarioContext implements Context
{
    private readonly ForConfiguringModule $moduleConfigurator;

    private ForSynchronizingMetadata $metadataUpdater;

    private ?ModuleMetadata $currentModuleMetadata = null;

    public function __construct()
    {
        $module = new MetadataModule(metadataStore: new FakeForStoringMetadata(), metadataReader: new StubAdapterForReadingExternalMetadataSource());
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

    /**
     * @Given there is the following metadata at metadata repository:
     */
    public function thereIsTheFollowingMetadataAtMetadataRepository(array $modulesMetadata)
    {
        $metadata = $modulesMetadata[0];
        $this->moduleConfigurator->createMetadata($metadata);
    }

    /**
     * @Then I should obtain the following metadata:
     */
    public function iShouldObtainTheFollowingMetadata(array $modulesMetadata)
    {
        $expectedResult = $modulesMetadata[0];
        Assert::assertEquals($expectedResult, $this->currentModuleMetadata);
    }

    /**
     * @Transform table:enableSync,category,tag,moduleCode
     */
    public function castModulesMetadataTable(TableNode $modulesMetadataTable): array
    {
        $moduleMetadata = [];
        foreach ($modulesMetadataTable as $moduleMetadataHash) {
            $tags = explode(',', $moduleMetadataHash['tag']);
            $metadata = new ModuleMetadata(
                moduleId: Uuid::fromString($moduleMetadataHash['moduleCode']),
                repositoryUrl: 'https://irrelevant.com',
                category: $moduleMetadataHash['category'],
                tags: $tags,
                enableSync: (bool) $moduleMetadataHash['enableSync'],
            );
            $moduleMetadata[] = $metadata;
        }

        return $moduleMetadata;
    }

    /**
     * @Transform table:enableSync,category,tag,moduleCode,moduleRepositoryUrl
     */
    public function castModulesMetadataWithUrlTable(TableNode $modulesMetadataTable): array
    {
        $moduleMetadata = [];
        foreach ($modulesMetadataTable as $moduleMetadataHash) {
            $tags = explode(',', $moduleMetadataHash['tag']);
            $metadata = new ModuleMetadata(
                moduleId: Uuid::fromString($moduleMetadataHash['moduleCode']),
                repositoryUrl: $moduleMetadataHash['moduleRepositoryUrl'],
                category: $moduleMetadataHash['category'],
                tags: $tags,
                enableSync: (bool) $moduleMetadataHash['enableSync'],
            );
            $moduleMetadata[] = $metadata;
        }

        return $moduleMetadata;
    }

    /**
     * @Given there is the following metadata at metadata original source :url
     */
    public function thereIsTheFollowingMetadataAtMetadataOriginalSource2($url, array $externalModulesMetadataTable)
    {
        $this->moduleConfigurator->setExternalMetadataDto($url, $externalModulesMetadataTable[0]);
    }

    /**
     * @Transform table:enableSync,category,tag
     */
    public function castExternalModulesMetadataTable(TableNode $externalModulesMetadataTable): array
    {
        $externalModuleMetadata = [];
        foreach ($externalModulesMetadataTable as $moduleMetadataHash) {
            $tags = explode(',', $moduleMetadataHash['tag']);
            $metadata = new ExternalMetadataDto(
                enableSync: (bool) $moduleMetadataHash['enableSync'],
                category: $moduleMetadataHash['category'],
                tags: $tags,
            );
            $externalModuleMetadata[] = $metadata;
        }

        return $externalModuleMetadata;
    }

    /**
     * @When I ask for update the metadata for module with code :moduleId
     */
    public function iAskForUpdateTheMetadataForModuleWithCode(string $moduleId)
    {
        $this->metadataUpdater->synchronizeMetadataFor($moduleId);
        $this->currentModuleMetadata = $this->metadataUpdater->getMetadataForModule($moduleId);
    }

    /**
     * @Then I should obtain the following updated metadata:
     */
    public function iShouldObtainTheFollowingUpdatedMetadata(array $updatedModuleMetadata)
    {
        $expectedResult = $updatedModuleMetadata[0];

        Assert::assertEquals($this->currentModuleMetadata->isSynchronizable(), $expectedResult->isSynchronizable());
        Assert::assertEquals($this->currentModuleMetadata->category(), $expectedResult->category());
        Assert::assertEquals($this->currentModuleMetadata->tags(), $expectedResult->tags());
    }
}

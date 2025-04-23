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

namespace Metadata\Tests\Unit;

use Metadata\AdapterForReadingExternalMetadataSourceStub\StubAdapterForReadingExternalMetadataSource;
use Metadata\AdapterForStoringMetadataFake\FakeForStoringMetadata;
use Metadata\Core\MetadataModule;
use Metadata\Core\MetadataUpdater;
use Metadata\Core\MetadataValidationEngine\FixedTrueMetadataValidationEngineValidation;
use Metadata\Core\MetadataValidationEngine\MetadataValidationException;
use Metadata\Core\ModuleConfigurator;
use Metadata\Core\Port\Driven\ForReadingExternalMetadataSource\ExternalMetadataDto;
use Metadata\Core\Port\Driven\ModuleMetadata;
use Metadata\Core\UnreferencedMetadataModuleException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

#[CoversClass(MetadataUpdater::class)]
#[CoversClass(UnreferencedMetadataModuleException::class)]
#[UsesClass(MetadataModule::class)]
#[UsesClass(FakeForStoringMetadata::class)]
#[UsesClass(StubAdapterForReadingExternalMetadataSource::class)]
#[UsesClass(FixedTrueMetadataValidationEngineValidation::class)]
#[UsesClass(ModuleConfigurator::class)]
#[UsesClass(ModuleMetadata::class)]
#[UsesClass(ExternalMetadataDto::class)]
class MetadataUpdaterTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    #[Test]
    public function should_detect_unreferenced_metadata_module()
    {
        $module = new MetadataModule(
            new FakeForStoringMetadata(),
            new StubAdapterForReadingExternalMetadataSource()
        );
        $moduleId = '15f7699b-e9a6-4a8a-9606-95d0a08c1959';
        self::expectException(UnreferencedMetadataModuleException::class);
        self::expectExceptionMessage(
            \sprintf('Unreferenced MetadataModule with Id: %s', $moduleId)
        );

        $module->metadataUpdater()->synchronizeMetadataFor($moduleId);
    }

    #[Test]
    public function should_update_metadata_for_a_given_module()
    {
        $app = new MetadataModule(
            new FakeForStoringMetadata(),
            new StubAdapterForReadingExternalMetadataSource()
        );
        $moduleMetadata = $this->generateAModuleMetadata(
            $moduleIdAsString = 'b33d940e-58b0-4096-85f1-bfa67bae7ef4',
            $repoUrl = 'https://github.com/foo/bar',
            $category = 'irrelevant',
            $tags = ['irrelevant1-1', 'irrelevant-2']
        );
        $app->moduleConfigurator()->createMetadata($moduleMetadata);
        $app->moduleConfigurator()->setExternalMetadataDto($repoUrl, new ExternalMetadataDto(false, 'performance', ['cache', 'redis']));

        $app->metadataUpdater()->synchronizeMetadataFor($moduleIdAsString);

        $updatedModuleMetadata = $app->metadataUpdater()->getMetadataForModule($moduleIdAsString);
        self::assertEquals(false, $updatedModuleMetadata->isSynchronizable());
        self::assertEquals('performance', $updatedModuleMetadata->category());
        self::assertEquals(['cache', 'redis'], $updatedModuleMetadata->tags());
    }

    #[Test]
    public function should_throw_metadata_validation_error()
    {
        self::markTestIncomplete('Implement a real Metadata Validator');
        self::expectException(MetadataValidationException::class);
        self::expectExceptionMessage(
            'Metadata validation error'
        );

        $app = new MetadataModule(
            new FakeForStoringMetadata(),
            new StubAdapterForReadingExternalMetadataSource(),
        );
        $moduleMetadata = $this->generateAModuleMetadata(
            $moduleIdAsString = 'b33d940e-58b0-4096-85f1-bfa67bae7ef4',
            $repoUrl = 'https://github.com/foo/bar',
            $category = 'irrelevant',
            $tags = ['irrelevant1-1', 'irrelevant-2']
        );
        $app->moduleConfigurator()->createMetadata($moduleMetadata);
        $app->moduleConfigurator()->setExternalMetadataDto($repoUrl, new ExternalMetadataDto(false, 'performance', ['foo', 'bar']));

        $app->metadataUpdater()->synchronizeMetadataFor($moduleIdAsString);
    }

    private function generateAModuleMetadata(string $moduleIdAsString = '', string $repoUrl = '', string $category = '', array $tags = []): ModuleMetadata
    {
        $moduleId = ($moduleIdAsString === '') ? Uuid::uuid4() : Uuid::fromString($moduleIdAsString);
        $repoUrl = ($repoUrl === '') ? 'https://github.com/foo/bar' : $repoUrl;
        $category = ($category === '') ? 'irrelevant' : $category;
        $tags = ($tags === []) ? ['irrelevant-1', 'irrelevant-2'] : $tags;

        return new ModuleMetadata($moduleId, $repoUrl, $category, $tags);
    }
}

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

namespace MetadataTests\Unit\Core\Process;

use Ecotone\Lite\EcotoneLite;
use Metadata\AdapterForReadingExternalMetadataSourceStub\StubAdapterForReadingExternalMetadataSource;
use Metadata\Core\MetadataValidationEngine\MetadataValidationException;
use Metadata\Core\MetadataValidationEngine\MetadataValidator;
use Metadata\Core\Port\Driven\ForReadingExternalMetadataSource\ExternalMetadataDto;
use Metadata\Core\Port\Driven\ForReadingExternalMetadataSource\MetadataReaderException;
use Metadata\Core\Process\Event\ModuleMetadataUpdateAbortedWithError;
use Metadata\Core\Process\Event\ModuleMetadataUpdateCompletedWithInvalidMetadata;
use Metadata\Core\Process\Event\ModuleMetadataUpdateCompletedWithMetadata;
use Metadata\Core\Process\Event\ModuleMetadataUpdateCompletedWithoutMetadata;
use Metadata\Core\Process\MetadataSynchronizerWorkflow;
use Metadata\Core\Process\StartModuleMetadataUpdate;
use Metadata\Core\ValueObject\Repository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Ramsey\Uuid\Uuid;

#[CoversClass(MetadataSynchronizerWorkflow::class)]
#[CoversClass(ModuleMetadataUpdateCompletedWithInvalidMetadata::class)]
#[CoversClass(ModuleMetadataUpdateCompletedWithMetadata::class)]
#[CoversClass(ModuleMetadataUpdateCompletedWithoutMetadata::class)]
#[CoversClass(ModuleMetadataUpdateAbortedWithError::class)]
#[CoversClass(StartModuleMetadataUpdate::class)]
#[UsesClass(ExternalMetadataDto::class)]
#[UsesClass(MetadataValidator::class)]
#[UsesClass(MetadataValidationException::class)]
#[UsesClass(Repository::class)]
#[UsesClass(StubAdapterForReadingExternalMetadataSource::class)]
class MetadataSynchronizerWorkflowTest extends TestCase
{
    private const A_VALID_REPOSITORY_URL = 'https://github.com/username/remository-name';

    private const AN_INVALID_REPOSITORY_URL = 'https://github.com/invalid';

    private const AN_UNSUPPORTED_SERVICE_REPOSITORY_URL = 'https://unsupportedgithostingservice.com/username/remository-name';

    private ?object $stubAdapterForReadingExternalMetadataSource;

    private ?object $messagingSystem;

    protected function setUp(): void
    {
        /** We could provide some Stub implementations */
        $this->stubAdapterForReadingExternalMetadataSource = new StubAdapterForReadingExternalMetadataSource();
        $this->messagingSystem = EcotoneLite::bootstrapFlowTesting(
            [MetadataSynchronizerWorkflow::class],
            [
                MetadataSynchronizerWorkflow::class => new MetadataSynchronizerWorkflow(new NullLogger(), $this->stubAdapterForReadingExternalMetadataSource),
                MetadataValidator::class => new MetadataValidator(),
            ]
        );
    }

    #[Test]
    public function shouldNotifyProcessCompleteWithMetadata()
    {
        $moduleId = Uuid::uuid4()->toString();
        $aStubbedMetadataDto = new ExternalMetadataDto(
            enableSync: true,
            category: 'billing',
            tags: ['sms', 'fax']
        );
        $this->stubAdapterForReadingExternalMetadataSource->setExternalMetadataDto(self::A_VALID_REPOSITORY_URL, $aStubbedMetadataDto);
        $expectedEvent = new ModuleMetadataUpdateCompletedWithMetadata($moduleId, $aStubbedMetadataDto);


        $result = $this->messagingSystem
            ->sendCommand(new StartModuleMetadataUpdate($moduleId, self::A_VALID_REPOSITORY_URL))
            ->getRecordedEvents();


        $this->assertEquals(
            [$expectedEvent],
            $result
        );
    }

    #[Test]
    public function shouldNotifyProcessCompletedWithoutMetadata()
    {
        $moduleId = Uuid::uuid4()->toString();
        $aStubbedMetadataDto = null;
        $this->stubAdapterForReadingExternalMetadataSource->setExternalMetadataDto(self::A_VALID_REPOSITORY_URL, $aStubbedMetadataDto);
        $expectedEvent = new ModuleMetadataUpdateCompletedWithoutMetadata($moduleId);


        $result = $this->messagingSystem
            ->sendCommand(new StartModuleMetadataUpdate($moduleId, self::A_VALID_REPOSITORY_URL))
            ->getRecordedEvents();


        $this->assertEquals(
            [$expectedEvent],
            $result
        );
    }

    #[Test]
    public function shouldNotifyProcessCompletedWithInvalidMetadata()
    {
        $moduleId = Uuid::uuid4()->toString();
        $aStubbedMetadataDto = new ExternalMetadataDto(
            enableSync: true,
            category: 'invalid-category',
            tags: ['invalid-tag-1', 'invalid-tag-2']
        );
        $this->stubAdapterForReadingExternalMetadataSource->setExternalMetadataDto(self::A_VALID_REPOSITORY_URL, $aStubbedMetadataDto);
        $expectedEvent = new ModuleMetadataUpdateCompletedWithInvalidMetadata($moduleId);


        $result = $this->messagingSystem
            ->sendCommand(new StartModuleMetadataUpdate($moduleId, self::A_VALID_REPOSITORY_URL))
            ->getRecordedEvents();


        $this->assertEquals(
            [$expectedEvent],
            $result
        );
    }

    #[Test]
    public function shouldNotifyProcessAbortedWithErrorWhenRepositoryUrlIsInvalid()
    {
        $moduleId = Uuid::uuid4()->toString();
        $expectedEvent = new ModuleMetadataUpdateAbortedWithError($moduleId, 'Impossible to fetch package by "' . self::AN_INVALID_REPOSITORY_URL . '" repository.');


        $result = $this->messagingSystem
            ->sendCommand(new StartModuleMetadataUpdate($moduleId, self::AN_INVALID_REPOSITORY_URL))
            ->getRecordedEvents();


        $this->assertEquals(
            [$expectedEvent],
            $result
        );
    }

    #[Test]
    public function shouldNotifyProcessAbortedWithErrorWhenGitHostingServiceProviderIsNotSupported()
    {
        $moduleId = Uuid::uuid4()->toString();
        $expectedEvent = new ModuleMetadataUpdateAbortedWithError($moduleId, 'Unsupported githost service provider: unsupportedgithostingservice.com');


        $result = $this->messagingSystem
            ->sendCommand(new StartModuleMetadataUpdate($moduleId, self::AN_UNSUPPORTED_SERVICE_REPOSITORY_URL))
            ->getRecordedEvents();


        $this->assertEquals(
            [$expectedEvent],
            $result
        );
    }

    #[Test]
    public function shouldNotifyProcessAbortedWithErrorWhenMetadataReaderHasIssue()
    {
        $moduleId = Uuid::uuid4()->toString();
        $this->stubAdapterForReadingExternalMetadataSource->setException(new MetadataReaderException('Network error'));
        $expectedEvent = new ModuleMetadataUpdateAbortedWithError($moduleId, 'Network error');


        $result = $this->messagingSystem
            ->sendCommand(new StartModuleMetadataUpdate($moduleId, self::A_VALID_REPOSITORY_URL))
            ->getRecordedEvents();


        $this->assertEquals(
            [$expectedEvent],
            $result
        );
    }

    protected function tearDown(): void
    {
        $this->stubAdapterForReadingExternalMetadataSource = null;
        $this->messagingSystem = null;
    }
}

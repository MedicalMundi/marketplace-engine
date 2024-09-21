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
use Metadata\Core\MetadataValidationEngine\MetadataValidator;
use Metadata\Core\Port\Driven\ForReadingExternalMetadataSource\ExternalMetadataDto;
use Metadata\Core\Process\MetadataUpdaterWorkflow;
use Metadata\Core\Process\ModuleMetadataDetected;
use Metadata\Core\Process\UpdateModuleMetadata;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Ramsey\Uuid\Uuid;

#[CoversClass(MetadataUpdaterWorkflow::class)]
class MetadataUpdaterWorkflowTest extends TestCase
{
    #[Test]
    public function shouldNotifyASuccessfulMetadataDetection()
    {
        /** We could provide some Stub implementations */
        $stubAdapterForReadingExternalMetadataSource = new StubAdapterForReadingExternalMetadataSource();

        $moduleId = Uuid::uuid4()->toString();
        $repositoryUrl = 'https://github.com/MedicalMundi/oe-module-todo-list';
        $aMetadataDto = new ExternalMetadataDto(
            enableSync: true,
            category: 'billing',
            tags: ['sms', 'fax']
        );
        $expectedMessage = new ModuleMetadataDetected($moduleId, $aMetadataDto);
        $stubAdapterForReadingExternalMetadataSource->setExternalMetadataDto($repositoryUrl, $aMetadataDto);
        $ecotoneLite = EcotoneLite::bootstrapFlowTesting(
            [MetadataUpdaterWorkflow::class],
            [
                MetadataUpdaterWorkflow::class => new MetadataUpdaterWorkflow(new NullLogger(), $stubAdapterForReadingExternalMetadataSource),
                MetadataValidator::class => new MetadataValidator(),
            ]
        );


        $this->assertEquals(
            [$expectedMessage],
            $ecotoneLite
                ->sendCommand(new UpdateModuleMetadata($moduleId, $repositoryUrl))
                ->getRecordedEvents()
        );
    }
}

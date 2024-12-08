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

namespace Metadata\Core\Process;

use Ecotone\Messaging\Attribute\InternalHandler;
use Ecotone\Messaging\Attribute\Parameter\Header;
use Ecotone\Modelling\Attribute\CommandHandler;

use Ecotone\Modelling\EventBus;
use Metadata\Core\MetadataValidationEngine\MetadataValidationException;
use Metadata\Core\MetadataValidationEngine\MetadataValidator;
use Metadata\Core\Port\Driven\ForReadingExternalMetadataSource\ExternalMetadataDto;
use Metadata\Core\Port\Driven\ForReadingExternalMetadataSource\ForReadingExternalMetadataSource;
use Metadata\Core\Port\Driven\ForReadingExternalMetadataSource\MetadataReaderException;
use Metadata\Core\Process\Event\ModuleMetadataUpdateAbortedWithError;
use Metadata\Core\Process\Event\ModuleMetadataUpdateCompletedWithInvalidMetadata;
use Metadata\Core\Process\Event\ModuleMetadataUpdateCompletedWithMetadata;
use Metadata\Core\Process\Event\ModuleMetadataUpdateCompletedWithoutMetadata;
use Metadata\Core\ValueObject\Repository;
use Psr\Log\LoggerInterface;
use UnexpectedValueException;

class MetadataSynchronizerWorkflow
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly ForReadingExternalMetadataSource $metadataReader,
    ) {
    }

    #[CommandHandler(
        outputChannelName: 'process.enrich'
    )]
    public function validateInputData(StartModuleMetadataUpdate $command, EventBus $eventBus): ?StartModuleMetadataUpdate
    {
        $this->logger->info('Metadata synchronizer - initialize update process for module id: ' . $command->moduleId);

        try {
            $repository = Repository::createFromRepositoryUrl($command->repositoryUrl);

            if ($repository->isSupported()) {
                return $command;
            } else {
                $eventBus->publish(
                    new ModuleMetadataUpdateAbortedWithError($command->moduleId, 'Unsupported githost service provider: ' . $repository->getSource())
                );

                $this->logger->info('Metadata synchronizer - unsupported repository detected url: ' . $command->repositoryUrl);
            }
        } catch (UnexpectedValueException $exception) {
            $eventBus->publish(
                new ModuleMetadataUpdateAbortedWithError($command->moduleId, $exception->getMessage())
            );
            $this->logger->info('Metadata synchronizer - error: ' . $exception->getMessage());
        }

        return null;
    }

    #[InternalHandler(
        inputChannelName: 'process.enrich',
        outputChannelName: 'process.readMetadata',
        changingHeaders: true,
    )]
    public function enrichCommand(StartModuleMetadataUpdate $command): array
    {
        $repository = Repository::createFromRepositoryUrl($command->repositoryUrl);

        return [
            'repository' => $repository,
        ];
    }

    #[InternalHandler(
        inputChannelName: 'process.readMetadata',
        outputChannelName: 'process.validateMetadata',
        changingHeaders: true,
    )]
    public function readMetadata(
        StartModuleMetadataUpdate $command,
        EventBus $eventBus
    ): ?array {
        try {
            $metadataDto = $this->metadataReader->readMetadataFromExternalSource($command->repositoryUrl);


            if (null === $metadataDto) {
                $eventBus->publish(
                    new ModuleMetadataUpdateCompletedWithoutMetadata($command->moduleId)
                );
            } else {
                return [
                    'metadata_dto' => $metadataDto,
                ];
            }
        } catch (MetadataReaderException $exception) {
            $eventBus->publish(
                new ModuleMetadataUpdateAbortedWithError($command->moduleId, $exception->getMessage())
            );
            $this->logger->info('Metadata synchronizer - error: ' . $exception->getMessage());
        }


        return null;
    }

    #[InternalHandler(
        inputChannelName: 'process.validateMetadata',
        outputChannelName: 'process.notifyProcessResult',
        changingHeaders: true,
    )]
    public function validateMetadata(
        StartModuleMetadataUpdate $command,
        #[Header('metadata_dto')]
        ExternalMetadataDto $metadataDto,
        MetadataValidator $metadataValidator,
        EventBus $eventBus,
    ): ?array {
        try {
            $metadataValidationResult = $metadataValidator->validate($metadataDto->toArray());

            return [
                'metadata_validation_result' => $metadataValidationResult,
            ];
        } catch (MetadataValidationException $exception) {
            $eventBus->publish(
                new ModuleMetadataUpdateCompletedWithInvalidMetadata($command->moduleId)
            );
            $this->logger->info('Metadata synchronizer - error: ' . $exception->getMessage());
        }

        return null;
    }

    #[InternalHandler(
        inputChannelName: 'process.notifyProcessResult',
    )]
    public function triggerMetadataUpdate(
        StartModuleMetadataUpdate $command,
        #[Header('metadata_validation_result')]
        bool $validationStatus,
        #[Header('metadata_dto')]
        ExternalMetadataDto $metadataDto,
        EventBus $eventBus
    ): void {
        if ($validationStatus) {
            $eventBus->publish(new ModuleMetadataUpdateCompletedWithMetadata($command->moduleId, $metadataDto));
        }
    }
}

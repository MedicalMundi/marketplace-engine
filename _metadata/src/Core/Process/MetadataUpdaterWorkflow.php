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
use Metadata\Core\MetadataValidationEngine\MetadataValidator;
use Metadata\Core\Port\Driven\ForReadingExternalMetadataSource\ExternalMetadataDto;
use Metadata\Core\Port\Driven\ForReadingExternalMetadataSource\ForReadingExternalMetadataSource;
use Metadata\Core\ValueObject\Repository;
use Psr\Log\LoggerInterface;
use UnexpectedValueException;

class MetadataUpdaterWorkflow
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly ForReadingExternalMetadataSource $metadataReader,
    ) {
    }

    #[CommandHandler(
        outputChannelName: 'process.enrich'
    )]
    public function validateInputData(UpdateModuleMetadata $command): ?UpdateModuleMetadata
    {
        $this->logger->info('MetadataUpdater - initialize update process for module id: ' . $command->moduleId);

        try {
            $repository = Repository::createFromRepositoryUrl($command->repositoryUrl);

            if ($repository->isSupported()) {
                return $command;
            } else {
                $this->logger->info('MetadataUpdater - unsupported repository detected url: ' . $command->repositoryUrl);
            }
        } catch (UnexpectedValueException $exception) {
            $this->logger->info('MetadataUpdater - error: ' . $exception->getMessage());
        }

        return null;
    }

    #[InternalHandler(
        inputChannelName: 'process.enrich',
        outputChannelName: 'process.readMetadata',
        changingHeaders: true,
    )]
    public function enrichCommand(UpdateModuleMetadata $command): array
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
        UpdateModuleMetadata $command,
    ): ?array {
        /**
         * if no error enrich command  or fail
         */
        $metadataDto = $this->metadataReader->readMetadataFromExternalSource($command->repositoryUrl);

        return [
            'metadata_dto' => $metadataDto,
        ];
    }

    #[InternalHandler(
        inputChannelName: 'process.validateMetadata',
        outputChannelName: 'process.notifyProcessResult',
        changingHeaders: true,
    )]
    public function validateMetadata(
        #[Header('metadata_dto')]
        ExternalMetadataDto $metadataDto,
        MetadataValidator $metadataValidator
    ): array {
        $metadataValidationResult = $metadataValidator->validate($metadataDto->toArray());

        return [
            'metadata_validation_result' => $metadataValidationResult,
        ];
    }

    #[InternalHandler(
        inputChannelName: 'process.notifyProcessResult',
    )]
    public function triggerMetadataUpdate(
        UpdateModuleMetadata $command,
        #[Header('metadata_validation_result')]
        bool $validationStatus,
        #[Header('metadata_dto')]
        ExternalMetadataDto $metadataDto,
        EventBus $eventBus
    ): void {
        if ($validationStatus) {
            $eventBus->publish(new ModuleMetadataDetected($command->moduleId, $metadataDto));
        } else {
            echo 'complete with no metadata';
            //$eventBus->publish(new ModuleMetadataDetected($command->moduleId, $metadataDto));
        }
    }
}

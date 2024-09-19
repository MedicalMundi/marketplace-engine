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

namespace Metadata\AdapterForReadingExternalMetadataSource;

use Ecotone\Messaging\Attribute\InternalHandler;
use Ecotone\Messaging\Attribute\Parameter\Header;
use Ecotone\Modelling\Attribute\CommandHandler;
use Github\Api\Repo;
use Github\Client as GithubClient;
use Metadata\Core\ValueObject\Repository;
use Nyholm\Psr7\Request;
use Psr\Http\Client\ClientInterface;
use Psr\Log\LoggerInterface;

class StatelessWorkflowProcess
{
    public function __construct(
        private readonly LoggerInterface $logger
    ) {
    }

    #[CommandHandler(outputChannelName: 'module.enrich')]
    public function validateImage(UpdateModuleMetadata $command): UpdateModuleMetadata
    {
        // enrich command with repository VO

        // enrich command with default branch name

        // enrich command with composer.json string

        // extract and validate metadata

        // emit command to update metadata aggregate

        $this->logger->info('MetadataUpdater - init update for module id: ' . $command->moduleId);

        return $command;
    }

    #[InternalHandler(
        inputChannelName: 'module.enrich',
        outputChannelName: 'module.getDefaultBranch',
        changingHeaders: true
    )]
    public function enrichCommand(UpdateModuleMetadata $command, GithubClient $githubClient): array
    {
        /**
         * TODO: handle failure
         */
        $repository = Repository::createFromRepositoryUrl($command->repositoryUrl);


        return [
            'repository' => $repository,
        ];
    }

    #[InternalHandler(
        inputChannelName: 'module.getDefaultBranch',
        outputChannelName: 'module.getComposerJson',
        changingHeaders: true,
    )]
    public function getDefaultBranch(
        UpdateModuleMetadata $command,
        #[Header('repository')]
        Repository $repository,
        GithubClient $githubClient
    ): ?array {
        /**
         * TODO: handle failure
         */
        try {
            $data = $githubClient->repo()->show($repository->getUsername(), $repository->getName());
            if (\array_key_exists('default_branch', $data)) {
                return [
                    'default_branch' => (string) $data['default_branch'],
                ];
            }
        } catch (\Exception $exception) {
            $this->logger->error('Metadata updater error on module id: ' . $command->moduleId . ' ' . $exception->getMessage());
        }

        return null;
    }

    #[InternalHandler(
        inputChannelName: 'module.getComposerJson',
        outputChannelName: 'module.extractMetadata',
        changingHeaders: true,
    )]
    public function getComposerJson(
        UpdateModuleMetadata $command,
        #[Header('repository')]
        Repository $repository,
        #[Header('default_branch')]
        string $defaultBranchName,
        ClientInterface $httpClient,
        GithubClient $githubClient,
    ): ?array {
        $path = 'composer.json';

        $reference = $defaultBranchName;

        /**
         * TODO: handle failure
         */
        try {
            /** @var Repo $repoApi */
            $repoApi = $githubClient->api('repo');
            $fileInfo = (array) $repoApi
                ->contents()
                ->show($repository->getUsername(), $repository->getName(), $path, $reference);
            $content = $this->getComposerJsonFileContent((string) $fileInfo['download_url'], $httpClient);

            return [
                'composer_json_content' => $content,
            ];
        } catch (\Exception $exception) {
            $this->logger->error('Metadata updater error on module id: ' . $command->moduleId . ' ' . $exception->getMessage());
        }

        return null;
    }

    #[InternalHandler(
        inputChannelName: 'module.extractMetadata',
        outputChannelName: 'image.upload'
    )]
    public function extractMetadata(
        UpdateModuleMetadata $command,
        #[Header('composer_json_content')]
        string $composerJsonContent,
    ): UpdateModuleMetadata {
        $ar = (array) json_decode($composerJsonContent, true);

        if ($this->hasMetadata($ar)) {
            print_r($ar['extra']['openemr-module']['metadata']['oe-modules.com']);
        }

        print_r($nodata = 'No metadata found');

        return $command;
    }

    #[InternalHandler(inputChannelName: 'image.resize', outputChannelName: 'image.upload')]
    public function resizeImage(UpdateModuleMetadata $command): UpdateModuleMetadata
    {
        //echo 'xxxx';

        return $command;
    }

    #[InternalHandler(inputChannelName: 'image.upload')]
    public function uploadImage(UpdateModuleMetadata $command): void
    {
        //$imageUploader->uploadImage($command->path);
        echo 'yyyy';
    }

    private function getComposerJsonFileContent(string $url, ClientInterface $httpclient): string
    {
        $request = new Request('GET', $url);

        $response = $httpclient->sendRequest($request);

        $x = $response->getBody()->getContents();

        return $x;
    }

    /**
     * TODO: refactor
     */
    private function hasMetadata(array $data): bool
    {
        $result = false;

        if (\array_key_exists('extra', $data)) {
            $extraSection = (array) $data['extra'];


            if (\array_key_exists('openemr-module', $extraSection)) {
                $openEmrModuleSection = (array) $extraSection['openemr-module'];

                if (\array_key_exists('metadata', $openEmrModuleSection)) {
                    $metadataSection = (array) $openEmrModuleSection['metadata'];

                    if (\array_key_exists('oe-modules.com', $metadataSection)) {
                        $result = true;
                    }
                }
            }
        }

        return $result;
    }
}

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

use Github\Api\Repo;
use Github\Client as GithubClient;
use Metadata\Core\Port\Driven\ForReadingExternalMetadataSource\ExternalMetadataDto;
use Metadata\Core\Port\Driven\ForReadingExternalMetadataSource\ForReadingExternalMetadataSource;
use Metadata\Core\Port\Driven\ForReadingExternalMetadataSource\MetadataReaderException;
use Metadata\Core\ValueObject\ComposerJsonFile;
use Metadata\Core\ValueObject\Repository;
use Nyholm\Psr7\Request;
use Psr\Http\Client\ClientInterface;

class GithubAdapterForReadingExternalMetadataSource implements ForReadingExternalMetadataSource
{
    public function __construct(
        private readonly GithubClient $githubClient,
        private readonly ClientInterface $httpclient,
    ) {
    }

    public function readMetadataFromExternalSource(string $moduleUrl): ?ExternalMetadataDto
    {
        $repository = Repository::createFromRepositoryUrl($moduleUrl);

        $defaultBranch = $this->getDefaultBranchName($repository);

        /**
         * TODO: refactor
         * $defaultBranch dovrebbe essere sempre un tipo string,
         * la funzione sopra non dovrebbe tornare null
         * ma eccezione di tipo http del client api
         * o eccezione per dati non processabili
         */
        if (null === $defaultBranch) {
            return null;
        }

        $composerJsonFileContent = $this->downloadComposerJsonFileContent($moduleUrl, $defaultBranch);

        $composerFile = ComposerJsonFile::createFromJson($composerJsonFileContent);

        if (! $composerFile->hasMetadata()) {
            return null;
        }

        $extractedMetadata = (array) $composerFile->getMetadata();
        $metadataDto = new ExternalMetadataDto(
            enableSync: true,
            category: (string) $extractedMetadata['category'],
            tags: (array) $extractedMetadata['tags'],
        );

        return $metadataDto;
    }

    private function doDownloadHttpRequest(string $url): string
    {
        $request = new Request('GET', $url);

        $response = $this->httpclient->sendRequest($request);

        return $response->getBody()->getContents();
    }

    private function downloadComposerJsonFileContent(string $url, string $reference): string
    {
        try {
            $repository = Repository::createFromRepositoryUrl($url);
            /** @var Repo $repoApi */
            $repoApi = $this->githubClient->api('repo');
            $fileInfo = (array) $repoApi
                ->contents()
                ->show($repository->getUsername(), $repository->getName(), 'composer.json', $reference);

            $composerJsonFileContent = $this->doDownloadHttpRequest((string) $fileInfo['download_url']);

            return $composerJsonFileContent;
        } catch (\Exception $exception) {
            throw new MetadataReaderException('Impossible to read metadata from: ' . $url . ' error: ' . $exception->getMessage());
        }
    }

    private function getDefaultBranchName(Repository $repository): ? string
    {
        $data = [];
        try {
            $data = $this->githubClient->repo()->show($repository->getUsername(), $repository->getName());
        } catch (\Exception $exception) {
            throw $exception;
        }

        if (! \array_key_exists('default_branch', $data)) {
            return null;
        }

        return (string) $data['default_branch'];
    }
}

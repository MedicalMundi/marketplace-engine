<?php

namespace Metadata\AdapterForReadingExternalMetadataSource;

use Github\Client as GithubClient;
use Metadata\Core\Port\Driven\ForReadingExternalMetadataSource\ExternalMetadataDto;
use Metadata\Core\Port\Driven\ForReadingExternalMetadataSource\ForReadingExternalMetadataSource;
use Metadata\Core\Port\Driven\ForReadingExternalMetadataSource\MetadataReaderException;

class GithubAdapterForReadingExternalMetadataSource implements ForReadingExternalMetadataSource
{

    public function __construct(private readonly GithubClient $githubClient)
    {
    }

    public function readMetadataFromExternalSource(string $moduleUrl): ExternalMetadataDto
    {

        try {
            // TODO: Implement readMetadataFromExternalSource() method.
            //get composer.json
            // extract metadata section
            // create metadataDto
            return new ExternalMetadataDto(true,'a category', ['tag-1','tag-2']);
        }catch (\RuntimeException $exception){
            throw new MetadataReaderException($exception->getMessage());
        }
    }
}
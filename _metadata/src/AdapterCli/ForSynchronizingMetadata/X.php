<?php

namespace Metadata\AdapterCli\ForSynchronizingMetadata;

use Ecotone\Messaging\Attribute\ConsoleCommand;
use Ecotone\Messaging\Attribute\Parameter\Reference;
use Metadata\Core\Port\Driver\ForSynchronizingMetadata\ForSynchronizingMetadata;

class X
{
    //#[ConsoleCommand('metadata:module:update')]
    public function execute( #[Reference] ForSynchronizingMetadata $updater): void
    {
        $moduleId='foo';
        $updater->synchronizeMetadataFor($moduleId);
    }
}
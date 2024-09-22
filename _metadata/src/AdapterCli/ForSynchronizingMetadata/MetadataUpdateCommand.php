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

namespace Metadata\AdapterCli\ForSynchronizingMetadata;

use Ecotone\Messaging\Attribute\ConsoleCommand;
use Ecotone\Modelling\CommandBus;
use Metadata\Core\Process\StartModuleMetadataUpdate;

class MetadataUpdateCommand
{
    #[ConsoleCommand('metadata:module:update')]
    public function execute(CommandBus $commandBus): void
    {
        $moduleId = 'foo';

        // with error
        //$repoUrl = 'https://github.com/zerai/foo';

        // without metadata
        //$repoUrl = 'https://github.com/zerai/oe-module-demo-farm-add-ons';

        // with metadata
        $repoUrl = 'https://github.com/MedicalMundi/oe-module-todo-list';

        $command = new StartModuleMetadataUpdate($moduleId, $repoUrl);

        $commandBus->send($command);
    }
}

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

class ComposerJsonFile
{
    private function __construct(
        private string $source,
    ) {
    }

    public static function createFromJson(string $json): self
    {
        /**
         * TODO: check if valid json
         */
        return new self($json);
    }

    /**
     * TODO: refactor
     */
    public function hasMetadata(): bool
    {
        $result = false;

        $data = (array) json_decode($this->source, true);

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

    public function getMetadata(): array
    {
        $data = (array) json_decode($this->source, true);

        /** @psalm-suppress MixedArrayAccess */
        return (array) $data['extra']['openemr-module']['metadata']['oe-modules.com'];
    }
}

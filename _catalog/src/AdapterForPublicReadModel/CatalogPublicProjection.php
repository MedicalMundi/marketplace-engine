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

namespace Catalog\AdapterForPublicReadModel;

use Catalog\Core\Catalog\PublicModuleWasAdded;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Types\Types;
use Ecotone\EventSourcing\Attribute\Projection;
use Ecotone\EventSourcing\Attribute\ProjectionDelete;
use Ecotone\EventSourcing\Attribute\ProjectionInitialization;
use Ecotone\EventSourcing\Attribute\ProjectionReset;
use Ecotone\Modelling\Attribute\EventHandler;
use Ecotone\Modelling\Attribute\QueryHandler;

/**
 * TODO: add integration test
 */
#[Projection("catalog.public.moduleList", 'catalog.catalog_stream')]
class CatalogPublicProjection
{
    public const TABLE_NAME = "prj_catalog_public_module_list";

    public function __construct(
        private readonly Connection $connection
    ) {
    }

    #[EventHandler]
    public function onPublicModuleWasAdded(PublicModuleWasAdded $event): void
    {
        $this->connection->insert(self::TABLE_NAME, [
            "module_id" => $event->id->toString(),
            "package_name" => $event->packageName,
            "description" => $event->description,
            "url" => $event->url,
            "module_type" => $event->type,
        ]);
    }

    #[QueryHandler("catalog.public.getModuleList")]
    public function getModuleList(): array
    {
        $sql = 'SELECT * FROM ' . self::TABLE_NAME;

        return $this->connection->executeQuery($sql)->fetchAllAssociative();
    }

    #[QueryHandler("catalog.public.getModuleByPackageName")]
    public function getModuleByPackageName(string $packageName): array|bool
    {
        $sql = 'SELECT * FROM ' . self::TABLE_NAME . ' WHERE package_name = :package_name';
        return $this->connection->executeQuery($sql, [
            "package_name" => $packageName,
        ])->fetchAssociative();
    }

    #[ProjectionInitialization]
    public function initializeProjection(): void
    {
        if ($this->connection->createSchemaManager()->tablesExist([self::TABLE_NAME])) {
            return;
        }

        $table = new Table(self::TABLE_NAME);

        $table->addColumn('module_id', Types::STRING);
        $table->addColumn('package_name', Types::STRING);
        $table->addColumn('description', Types::STRING);
        $table->addColumn('url', Types::STRING);
        $table->addColumn('module_type', Types::STRING);
        $table->addColumn('category', Types::STRING, [
            'length' => 100,
            'default' => '',
        ]);
        $table->addColumn('tags', Types::STRING, [
            'length' => 255,
            'default' => '',
        ]);

        $table->setPrimaryKey(["module_id"]);
        $table->addUniqueIndex(["package_name"]);

        $this->connection->createSchemaManager()->createTable($table);
    }

    #[ProjectionReset]
    public function resetProjection(): void
    {
        $sql = 'DELETE FROM ' . self::TABLE_NAME;

        $this->connection->executeQuery($sql);
    }

    #[ProjectionDelete]
    public function delete(): void
    {
        $sql = 'DROP TABLE ' . self::TABLE_NAME;
        $this->connection->executeQuery($sql);
    }
}

<?php declare(strict_types=1);

namespace Sidworks\ProductFlipImage\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1776902400AddContextPhoto extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1776902400;
    }

    public function update(Connection $connection): void
    {
        $schemaManager = $connection->createSchemaManager();
        $columns = array_map(fn($col) => $col->getName(), $schemaManager->listTableColumns('product'));

        if (!in_array('context_photo_id', $columns, true)) {
            $connection->executeStatement('
                ALTER TABLE `product`
                ADD COLUMN `context_photo_id` BINARY(16) NULL
            ');
        }

        $foreignKeys = array_map(fn($fk) => $fk->getName(), $schemaManager->listTableForeignKeys('product'));
        if (!in_array('fk.product.context_photo_id', $foreignKeys, true)) {
            $connection->executeStatement('
                ALTER TABLE `product`
                ADD CONSTRAINT `fk.product.context_photo_id` FOREIGN KEY (`context_photo_id`)
                REFERENCES `product_media` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
            ');
        }

        $indexes = $schemaManager->listTableIndexes('product');
        if (!isset($indexes['idx_context_photo_id'])) {
            $connection->executeStatement('
                ALTER TABLE `product`
                ADD INDEX `idx_context_photo_id` (`context_photo_id`)
            ');
        }

        if (!isset($indexes['idx_context_photo_id_active'])) {
            $connection->executeStatement('
                ALTER TABLE `product`
                ADD INDEX `idx_context_photo_id_active` (`context_photo_id`, `active`)
            ');
        }
    }
}

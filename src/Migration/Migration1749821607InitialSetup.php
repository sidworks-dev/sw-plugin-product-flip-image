<?php declare(strict_types=1);

namespace Sidworks\ProductFlipImage\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1749821607InitialSetup extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1749821607;
    }

    public function update(Connection $connection): void
    {
        $schemaManager = $connection->createSchemaManager();
        $columns = array_map(fn($col) => $col->getName(), $schemaManager->listTableColumns('product'));

        if (!in_array('flip', $columns, true) || !in_array('flip_id', $columns, true)) {
            $connection->executeStatement('
            ALTER TABLE `product`
            ADD COLUMN IF NOT EXISTS `flip` BINARY(16),
            ADD COLUMN IF NOT EXISTS `flip_id` BINARY(16) NULL
        ');
        }

        $foreignKeys = array_map(fn($fk) => $fk->getName(), $schemaManager->listTableForeignKeys('product'));
        if (!in_array('fk.product.flip_id', $foreignKeys, true)) {
            $connection->executeStatement('
            ALTER TABLE `product`
            ADD CONSTRAINT `fk.product.flip_id` FOREIGN KEY (`flip_id`)
            REFERENCES `product_media` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
        ');
        }
    }
}

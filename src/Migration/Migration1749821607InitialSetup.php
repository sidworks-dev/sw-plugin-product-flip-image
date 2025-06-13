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
        $connection->executeStatement('
        ALTER TABLE `product`
        ADD COLUMN `flip` BINARY(16),
        ADD COLUMN `flip_id` BINARY(16) NULL,
        ADD CONSTRAINT `fk.product.flip_id` FOREIGN KEY (`flip_id`)
            REFERENCES `product_media` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
        ');

    }
}

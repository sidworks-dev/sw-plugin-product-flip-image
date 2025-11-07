<?php declare(strict_types=1);

namespace Sidworks\ProductFlipImage\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1749821608AddPerformanceIndexes extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1749821608;
    }

    public function update(Connection $connection): void
    {
        $connection->executeStatement('
                ALTER TABLE `product`
                ADD INDEX `idx_flip_id` (`flip_id`)
            ');

        $connection->executeStatement('
                ALTER TABLE `product`
                ADD INDEX `idx_flip_id_active` (`flip_id`, `active`)
            ');
    }
}

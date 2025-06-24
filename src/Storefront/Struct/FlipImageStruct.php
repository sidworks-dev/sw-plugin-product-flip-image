<?php
declare(strict_types=1);

namespace Sidworks\ProductFlipImage\Storefront\Struct;

use Shopware\Core\Framework\Struct\Struct;

class FlipImageStruct extends Struct
{
    public function __construct(
        protected readonly string $url
    ) {}

    public function getUrl(): string
    {
        return $this->url;
    }
}

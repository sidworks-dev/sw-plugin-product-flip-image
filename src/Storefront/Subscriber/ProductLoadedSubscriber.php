<?php declare(strict_types=1);

namespace Sidworks\ProductFlipImage\Storefront\Subscriber;

use Shopware\Core\Content\Product\Events\ProductListingCriteriaEvent;
use Shopware\Core\Content\Product\ProductEvents;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityLoadedEvent;
use Shopware\Storefront\Page\Product\ProductPageCriteriaEvent;
use Sidworks\ProductFlipImage\Struct\FlipImageStruct;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ProductLoadedSubscriber implements EventSubscriberInterface
{
    private const SIDWORKS_PRODUCT_FLIP_IMAGE_EXTENSION = 'sidworksProductFlipImage';

    public static function getSubscribedEvents(): array
    {
        return [
            ProductPageCriteriaEvent::class => 'onProductCriteriaLoaded',
            ProductEvents::PRODUCT_LISTING_CRITERIA => 'onProductListingCriteria',
            ProductEvents::PRODUCT_LOADED_EVENT => 'productLoaded',
        ];
    }

    public function onProductCriteriaLoaded(ProductPageCriteriaEvent $event): void
    {
        $event->getCriteria()->addAssociation('flip');
    }

    public function onProductListingCriteria(ProductListingCriteriaEvent $event): void
    {
        $event->getCriteria()->addAssociation('flip');
    }

    public function productLoaded(EntityLoadedEvent $event): void
    {
        foreach ($event->getEntities() as $product) {
            /** @var \Shopware\Core\Content\Product\ProductEntity $product */
            $flipImage = $product->getExtension('flip');

            if (!$flipImage || !$flipImage->getMedia()) {
                continue;
            }

            $media = $flipImage->getMedia();

            $product->addExtension(self::SIDWORKS_PRODUCT_FLIP_IMAGE_EXTENSION, $media);
        }
    }
}

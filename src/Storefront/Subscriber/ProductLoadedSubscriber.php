<?php declare(strict_types=1);

namespace Sidworks\ProductFlipImage\Storefront\Subscriber;

use Shopware\Core\Content\Product\Events\ProductListingCriteriaEvent;
use Shopware\Core\Content\Product\ProductEvents;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityLoadedEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\NotFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Storefront\Page\Product\ProductPageCriteriaEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ProductLoadedSubscriber implements EventSubscriberInterface
{
    private const SIDWORKS_PRODUCT_FLIP_IMAGE_EXTENSION = 'sidworksProductFlipImage';

    public function __construct(
        private readonly EntityRepository $productMediaRepository
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            ProductPageCriteriaEvent::class => 'onProductCriteriaLoaded',
            ProductEvents::PRODUCT_LOADED_EVENT => 'productLoaded',
        ];
    }

    public function onProductCriteriaLoaded(ProductPageCriteriaEvent $event): void
    {
        // Only load for detail page where it's more likely to be used
        $event->getCriteria()->addAssociation('flip');
    }

    // Remove onProductListingCriteria - don't preload for listings

    public function productLoaded(EntityLoadedEvent $event): void
    {
        $productsWithFlipIds = [];

        // First pass: identify products that have flip IDs
        foreach ($event->getEntities() as $product) {
            $flipId = $product->get('flipId');
            if ($flipId) {
                $productsWithFlipIds[$product->getId()] = $flipId;
            }
        }

        if (empty($productsWithFlipIds)) {
            return; // No flip images to load
        }

        // Single batch query to fetch all flip images at once
        $criteria = new Criteria(array_values($productsWithFlipIds));
        $criteria->addAssociation('media');

        $flipImages = $this->productMediaRepository->search(
            $criteria,
            $event->getContext()
        );

        // Second pass: attach flip images to products
        foreach ($event->getEntities() as $product) {
            $flipId = $productsWithFlipIds[$product->getId()] ?? null;

            if (!$flipId) {
                continue;
            }

            $flipImage = $flipImages->get($flipId);

            if (!$flipImage || !$flipImage->getMedia()) {
                continue;
            }

            $product->addExtension(
                self::SIDWORKS_PRODUCT_FLIP_IMAGE_EXTENSION,
                $flipImage->getMedia()
            );
        }
    }
}

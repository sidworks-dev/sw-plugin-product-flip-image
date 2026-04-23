<?php declare(strict_types=1);

namespace Sidworks\ProductFlipImage\Storefront\Subscriber;

use Shopware\Core\Content\Product\Aggregate\ProductMedia\ProductMediaEntity;
use Shopware\Core\Content\Product\Events\ProductListingResultEvent;
use Shopware\Core\Content\Product\ProductEvents;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityLoadedEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ProductLoadedSubscriber implements EventSubscriberInterface
{
    private const SIDWORKS_PRODUCT_FLIP_IMAGE_EXTENSION = 'sidworksProductFlipImage';
    private const SIDWORKS_PRODUCT_CONTEXT_IMAGE_EXTENSION = 'sidworksProductContextImage';
    private const SIDWORKS_PRODUCT_CONTEXT_PRODUCT_MEDIA_EXTENSION = 'sidworksProductContextProductMedia';
    private const PRODUCT_MEDIA_EXTENSION_MAPPING = [
        self::SIDWORKS_PRODUCT_FLIP_IMAGE_EXTENSION => 'flipId',
        self::SIDWORKS_PRODUCT_CONTEXT_IMAGE_EXTENSION => 'contextPhotoId',
    ];

    public function __construct(
        private readonly EntityRepository $productMediaRepository
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            ProductEvents::PRODUCT_LISTING_RESULT => 'onProductListingResult',
            ProductEvents::PRODUCT_SEARCH_RESULT => 'onProductListingResult',
            ProductEvents::PRODUCT_SUGGEST_RESULT => 'onProductListingResult',
            ProductEvents::PRODUCT_LOADED_EVENT => 'productLoaded',
        ];
    }

    public function onProductListingResult(ProductListingResultEvent $event): void
    {
        foreach ($event->getResult()->getEntities() as $product) {
            $contextProductMedia = $product->getExtension(self::SIDWORKS_PRODUCT_CONTEXT_PRODUCT_MEDIA_EXTENSION);

            if (!$contextProductMedia instanceof ProductMediaEntity) {
                continue;
            }

            $product->setCover($contextProductMedia);
        }
    }

    public function productLoaded(EntityLoadedEvent $event): void
    {
        $productExtensionMediaIds = [];
        $productMediaIds = [];

        // First pass: identify product-media ids we need to resolve.
        foreach ($event->getEntities() as $product) {
            foreach (self::PRODUCT_MEDIA_EXTENSION_MAPPING as $extensionName => $fieldName) {
                $productMediaId = $product->get($fieldName);

                if (!$productMediaId) {
                    continue;
                }

                $productExtensionMediaIds[$product->getId()][$extensionName] = $productMediaId;
                $productMediaIds[$productMediaId] = $productMediaId;
            }
        }

        if (empty($productMediaIds)) {
            return; // No extension images to load
        }

        // Single batch query to fetch all product-media entities at once.
        $criteria = new Criteria(array_values($productMediaIds));
        $criteria->addAssociation('media');

        $productMedia = $this->productMediaRepository->search(
            $criteria,
            $event->getContext()
        );

        // Second pass: attach resolved media entities to products.
        foreach ($event->getEntities() as $product) {
            $extensionMediaIds = $productExtensionMediaIds[$product->getId()] ?? null;
            if (!$extensionMediaIds) {
                continue;
            }

            foreach ($extensionMediaIds as $extensionName => $productMediaId) {
                $extensionProductMedia = $productMedia->get($productMediaId);

                if (!$extensionProductMedia || !$extensionProductMedia->getMedia()) {
                    continue;
                }

                $product->addExtension(
                    $extensionName,
                    $extensionProductMedia->getMedia()
                );

                if ($extensionName === self::SIDWORKS_PRODUCT_CONTEXT_IMAGE_EXTENSION) {
                    $product->addExtension(
                        self::SIDWORKS_PRODUCT_CONTEXT_PRODUCT_MEDIA_EXTENSION,
                        $extensionProductMedia
                    );
                }
            }
        }
    }
}

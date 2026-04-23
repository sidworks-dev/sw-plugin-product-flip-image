import template from './sw-product-media-form.html.twig';

Shopware.Component.override('sw-product-media-form', {
    template,

    methods: {
        isFlip(mediaItem) {
            return this.product.flipId === mediaItem.id;
        },

        isContextPhoto(mediaItem) {
            return this.product.contextPhotoId === mediaItem.id;
        },

        markMediaAsFlip(productMedia) {
            this.product.flipId = productMedia.id;
        },

        removeFlip(productMedia) {
            this.product.flipId = null;
        },

        markMediaAsContextPhoto(productMedia) {
            this.product.contextPhotoId = productMedia.id;
        },

        removeContextPhoto(productMedia) {
            this.product.contextPhotoId = null;
        }
    }
});

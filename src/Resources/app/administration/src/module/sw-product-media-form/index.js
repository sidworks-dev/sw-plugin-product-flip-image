import template from './sw-product-media-form.html.twig';

Shopware.Component.override('sw-product-media-form', {
    template,
    methods: {
        isFlip(mediaItem) {
            if (this.product.flipId == mediaItem.id) {
                return true;
            }
        },

        markMediaAsFlip(productMedia) {
            this.product.flipId = productMedia.id;
        },
    }
});

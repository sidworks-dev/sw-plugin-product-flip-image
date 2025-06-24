import template from './sw-product-image.html.twig';
import './sw-product-image.scss';

Shopware.Component.override('sw-product-image', {
    template,

    compatConfig: Shopware.compatConfig,

    emits: [
        'sw-product-image-flip',
    ],

    props: {
        isFlip: {
            type: Boolean,
            required: false,
            default: false,
        },
    }
});

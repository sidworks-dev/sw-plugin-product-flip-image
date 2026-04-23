import template from './sw-product-image.html.twig';
import './sw-product-image.scss';

Shopware.Component.override('sw-product-image', {
    template,

    compatConfig: Shopware.compatConfig,

    emits: [
        'sw-product-image-cover',
        'sw-product-image-delete',
        'sw-product-image-flip',
        'sw-product-image-flip-delete',
        'sw-product-image-context-photo',
        'sw-product-image-context-photo-delete',
    ],

    props: {
        isFlip: {
            type: Boolean,
            required: false,
            default: false,
        },

        isContextPhoto: {
            type: Boolean,
            required: false,
            default: false,
        },
    }
});

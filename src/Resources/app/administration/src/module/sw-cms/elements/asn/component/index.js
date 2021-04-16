import template from './sw-cms-el-preview-asn.html.twig';

Shopware.Component.register('sw-cms-el-asn', {
    template,
    mixins: [
        'cms-element',
    ],
    created() {
        this.createdComponent();
    },

    methods: {
        createdComponent() {
            this.initElementConfig('asn');
        },
    },
});

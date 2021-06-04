import template from './sw-cms-el-asn.html.twig';

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

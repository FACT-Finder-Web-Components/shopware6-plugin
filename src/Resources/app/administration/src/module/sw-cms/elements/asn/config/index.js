import template from './sw-cms-el-config-asn.html.twig';

Shopware.Component.register('sw-cms-el-config-asn', {
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

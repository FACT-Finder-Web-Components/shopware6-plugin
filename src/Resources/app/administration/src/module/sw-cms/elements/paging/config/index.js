import template from './sw-cms-el-config-paging.html.twig';

Shopware.Component.register('sw-cms-el-config-paging', {
    template,

    mixins: [
        'cms-element',
    ],

    created() {
        this.createdComponent();
    },

    methods: {
        createdComponent() {
            this.initElementConfig('paging');
        },
    },
});

import template from './sw-cms-el-preview-paging.html.twig';

Shopware.Component.register('sw-cms-el-paging', {
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

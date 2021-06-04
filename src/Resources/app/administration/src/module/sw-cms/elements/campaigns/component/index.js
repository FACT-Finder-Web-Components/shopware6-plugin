import template from './sw-cms-el-campaigns.html.twig';

Shopware.Component.register('sw-cms-el-campaigns', {
    template,
    mixins: [
        'cms-element',
    ],
    created() {
        this.createdComponent();
    },

    methods: {
        createdComponent() {
            this.initElementConfig('campaigns');
        },
    },
});

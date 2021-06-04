import template from './sw-cms-el-config-campaigns.html.twig';

Shopware.Component.register('sw-cms-el-config-campaigns', {
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

import template from './sw-cms-el-config-campaigns.html.twig';

Shopware.Component.register('sw-cms-el-config-campaigns', {
    template,

    mixins: [
        'cms-element',
    ],

    created() {
        this.createdComponent();
    },

    data() {
        return {
            campaignFlags: ['None','is-product-campaign', 'is-landing-page-campaign'],
        };
    },

    methods: {
        createdComponent() {
            this.initElementConfig('campaigns');
        },
    },
});

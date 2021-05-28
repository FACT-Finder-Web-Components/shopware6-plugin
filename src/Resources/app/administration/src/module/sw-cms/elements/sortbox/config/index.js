import template from './sw-cms-el-config-sortbox.html.twig';

Shopware.Component.register('sw-cms-el-config-sortbox', {
    template,

    mixins: [
        'cms-element',
    ],
    created() {
        this.createdComponent();
    },

    methods: {
        createdComponent() {
            this.initElementConfig('sortbox');
        },
    },
});

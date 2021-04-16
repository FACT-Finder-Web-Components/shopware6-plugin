import template from './sw-cms-el-preview-sortbox.html.twig';

Shopware.Component.register('sw-cms-el-sortbox', {
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

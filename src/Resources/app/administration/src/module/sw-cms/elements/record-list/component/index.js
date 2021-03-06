import template from './sw-cms-el-record-list.html.twig';

Shopware.Component.register('sw-cms-el-record-list', {

    template,
    mixins: [
        'cms-element',
    ],
    created() {
        this.createdComponent();
    },

    methods: {
        createdComponent() {
            this.initElementConfig('record-list');
        },
    },
});

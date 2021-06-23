import template from './ui-feed-export-index.html.twig'

const { Component, Mixin } = Shopware;

Component.register('ui-feed-export-index', {
    template,

    mixins: [
        Mixin.getByName('notification')
    ],

    metaInfo() {
        return {
            title: this.$createTitle()
        };
    },
});

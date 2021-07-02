import template from './ui-feed-export-index.html.twig'

const { Component } = Shopware;

Component.register('ui-feed-export-index', {
    template,

    metaInfo() {
        return {
            title: this.$createTitle()
        };
    },
});

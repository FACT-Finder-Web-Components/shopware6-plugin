import template from './ui-feed-export-button.html.twig';

const { Component } = Shopware;


Component.register('ui-feed-export-button', {
    template,

    methods: {
        getFeedExportFile(url) {
            alert(url);
        }
    }
});

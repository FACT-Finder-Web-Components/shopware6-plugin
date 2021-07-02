import './component/ui-feed-export-form'
import './page/ui-feed-export-index'

import deDE from './snippet/de-DE.json';
import enGB from './snippet/en-GB.json';

const { Module } = Shopware;

Module.register('ui-feed-export', {
    color: '#ff3d58',
    icon: 'default-shopping-paper-bag-product',
    title: 'ui-feed-export.title',
    description: '',

    snippets: {
        'de-DE': deDE,
        'en-GB': enGB
    },

    routes: {
        index: {
            component: 'ui-feed-export-index',
            path: 'index'
        }
    },

    navigation: [{
            label: 'ui-feed-export.title',
            path: 'ui.feed.export.index',
            position: 100,
            parent: 'sw-extension',
        }],
    });

import './component';
import './config';
import './preview';

Shopware.Service('cmsService').registerCmsElement({
    name: 'record-list',
    label: 'sw-cms.elements.recordList.label',
    component: 'sw-cms-el-record-list',
    configComponent: 'sw-cms-el-config-record-list',
    previewComponent: 'sw-cms-el-preview-record-list',
    defaultConfig: {
        subscribe: {
          value: true,
          source: 'static'
        },
        infiniteScrolling: {
            value: false,
            source: 'static',
        },
        restoreScrollPosition: {
            value: false,
            source: 'static',
        },
        infiniteDebounceDelay: {
            value: '32',
            source: 'static',
        },
        infiniteScrollMargin: {
            value: 0,
            source: 'static',
        },
        callback: {
            value: '',
            source: 'static'
        },
        id: {
            value: '',
            source: 'static'
        },
        domUpdated: {
            value: '',
            source: 'static'
        }
    },
});

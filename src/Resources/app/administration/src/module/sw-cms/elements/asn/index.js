import './component';
import './config';
import './preview';

Shopware.Service('cmsService').registerCmsElement({
    name: 'asn',
    label: 'sw-cms.elements.asn.label',
    component: 'sw-cms-el-asn',
    configComponent: 'sw-cms-el-config-asn',
    previewComponent: 'sw-cms-el-preview-asn',
    defaultConfig: {
        subscribe: {
            value: true,
            source: 'static'
        },
        vertical: {
            value: false,
            source: 'static'
        },
        topic: {
            value: 'asn',
            source: 'static'
        },
        callbackArg: {
            value: 'groups',
            source: 'static'
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
        },
        filterCloud: {
            value: true,
            source: 'static'
        },
        filterCloudBlacklist: {
            value: '',
            source: 'static'
        },
        filterCloudWhitelist: {
            value: '',
            source: 'static'
        },
        filterCloudOrder: {
            value: 'fact-finder',
            source: 'static'
        },
    }
});

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
        filterCloud: {
            value: true,
            source: 'static'
        },
        filterCloudBlacklist: {
            value: '',
            source: 'static'
        }
    }
});

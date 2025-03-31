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

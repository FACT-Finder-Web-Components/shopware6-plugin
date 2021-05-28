import './component';
import './config';
import './preview';

Shopware.Service('cmsService').registerCmsElement({
    name: 'paging',
    label: 'sw-cms.elements.paging.label',
    component: 'sw-cms-el-paging',
    configComponent: 'sw-cms-el-config-paging',
    previewComponent: 'sw-cms-el-preview-paging',
    defaultConfig: {
        subscribe: {
            value: true,
            source: 'static',
        },
        showOnly: {
            value: 'true',
            source: 'static',
        },
    },
});

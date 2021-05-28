import './component';
import './config';
import './preview';

Shopware.Service('cmsService').registerCmsElement({
    name: 'sortbox',
    label: 'sw-cms.elements.sortbox.label',
    component: 'sw-cms-el-sortbox',
    configComponent: 'sw-cms-el-config-sortbox',
    previewComponent: 'sw-cms-el-preview-sortbox',
    defaultConfig: {
        subscribe: {
            value: true,
            source: 'static',
        },
        opened: {
            value: true,
            source: 'static',
        },
        showSelected: {
            value: false,
            source: 'static',
        },
        showSelectedFirst: {
            value: false,
            source: 'static',
        },
        collapseOnblur: {
            value: false,
            source: 'static',
        },
    },
});

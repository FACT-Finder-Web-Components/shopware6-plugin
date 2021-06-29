import './component';
import './preview';

Shopware.Service('cmsService').registerCmsBlock({
    name: 'filters',
    label: 'sw-cms.blocks.commerce.factfinderWebComponentsFilters.label',
    category: 'commerce',
    component: 'sw-cms-block-filters',
    previewComponent: 'sw-cms-block-filters-preview',
    defaultConfig: {
        cssClass: 'cms-block-sidebar-filter'
    },
    slots: {
        filters: 'asn'
    }
});

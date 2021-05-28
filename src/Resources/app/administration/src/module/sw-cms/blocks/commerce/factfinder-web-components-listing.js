import './component';
import './preview';

Shopware.Service('cmsService').registerCmsBlock({
    name: 'factfinder-web-components-listing',
    label: 'sw-cms.blocks.commerce.factfinderWebComponentsListing.label',
    category: 'commerce',
    component: 'sw-cms-block-factfinder-web-components-listing',
    previewComponent: 'cms-preview-factfinder-web-components-listing',
    slots: {
        toolbarFilters: 'asn',
        toolbarPaging: 'paging',
        toolbarSorting: 'sortbox',
        center: 'record-list',
    }
});

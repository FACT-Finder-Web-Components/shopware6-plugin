import './component';
import './preview';

Shopware.Service('cmsService').registerCmsBlock({
    name: 'listing',
    label: 'sw-cms.blocks.commerce.factfinderWebComponentsListing.label',
    category: 'commerce',
    component: 'sw-cms-block-listing',
    previewComponent: 'sw-cms-block-listing-preview',
    slots: {
        toolbarFilters: 'asn',
        toolbarPaging: 'paging',
        toolbarSorting: 'sortbox',
        records: 'record-list',
    }
});

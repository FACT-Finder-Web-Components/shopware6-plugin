import './component';
import './preview';

Shopware.Service('cmsService').registerCmsBlock({
    name: 'campaigns',
    label: 'sw-cms.blocks.commerce.factfinderWebComponentsCampaigns.label',
    category: 'commerce',
    component: 'sw-cms-block-campaigns',
    previewComponent: 'sw-cms-block-campaigns-preview',
    slots: {
        campaigns: 'campaigns'
    }
});

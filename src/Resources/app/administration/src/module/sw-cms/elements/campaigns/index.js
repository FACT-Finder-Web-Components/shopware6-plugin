import './component';
import './config';
import './preview';

Shopware.Service('cmsService').registerCmsElement({
    name: 'campaigns',
    label: 'sw-cms.elements.campaigns.label',
    component: 'sw-cms-el-campaigns',
    configComponent: 'sw-cms-el-config-campaigns',
    previewComponent: 'sw-cms-el-preview-campaigns',
    defaultConfig: {
        advisorCampaignName: {
            value: '',
            source: 'static'
        },
        feedbackCampaignLabel: {
            value: '',
            source: 'static'
        },
    }
});

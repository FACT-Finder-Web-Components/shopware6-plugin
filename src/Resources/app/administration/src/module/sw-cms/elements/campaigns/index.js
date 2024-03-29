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
        feedbackCampaignFlag: {
            value: '',
            source: 'static'
        },
        enableFeedbackCampaign: {
            value: false,
            source: 'static'
        },
        enableAdvisorCampaign: {
            value: false,
            source: 'static'
        },
        enableRedirectCampaign: {
            value: false,
            source: 'static'
        },
        enablePushedProducts: {
            value: false,
            source: 'static'
        },
        pushedProductsFlag: {
            value: '',
            source: 'static'
        },
        pushedProductsName: {
            value: '',
            source: 'static'
        },
    }
});

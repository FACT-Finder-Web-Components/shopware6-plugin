import template from './ui-feed-export-form.html.twig';

const {Component} = Shopware;

Component.register('ui-feed-export-form', {
    template,

    data() {
        return {
            salesChannelValue: null,
            salesChannelLanguageValue: null,
            successAlertVisible: false,
            errorAlertVisible: false
        }
    },

    methods: {
        getFeedExportFile(url) {
            const httpClient = Shopware.Service('syncService').httpClient;
            const basicHeaders = {
                Authorization: `Bearer ${Shopware.Context.api.authToken.access}`,
                'Content-Type': 'application/json'
            };
            const params = {
                salesChannelValue: this.salesChannelValue,
                salesChannelLanguageValue: this.salesChannelLanguageValue
            };

            httpClient
                .get(url, {
                    headers: basicHeaders,
                    params: params
                })
                .then((response) => {
                    if (response.status === 200) {
                        this.successAlertVisible = true;
                    } else {
                        this.errorAlertVisible = true;
                    }
                });
        }
    }
});

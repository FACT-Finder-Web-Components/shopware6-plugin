import template from './ui-feed-export-form.html.twig';

const {Component} = Shopware;

Component.register('ui-feed-export-button', {
    template,

    methods: {
        getFeedExportFile(url) {
            const httpClient = Shopware.Service('syncService').httpClient;
            const basicHeaders = {
                Authorization: `Bearer ${Shopware.Context.api.authToken.access}`,
                'Content-Type': 'application/json'
            };
            const params = {};

            httpClient
                .get(url, {
                    headers: basicHeaders,
                    params: params
                })
                .then((response) => {
                    alert(response.data);
                });
        }
    }
});

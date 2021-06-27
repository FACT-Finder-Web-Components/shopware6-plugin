import template from './ui-feed-export-button.html.twig';

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
                    params: params,
                    responseType: 'blob'
                })
                .then((response) => {
                    const filename = response.headers['content-disposition'].split(";") [1].split("filename=")[1].trim();
                    const url = window.URL.createObjectURL(new Blob([response.data], {type: 'text'}));
                    const link = document.createElement('a');

                    console.log(response);
                    console.log(filename);

                    link.href = url;
                    link.setAttribute('download', filename);
                    document.body.appendChild(link);
                    link.click();
                })
            ;
        }
    }
});

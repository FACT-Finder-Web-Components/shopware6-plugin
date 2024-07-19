import template from './test-push-import-connection.html.twig';

const {Component, Mixin} = Shopware;

Component.register('test-push-import-connection', {
        template,
    mixins: [
            Mixin.getByName('notification'),
            Mixin.getByName('sw-inline-snippet'),
        ],
        data() {
            return {
                isLoading: false,
                isSaveSuccessful: false,
            };
    },

    methods: {
        async onClick() {
            this.isLoading = true;
            const httpClient = Shopware.Service('syncService').httpClient;
            const url = '_action/test-connection/push-import';
            const basicHeaders = {
                Authorization: `Bearer ${Shopware.Context.api.authToken.access}`,
                'Content-Type': 'application/json'
            };

            httpClient
                .get(url, {
                    headers: basicHeaders
                    })
                .then((response) => {
                    if (response.status === 200) {
                        this.createNotificationSuccess({
                            message: Shopware.Snippet.tc('configuration.testConnection.success')
                            });
                    } else {
                        this.createNotificationError({
                            title: Shopware.Snippet.tc('configuration.testConnection.fail'),
                            message: Shopware.Snippet.tc('configuration.testConnection.helpText')
                            });
                    }
                    })
                .catch(() => {
                    this.createNotificationError({
                        title: Shopware.Snippet.tc('configuration.testConnection.fail'),
                        message: Shopware.Snippet.tc('configuration.testConnection.helpText')
                        });
                })
                .finally(() => {
                    this.isSaveSuccessful = true;
                    this.isLoading = false;
                    });
        },
    },
    },);

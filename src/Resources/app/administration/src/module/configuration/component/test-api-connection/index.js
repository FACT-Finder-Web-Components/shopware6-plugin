import template from './test-api-connection.html.twig';
import './test-api-connection.scss';

const {Component, Mixin} = Shopware;

Component.register('test-api-connection', {
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
                const url = '_action/test-connection/api';
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
                                message: this.$tc('configuration.testConnection.success')
                            });
                        } else {
                            this.createNotificationError({
                                title: this.$tc('configuration.testConnection.fail'),
                                message: this.$tc('configuration.testConnection.helpText')
                            });
                        }
                    })
                    .catch(() => {
                        this.createNotificationError({
                            title: this.$tc('configuration.testConnection.fail'),
                            message: this.$tc('configuration.testConnection.helpText')
                        });
                    })
                    .finally(() => {
                        this.isSaveSuccessful = true;
                        this.isLoading = false;
                    });
            },
        },
    },
);

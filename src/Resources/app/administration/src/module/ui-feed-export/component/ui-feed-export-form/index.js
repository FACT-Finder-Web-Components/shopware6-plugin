import template from './ui-feed-export-form.html.twig';

const { Component, Mixin } = Shopware;

Component.register('ui-feed-export-form', {
    template,

    data() {
        return {
            salesChannelValue: null,
            salesChannelLanguageValue: null,
            exportTypeValue: null,
            typeSelectOptions: [],
        }
    },

    mixins: [
        Mixin.getByName('notification')
    ],
    mounted () {
        this.getExportTypeValues()
    },
    filters: {
        capitalize: function (value) {
            if (!value) return '';
            value = value.toString();
            return value.charAt(0).toUpperCase() + value.slice(1);
        }
    },
    methods: {
        getExportTypeValues() {
            const httpClient = Shopware.Service('syncService').httpClient;
            const url = '_action/fact-finder/get-export-type-options';
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
                        this.typeSelectOptions = response.data
                    }
                });
        },
        successFeedGenerationWindow() {
            this.createNotificationSuccess({
                message: this.$tc('ui-feed-export.component.export_form.alert_success.text')
            })
        },
        errorFeedGenerationWindow() {
            this.createNotificationError({
                message: this.$tc('ui-feed-export.component.export_form.alert_error.text')
            })
        },
        getFeedExportFile(url) {
            const httpClient = Shopware.Service('syncService').httpClient;
            const basicHeaders = {
                Authorization: `Bearer ${Shopware.Context.api.authToken.access}`,
                'Content-Type': 'application/json'
            };
            const params = {
                salesChannelValue: this.salesChannelValue,
                salesChannelLanguageValue: this.salesChannelLanguageValue,
                exportTypeValue: this.exportTypeValue
            };

            httpClient
                .get(url, {
                    headers: basicHeaders,
                    params: params
                })
                .then((response) => {
                    if (response.status === 200) {
                        this.successFeedGenerationWindow();
                    } else {
                        this.errorFeedGenerationWindow();
                    }
                });
        }
    }
});

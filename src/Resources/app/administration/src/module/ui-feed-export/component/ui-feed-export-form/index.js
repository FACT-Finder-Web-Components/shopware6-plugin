import template from './ui-feed-export-form.html.twig';

const { Component, Mixin } = Shopware;

Component.register('ui-feed-export-form', {
    template,

    inject: ['systemConfigApiService'],

    data() {
        return {
            salesChannelValue: null,
            salesChannelLanguageValue: null,
            exportTypeValue: null,
            typeSelectOptions: [],
            isCacheDisable: false,
        }
    },

    mixins: [
        Mixin.getByName('notification')
    ],
    mounted () {
        this.getPluginConfig()
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
         async getPluginConfig() {
             const config = await this.systemConfigApiService.getValues('OmikronFactFinder.config');
             this.isCacheDisable = config['OmikronFactFinder.config.enableExportCache'];
        },
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
        successRefreshCacheWindow() {
            this.createNotificationSuccess({
                message: this.$tc('ui-feed-export.component.export_form.refresh_cache_success.text')
            })
        },
        errorRefreshCacheWindow() {
            this.createNotificationError({
                message: this.$tc('ui-feed-export.component.export_form.refresh_cache_error.text')
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
        },
        refreshExportCache(url) {
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
                        this.successRefreshCacheWindow();
                    } else {
                        this.errorRefreshCacheWindow();
                    }
                });
        }
    }
});

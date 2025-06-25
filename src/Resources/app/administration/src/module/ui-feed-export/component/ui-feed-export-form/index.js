import { defineComponent, ref, onMounted } from 'vue';
import template from './ui-feed-export-form.html.twig';

const { Component, Mixin } = Shopware;

// Funkcja pomocnicza do kapitalizacji (zastępuje filtr)
const capitalize = (value) => {
    if (!value) {
        return '';
    }
    return value.charAt(0).toUpperCase() + value.slice(1);
};

// Definicja komponentu
Component.register('ui-feed-export-form', defineComponent({
    template,

    // Inject services
    inject: ['systemConfigApiService'],

    // Mixins
    mixins: [
        Mixin.getByName('notification'),
    ],

    // Composition API setup
    setup() {
        // Reaktywne dane
        const salesChannelValue = ref(null);
        const salesChannelLanguageValue = ref(null);
        const exportTypeValue = ref(null);
        const typeSelectOptions = ref([]);
        const isCacheDisable = ref(false);
        const isLoadingCache = ref(false);
        const isLoadingExport = ref(false);

        // Pobieranie konfiguracji pluginu
        const getPluginConfig = async() => {
            try {
                const config = await Shopware.Service('systemConfigApiService').getValues('OmikronFactFinder.config');
                isCacheDisable.value = config['OmikronFactFinder.config.enableExportCache'] || false;
            } catch (error) {
                console.error('Error fetching plugin config:', error);
                Shopware.Service('notificationService').createNotificationError({
                    message: Shopware.Service('localeHelper').translate('ui-feed-export.component.export_form.alert_error.text'),
                });
            }
        };

        // Pobieranie opcji typów eksportu
        const getExportTypeValues = async() => {
            const httpClient = Shopware.Context.api.httpClient; // Używamy api.httpClient zamiast syncService
            const url = '_action/fact-finder/get-export-type-options';
            const basicHeaders = {
                Authorization: `Bearer ${Shopware.Context.api.authToken.access}`,
                'Content-Type': 'application/json',
            };

            try {
                const response = await httpClient.get(url, { headers: basicHeaders });
                if (response.status === 200) {
                    typeSelectOptions.value = response.data || [];
                }
            } catch (error) {
                console.error('Error fetching export type options:', error);
                Shopware.Service('notificationService').createNotificationError({
                    message: Shopware.Service('localeHelper').translate('ui-feed-export.component.export_form.alert_error.text'),
                });
            }
        };

        // Powiadomienia
        const createNotificationSuccess = (message) => {
            Shopware.Service('notificationService').createNotificationSuccess({ message });
        };

        const createNotificationError = (message) => {
            Shopware.Service('notificationService').createNotificationError({ message });
        };

        // Funkcje powiadomień
        const successFeedGenerationWindow = () => {
            createNotificationSuccess(Shopware.Service('localeHelper').translate('ui-feed-export.component.export_form.alert_success.text'));
        };

        const errorFeedGenerationWindow = () => {
            createNotificationError(Shopware.Service('localeHelper').translate('ui-feed-export.component.export_form.alert_error.text'));
        };

        const errorNotValidParams = () => {
            createNotificationError(Shopware.Service('localeHelper').translate('ui-feed-export.component.export_form.alert_not_valid_params.text'));
        };

        const successRefreshCacheWindow = () => {
            createNotificationSuccess(Shopware.Service('localeHelper').translate('ui-feed-export.component.export_form.refresh_cache_success.text'));
        };

        const errorRefreshCacheWindow = () => {
            createNotificationError(Shopware.Service('localeHelper').translate('ui-feed-export.component.export_form.refresh_cache_error.text'));
        };

        // Walidacja parametrów
        const validateParams = (params) => {
            return !!(params.salesChannelValue && params.salesChannelLanguageValue && params.exportTypeValue);
        };

        // Pobieranie pliku eksportu
        const getFeedExportFile = async(url) => {
            const params = {
                salesChannelValue: salesChannelValue.value,
                salesChannelLanguageValue: salesChannelLanguageValue.value,
                exportTypeValue: exportTypeValue.value,
            };

            if (!validateParams(params)) {
                errorNotValidParams();
                return;
            }

            isLoadingExport.value = true;
            const httpClient = Shopware.Context.api.httpClient;
            const basicHeaders = {
                Authorization: `Bearer ${Shopware.Context.api.authToken.access}`,
                'Content-Type': 'application/json',
            };

            try {
                const response = await httpClient.get(url, { headers: basicHeaders, params });
                if (response.status === 200) {
                    successFeedGenerationWindow();
                } else {
                    errorFeedGenerationWindow();
                }
            } catch (error) {
                errorFeedGenerationWindow();
                console.error('Error generating feed:', error);
            } finally {
                isLoadingExport.value = false;
            }
        };

        // Odświeżanie cache
        const refreshExportCache = async(url) => {
            isLoadingCache.value = true;
            const httpClient = Shopware.Context.api.httpClient;
            const basicHeaders = {
                Authorization: `Bearer ${Shopware.Context.api.authToken.access}`,
                'Content-Type': 'application/json',
            };
            const params = {
                salesChannelValue: salesChannelValue.value,
                salesChannelLanguageValue: salesChannelLanguageValue.value,
            };

            try {
                const response = await httpClient.get(url, { headers: basicHeaders, params });
                if (response.status === 200) {
                    successRefreshCacheWindow();
                } else {
                    errorRefreshCacheWindow();
                }
            } catch (error) {
                errorRefreshCacheWindow();
                console.error('Error refreshing cache:', error);
            } finally {
                isLoadingCache.value = false;
            }
        };

        // Hook mounted
        onMounted(() => {
            getPluginConfig();
            getExportTypeValues();
        });

        // Eksport wartości i metod
        return {
            salesChannelValue,
            salesChannelLanguageValue,
            exportTypeValue,
            typeSelectOptions,
            isCacheDisable,
            isLoadingCache,
            isLoadingExport,
            capitalize,
            getFeedExportFile,
            refreshExportCache,
        };
    },
}));

import Plugin from 'src/plugin-system/plugin.class';
import DomAccessHelper from 'src/helper/dom-access.helper';

export default class TrackingPlugin extends Plugin
{
    init() {
        this.registerEvents();
    }

    registerEvents() {
        window.PluginManager.getPluginInstances('AddToCart')
              .forEach(pluginInstance => pluginInstance.$emitter.subscribe('beforeFormSubmit', this.trackAddToCart.bind(this)));
    }

    getQuantity(data)
    {
        if (ffTrackingSettings.addToCart.count === 'count_as_one') {
            return 1;
        }

        try {
            const quantityInput = DomAccessHelper.querySelector(data, '[name$="[quantity]"]');

            return parseInt(quantityInput.value);
        } catch (e) {
            return 1;
        }
    }

    async trackAddToCart(event) {
        const productNumberInput = DomAccessHelper.querySelector(event.target, '[name="product-number"]');
        const quantity = this.getQuantity(event.target);

        if (!productNumberInput) {
            return;
        }

        waitForFactFinder().then(factfinder => {
            const trackingHelper = factfinder.communication.Util.trackingHelper;
            factfinder.communication.EventAggregator.addFFEvent({
                type: 'getRecords',
                recordId: productNumberInput.value,
                idType: 'productNumber',

                success: ([product]) => {
                    const fieldRoles = factfinder.communication.fieldRoles;
                    const getMasterId = ({record}) => record[fieldRoles.masterArticleNumber] || record[fieldRoles.masterId];
                    const getTrackingNumber = ({record}) => record[fieldRoles.trackingProductNumber] || record[fieldRoles.productNumber]

                    factfinder.communication.Tracking.cart({
                        id: getTrackingNumber(product),
                        masterId: getMasterId(product),
                        price: trackingHelper.getPrice(product),
                        title: trackingHelper.getTitle(product),
                        count: quantity,
                        userId: factfinder.communication.globalCommunicationParameter.userId,
                    });
                },
            });
        });
    }
}

function waitForFactFinder() {
    return new Promise(resolve => {
        if (typeof window.factfinder !== 'undefined') {
            resolve(window.factfinder);
        } else {
            document.addEventListener('ffReady', event => resolve(event.factfinder));
        }
    });
}

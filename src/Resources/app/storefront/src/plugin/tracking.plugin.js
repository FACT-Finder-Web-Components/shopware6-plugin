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

    async trackAddToCart(event) {
        const productNumberInput = DomAccessHelper.querySelector(event.target, '[name="product-number"]');
        if (!productNumberInput) {
            return;
        }
        waitForFactFinder().then(factfinder => {
            const trackingHelper = factfinder.communication.Util.trackingHelper;
            factfinder.communication.EventAggregator.addFFEvent({
                type: 'getRecords',
                recordId: productNumberInput.value,
                idType: 'productNumber',
                success: ([product]) => factfinder.communication.Tracking.cart({
                    id: trackingHelper.getTrackingProductId(product),
                    masterId: trackingHelper.getMasterArticleNumber(product),
                    price: trackingHelper.getPrice(product),
                    count: 1,
                }),
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

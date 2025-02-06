import Plugin from 'src/plugin-system/plugin.class';
import DomAccessHelper from 'src/helper/dom-access.helper';

export default class TrackingPlugin extends Plugin
{
    init()
    {
        this.registerEvents();
    }

    registerEvents()
    {
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

    async trackAddToCart(event)
    {
        try {
            const productNumberInput = DomAccessHelper.querySelector(event.target, '[name="product-number"]');
            const quantity = this.getQuantity(event.target);

            waitForFactFinder().then(factfinder => {
                factfinder.request.records({ productNumber: [productNumberInput.value]}).then((result) => {
                    const product = result.records[0];

                    if (!product) {
                        return;
                    }

                    const getUserId = factfinder.config.get().ffParams ? factfinder.config.get().ffParams.userId : undefined;

                    let cartObj = {
                        id: product.ProductNumber,
                        masterId: product.Master,
                        price: product.Price,
                        title: product.Name,
                        count: quantity,
                        sid: JSON.parse(localStorage.ffwebco).sid,
                    }

                    if (getUserId) {
                        cartObj.userId = getUserId;
                    }

                    factfinder.tracking.cart([cartObj]);
                })
            });
        } catch (err) {
            // Do nothing - product number does not exist which means that adding to the cart is not done by record from FF
        }




    }
}

function waitForFactFinder()
{
    return new Promise(resolve => {
        if (typeof window.factfinder !== 'undefined') {
            resolve(window.factfinder);
        } else {
            document.addEventListener('ffCoreReady', event => resolve(event.factfinder));
        }
    });
}

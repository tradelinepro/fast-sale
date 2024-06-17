import FastSaleSearchPlugin from './fast-sale/search.plugin';
import FastSaleAddToCartPlugin from './fast-sale/fast-sale-add-to-cart.plugin';

const PluginManager = window.PluginManager;

PluginManager.register('FastSaleAddToCart', FastSaleAddToCartPlugin, '[data-fast-sale-add-to-cart]');
PluginManager.register('FastSaleSearch', FastSaleSearchPlugin, '[data-fast-sale-search]');

// Necessary for the webpack hot module reloading server
if (module.hot) {
    module.hot.accept();
}

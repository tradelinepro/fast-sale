import AddToCartPlugin from 'src/plugin/add-to-cart/add-to-cart.plugin.js';

export default class FastSaleAddToCartPlugin extends AddToCartPlugin {
    _openOffCanvasCarts(requestUrl, formData) {
        const offCanvasCartInstances = PluginManager.getPluginInstances('OffCanvasCart');
        if (offCanvasCartInstances.length > 0) {
            super._openOffCanvasCart(offCanvasCartInstances[0], requestUrl, formData);
        }
    }
}

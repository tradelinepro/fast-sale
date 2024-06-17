import Plugin from 'src/plugin-system/plugin.class';
import PluginManager from 'src/plugin-system/plugin.manager';
import DomAccess from 'src/helper/dom-access.helper';
import Debouncer from 'src/helper/debouncer.helper';
import HttpClient from 'src/service/http-client.service';
import ButtonLoadingIndicator from 'src/utility/loading-indicator/button-loading-indicator.util';
import ArrowNavigationHelper from 'src/helper/arrow-navigation.helper';
import Iterator from 'src/helper/iterator.helper';

export default class FastSaleSearchPlugin extends Plugin {

    static options = {
        resultSelector: '.js-fast-sale-search-result',
        resultItemSelector: '.js-fast-sale-result',

        inputFieldSelector: '.js-fast-sale-search-input',
        buttonFieldSelector: '.js-fast-sale-search-button',

        clearInputAfterXhr: 0,

        delay: 250,
        minChars: 3,
    };

    init() {
        try {
            this.inputField = DomAccess.querySelector(this.el, this.options.inputFieldSelector);
            this.submitButton = DomAccess.querySelector(this.el, this.options.buttonFieldSelector);
        } catch {
            return;
        }

        this.client = new HttpClient();

        this.navigationHelper = new ArrowNavigationHelper(
            this.inputField,
            this.options.resultSelector,
            this.options.resultItemSelector,
            true,
        );

        this.inputField.addEventListener(
            'input',
            Debouncer.debounce(this.handleInputEvent.bind(this), this.options.delay),
            {
                capture: true,
                passive: true,
            },
        );

        this.el.addEventListener(
            'submit',
            (event) => {
                event.preventDefault();
            }
        );
    }

    handleInputEvent() {
        const value = this.inputField.value.trim();

        if (value.length < this.options.minChars) {
            this.clearSuggestResults();

            return;
        }

        const url = this.el.action + encodeURIComponent(value);
        const indicator = new ButtonLoadingIndicator(this.submitButton);

        indicator.create();

        this.client.abort();
        this.client.get(url, (response) => {
            this.clearSuggestResults();

            indicator.remove();

            if(this.options.clearInputAfterXhr) {
                this.inputField.value = "";
            }

            this.el.insertAdjacentHTML('afterend', response);

            PluginManager.initializePlugins();
        });
    }

    clearSuggestResults() {
        this.navigationHelper.resetIterator();

        Iterator.iterate(document.querySelectorAll(this.options.resultSelector), result => result.remove());
    }
}

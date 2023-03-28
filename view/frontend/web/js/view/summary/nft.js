/*
 * Copyright © Ihor Oleksiienko (https://github.com/torys877)
 * See LICENSE for license details.
 */

define([
    'Magento_Checkout/js/view/summary/abstract-total',
    'Magento_Checkout/js/model/quote'
], function (Component, quote) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Crypto_NftLoyalty/summary/nft'
        },
        totals: quote.getTotals(),

        /**
         * @return {*|Boolean}
         */
        isDisplayed: function () {
            if (window.checkoutConfig.totalsData.nft != undefined) {
                return true;
            }
            return false;
        },
        /**
         * Get NFT label
         *
         * @returns {null|String}
         */
        getTitle: function () {
            if (this.isDisplayed()) {
                return window.checkoutConfig.totalsData.nft.label;
            }

            return '';
        },
    });
});

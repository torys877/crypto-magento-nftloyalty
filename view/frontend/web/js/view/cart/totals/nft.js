/*
 * Copyright © Ihor Oleksiienko (https://github.com/torys877)
 * See LICENSE for license details.
 */

define([
    'Magento_SalesRule/js/view/summary/discount'
], function (Component) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Crypto_NftLoyalty/cart/totals/nft'
        },

        /**
         * @override
         *
         * @returns {Boolean}
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
            return window.checkoutConfig.totalsData.nft.label;
        },
    });
});

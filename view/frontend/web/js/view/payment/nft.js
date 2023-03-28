/*
 * Copyright © Ihor Oleksiienko (https://github.com/torys877)
 * See LICENSE for license details.
 */

define([
    'jquery',
    'ko',
    'uiComponent',
    'Magento_Checkout/js/model/quote',
    'Crypto_NftLoyalty/js/web3instance',
    'Crypto_NftLoyalty/js/model/payment/nft-messages',
    'mage/storage',
    'mage/translate',
    'Magento_Checkout/js/action/get-payment-information',
    'Magento_Checkout/js/model/totals',
    'Magento_Checkout/js/model/full-screen-loader',
    'Magento_Checkout/js/action/recollect-shipping-rates',
    'Magento_Checkout/js/model/resource-url-manager',
    'Magento_Checkout/js/model/error-processor',
    'domReady!'
], function ($, ko, Component, quote, web3instance, messageContainer, storage, $t, getPaymentInformationAction,
             totals, fullScreenLoader, recollectShippingRates, urlManager, errorProcessor) {
    'use strict';

    var quoteTotals = quote.getTotals(),
        nft = ko.observable(null),
        isNftApplied = ko.observable(null),
        isWalletConnected = ko.observable(null);

    var quoteTotals = quote.getTotals();

    if (quoteTotals()) {
        nft(quoteTotals()['nft']);
    }

    isNftApplied(nft() != null);

    return Component.extend({
        defaults: {
            template: 'Crypto_NftLoyalty/payment/apply_nft',
            accounts: []
        },
        isNftApplied: isNftApplied,
        isWalletConnected: isWalletConnected,

        initialize: function () {
            isWalletConnected(false);
            this._super();
            web3instance.initialize();

            if (!web3instance.isWalletConnected()) {
                this.connectWallet();
            }

            if (web3instance.isWalletConnected()) {
                this.isWalletConnected(true);
            } else {
                this.isWalletConnected(false);
            }

            return;
        },
        connectWallet: function() {
            if (!web3instance.isWeb3()) {
                web3instance.createWeb3()
            }

            if (!web3instance.isWeb3()) {
                messageContainer.addErrorMessage({
                    'message': 'Metamask is not authorized or not installed.'
                });

                return false;
            }

            let self = this;
            web3instance.web3client.eth.requestAccounts().then(
                function(accs) {
                    web3instance.accounts = accs;
                    if (accs.length) {
                        isWalletConnected(true);
                    }
                }
            );
        },

        /**
         * Coupon code application procedure
         */
        apply: function () {
            this.action();
        },
        getApplyNftUrl: function() {
            var customerAddress = web3instance.getCurrentAccount();
            var urls = {
                    'guest': '/nft/' + quote.getQuoteId() + '/customerWallet/' + customerAddress,
                    'customer': '/nft/' + quote.getQuoteId() + '/customerWallet/' + customerAddress
                };

            return urlManager.getUrl(urls, {});
        },

        action: function () {
            var quoteId = quote.getQuoteId(),
                url = this.getApplyNftUrl(),
                data = {},
                headers = {};

            fullScreenLoader.startLoader();

            return storage.put(
                url,
                data,
                false,
                null,
                headers
            ).done(function (response) {
                var deferred;

                if (!response) {
                    messageContainer.addErrorMessage({
                        'message': 'You do not have NFT for discount'
                    });
                    isNftApplied(false);
                    totals.isLoading(false);
                    fullScreenLoader.stopLoader();

                    return;
                }

                deferred = $.Deferred();

                isNftApplied(true);
                totals.isLoading(true);
                recollectShippingRates();
                getPaymentInformationAction(deferred);

                $.when(deferred).done(function () {
                    fullScreenLoader.stopLoader();
                    totals.isLoading(false);
                });
                messageContainer.addSuccessMessage({
                    'message': 'Your NFT was applied'
                });
            }).fail(function (response) {
                fullScreenLoader.stopLoader();
                totals.isLoading(false);
                errorProcessor.process(response, messageContainer);
            });
        }
    });
});

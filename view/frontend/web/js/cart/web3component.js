/*
 * Copyright © Ihor Oleksiienko (https://github.com/torys877)
 * See LICENSE for license details.
 */
define([
    'uiComponent',
    'jquery',
    'web3'
], function (Component, $, web3) {
    'use strict';

    return Component.extend({
        defaults: {
            quote_id: null,
            apply_nft_url: null,
            web3client: null,
            requestIntervalSeconds: null,
            is_nft_applied: null,
            accounts: []
        },
        /** connect provider **/
        initialize: function () {
            this._super();
            if (!this.createWeb3()) {
                return;
            }

            this.connectWallet();

            if (this.is_nft_applied) {
                $('#connect_wallet_button').hide();
                $('#apply_nft').hide();
            }
        },
        showMessage: function(message) {
            $('.message').html(message);
            $('.message').show();
        },
        createWeb3: function() {
            this.givenProvider = web3.givenProvider;
            if (
                this.givenProvider &&
                typeof this.givenProvider != 'undefined'
            ) {
                this.web3client = new web3(web3.givenProvider);
                return true;
            } else {
                this.showMessage('Metamask is not authorized or not installed.');
                return false;
            }
        },
        /** check is provider exist **/
        isWeb3: function() {
            if (!this.web3client) {
                if (this.createWeb3()) {
                    return true;
                }

                return false;
            }

            return true;
        },
        isNftApplied: function() {
            return this.is_nft_applied;
        },
        /** connect metamask wallet to website **/
        connectWallet: function() {
            if (!this.isWeb3()) {
                return;
            }
            let self = this;
            this.web3client.eth.requestAccounts().then(
                function(accs) {
                    self.accounts = accs;
                    if (accs.length) {
                        $('#connect_wallet_button').hide();
                        $('#apply_nft').show();
                    }
                    if (self.is_nft_applied) {
                        $('#connect_wallet_button').hide();
                        $('#apply_nft').hide();
                        self.showMessage('NFT is already applied.');
                    }
                }
            );
        },
        /** check is wallet connected to website **/
        isWalletConnected: function() {
            if (!this.isWeb3()) {
                return false;
            }
            var result = this.accounts.length ? true:false;

            return result;
        },
        /** get all connected accounts **/
        getAccounts: function() {
            if (!this.isWeb3()) {
                return;
            }
            var self = this;
            this.web3client.eth.requestAccounts().then(
                function(result) {
                    self.accounts = result
                }
            );
        },
        /** get current account **/
        getCurrentAccount: function() {
            if (!this.isWeb3()) {
                return;
            }
            if (this.isWalletConnected()) {
                return this.accounts[0];
            }

            return false;
        },
        /** send metamask transaction **/
        applyNft: function() {
            if (!this.isWeb3()) {
                return;
            }

            let self = this;

            $.ajax({
                type: 'POST',
                url: self.apply_nft_url,
                showLoader: true,
                data: {
                    "customer_address": self.getCurrentAccount(),
                    "quote_id": self.quote_id
                }
            })
                .done(function(result) {
                    //register transaction, not captured
                    self.showMessage(result.message);
                    if (result.error) {
                        return;
                    }

                    location.reload();
                })
                .fail(function(result){
                    self.showMessage('Sorry, there was a problem saving the settings.');
                });
        }
    });
});

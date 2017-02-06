define(
		[ 'jquery', 'ko', 'Magento_Checkout/js/view/payment/default',
				'Paymill_Paymill/js/Paymill', 'mage/translate' ],
		function($, ko, Component, paymillComp, $t) {
			'use strict';

			return Component
					.extend({
						defaults : {
							template : 'Paymill_Paymill/payment/paymill_directdebit'
						},

						selectPaymentMethod : function() {
							window.paymillElv = paymillComp.initialize(this
									.getCode());
							window.paymillElv.setValidationRules();
							window.paymillElv.setEventListener(this
									.getTokenSelector());
							return this._super();
						},

						getPublicKey : function() {
							return window.checkoutConfig.payment.getPublicKey[this
									.getCode()];
						},

						getPaymentEntry : function(nameField) {
							return window.checkoutConfig.payment.getPaymentEntry[this
									.getCode()][nameField];
						},

						getPaymentEntryElv : function() {
							return window.checkoutConfig.payment.getPaymentEntryElv[this
									.getCode()];
						},

						getTokenSelector : function() {
							return window.checkoutConfig.payment.getTokenSelector[this
									.getCode()];
						},

						isInDebugMode : function() {
							return window.checkoutConfig.payment.isInDebugMode[this
									.getCode()];
						},

						getCheckoutDesc : function() {
							return window.checkoutConfig.payment.getCheckoutDesc[this
									.getCode()];
						},

						isFastCheckout : function() {
							return window.checkoutConfig.payment.isFastCheckout[this
									.getCode()];
						},

						getTokenLog : function() {
							return window.checkoutConfig.payment.getTokenLog[this
									.getCode()];
						},

						getTranslatedText : function(phrase) {

							return $t(phrase);
						},

						/**
						 * Get data
						 *
						 * @returns {Object}
						 */
						getData : function() {
							var data = {
								'method' : this.getCode(),
								'additional_data' : {
									'paymill-payment-token-elv' : $('.paymill-payment-token-elv')[0].value
								}
							};
							return data;
						},

						submitOrder : function() {
							var that = this;
							paymillElv.generateTokenOnSubmit(function(
									valid) {
								if (valid) {
									var button = $("button:first");
									button.click(function() {
										return that.placeOrder();
									});
									button.trigger("click");
								}
							});
						}

					});
		});

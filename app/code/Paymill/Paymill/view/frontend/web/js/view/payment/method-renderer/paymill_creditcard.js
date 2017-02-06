define(
		[ 'jquery', 'ko', 'Magento_Checkout/js/view/payment/default',
				'Paymill_Paymill/js/Paymill', 'mage/translate' ],
		function($, ko, Component, paymillComp, $t) {
			'use strict';

			var htmltemplate = function() {
				if (window.checkoutConfig.payment.getPci['paymill_creditcard'] === 'SAQ A-EP')
					return 'Paymill_Paymill/payment/paymill_creditcard';
				else
					return 'Paymill_Paymill/payment/paymill_creditcard_form';
			};

			return Component
					.extend({
						defaults : {

							template : htmltemplate()
						},

						selectPaymentMethod : function() {

							if (this.getPci() === 'SAQ A-EP') {
								window.paymillCreditcard = paymillComp
										.initialize(this.getCode());
								window.paymillCreditcard.setValidationRules();
								window.paymillCreditcard.setEventListener(this
										.getTokenSelector());
								window.paymillCreditcard.setCreditcards(this
										.getCreditCardLogosBrand());
								window.paymillPci = this.getPci();
								window.methodCode = this.getCode();
							} else {
								window.paymillCreditcard = paymillComp
										.initialize(this.getCode());
								if (this.isFastCheckout() === 'false') {
									paymill
											.embedFrame(
													'paymillContainer',
													{
														lang : $t('paymill_lang')
													},
													paymillCreditcard.PaymillFrameResponseHandler);
								}
								window.paymillTokenSelector = this
										.getTokenSelector();
								window.paymillPci = this.getPci();
							}

							return this._super();
						},
						paymillFrame : function() {
							window.paymillCreditcard.methodInstance
									.openPaymillFrame('de');
						},

						getPublicKey : function() {
							return window.checkoutConfig.payment.getPublicKey[this
									.getCode()];
						},
						getPaymentEntry : function(nameField) {
							return window.checkoutConfig.payment.getPaymentEntry[this
									.getCode()][nameField];
						},

						getSpecificCreditcard : function() {
							return window.checkoutConfig.payment.getSpecificCreditcard[this
									.getCode()];
						},

						getCreditCardLogosBrand : function() {
							return window.checkoutConfig.payment.getCreditCardLogosBrand[this
									.getCode()];
						},
						getPaymillCcMonths : function() {
							return window.checkoutConfig.payment.getPaymillCcMonths[this
									.getCode()];
						},

						getPaymillCcYears : function() {
							return window.checkoutConfig.payment.getPaymillCcYears[this
									.getCode()];
						},

						getTokenSelector : function() {
							return window.checkoutConfig.payment.getTokenSelector[this
									.getCode()];
						},

						getPci : function() {
							return window.checkoutConfig.payment.getPci[this
									.getCode()];
						},

						isFastCheckout : function() {
							return window.checkoutConfig.payment.isFastCheckout[this
									.getCode()];
						},

						getCurrency : function() {
							return window.checkoutConfig.payment.getCurrency[this
									.getCode()];
						},

						getCustomerEmail : function() {
							return window.checkoutConfig.payment.getCustomerEmail[this
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

						getTokenUrl : function() {
							return window.checkoutConfig.payment.getTokenUrl[this
									.getCode()];
						},

						getTokenLog : function() {
							return window.checkoutConfig.payment.getTokenLog[this
									.getCode()];
						},
						getCvvImageUrl : function() {
							return window.checkoutConfig.payment.getCvvImageUrl[this
									.getCode()];
						},

						/**
						 * Get image for CVV
						 * 
						 * @returns {String}
						 */
						getCvvImageHtml : function() {
							return '<img src="'
									+ this.getCvvImageUrl()
									+ '" alt="'
									+ $t('Card Verification Number Visual Reference')
									+ '" title="'
									+ $t('Card Verification Number Visual Reference')
									+ '" />';
						},

						/**
						 * Get list of available month values
						 * 
						 * @returns {Object}
						 */
						getCcMonthsValues : function() {
							return _.map(this.getPaymillCcMonths(), function(
									value, key) {
								return {
									'value' : key,
									'month' : value
								};
							});
						},

						/**
						 * Get list of available year values
						 * 
						 * @returns {Object}
						 */
						getCcYearsValues : function() {
							return _.map(this.getPaymillCcYears(), function(
									value, key) {
								return {
									'value' : key,
									'year' : value
								};
							});
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
									'paymill-payment-token-cc' : $('.paymill-payment-token-cc')[0].value
								}
							};
							return data;
						},

						submitOrder : function() {
							var that = this;
							paymillCreditcard.generateTokenOnSubmit(function(
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

define(
		[ 'uiComponent', 'Magento_Checkout/js/model/payment/renderer-list' ],
		function(Component, rendererList, paymillComp) {
			'use strict';
			rendererList
					.push(
							{
								type : 'paymill_creditcard',
								component : 'Paymill_Paymill/js/view/payment/method-renderer/paymill_creditcard'
							},
							{
								type : 'paymill_directdebit',
								component : 'Paymill_Paymill/js/view/payment/method-renderer/paymill_directdebit'
							});

			return Component.extend({});
		});

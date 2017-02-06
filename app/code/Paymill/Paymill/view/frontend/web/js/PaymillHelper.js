define([ 'jquery' ], function($) {
	'use strict';

	return {

		getElementValue : function(selector) {
			var value = '';
			if ($(selector)[0]) {
				value = $(selector)[0].value;
			}

			return value;
		},

		getShortCode : function() {
			var methods = {
				paymill_creditcard : "cc",
				paymill_directdebit : 'elv'
			};

			if (paymentCode in methods) {
				return methods[paymentCode];
			}

			return 'other';
		},

		setElementValue : function(selector, value) {
			if ($(selector)[0]) {
				$(selector)[0].value = value;
			}
		},

		getMethodCode : function() {
			return paymentCode;
		}
	};
});

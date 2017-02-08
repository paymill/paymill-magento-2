define(
    [
        'jquery',
        'Paymill_Paymill/js/PaymillHelper',
        'Paymill_Paymill/js/Creditcard',
        'Paymill_Paymill/js/Elv'
    ],
    function ($, paymill_helper, creditcard, elv) {
        'use strict';

        if (typeof Array.prototype.forEach !== 'function') {
            Array.prototype.forEach = function (callback, context) {
                for (var i = 0; i < this.length; i++) {
                    callback.apply(context, [this[i], i, this]);
                }
            };
        }

        window.PAYMILL_PUBLIC_KEY = null;
        window.paymentCode = null;
        window.validElv = false;
        window.validCC = false;

        var tokenCallback = function (error, result, paymillGlo, callbackTokenRes)
        {
            paymillGlo.debug("Enter paymillResponseHandler");

            var rules = {};
            if (error) {
                var message = 'unknown_error';
                var key = error.apierror;
                if (paymillGlo.helper.getElementValue('.PAYMILL_' + key + '-' + paymillGlo.helper.getShortCode()) !== '') {
                    message = paymillGlo.helper.getElementValue('.PAYMILL_' + key + '-' + paymillGlo.helper.getShortCode());
                }

                if (message === 'unknown_error' && error.message !== undefined) {
                    message = error.message;
                }


                // Appending error
                rules['paymill-validate-' + paymillGlo.helper.getShortCode() + '-token'] = new Validator(
                    'paymill-validate-' + paymillGlo.helper.getShortCode() + '-token',
                    paymillGlo.helper.getElementValue('.paymill-payment-error-' + paymillGlo.helper.getShortCode() + '-token') + ' ' + message,
                    function (value) {
                        return false;
                    },
                    ''
                );

                paymillGlo.helper.setElementValue('#paymill_creditcard_cvc', '');
                paymillGlo.logError(error);
                paymillGlo.debug(error.apierror);
                paymillGlo.debug(error.message);
                paymillGlo.debug("Paymill Response Handler triggered: Error.");
                Object.extend(Validation.methods, rules);
                callbackTokenRes(false);
            } else {
                rules['paymill-validate-' + paymillGlo.helper.getShortCode() + '-token'] = new Validator(
                    'paymill-validate-' + paymillGlo.helper.getShortCode() + '-token',
                    '',
                    function (value) {
                        return true;
                    },
                    ''
                );

                Object.extend(Validation.methods, rules);

                paymillGlo.debug("Saving Token in Form: " + result.token);
                paymillGlo.helper.setElementValue('.paymill-payment-token-' + paymillGlo.helper.getShortCode(), result.token);
                callbackTokenRes(true);
            }
        };

        return {

            /**
			 * Init component
			 */
            initialize: function (methodCode) {
                this.methodInstance = null;
                this.methodCode = methodCode;
                if (methodCode === 'paymill_creditcard') {
                    this.methodInstance = creditcard.initialize();
                }

                if (methodCode === 'paymill_directdebit') {
                    this.methodInstance = elv.initialize();
                }
                paymentCode = methodCode;
                this.helper = paymill_helper;
                return this;
            },

            setValidationRules: function ()
            {
                this.methodInstance.setValidationRules();
            },
            
            openFrame: function (lang)
            {
                this.methodInstance.openPaymillFrame(lang);
            },

            setEventListener: function (selector)
            {
                this.methodInstance.setEventListener(selector);
            },

            generateToken: function (callbackTokenRes)
            {
                if (this.validate()) {
                    if (this.helper.getMethodCode() === 'paymill_creditcard') {
                        new Validation($('#paymill_creditcard_cvc')[0].form.id).validate();
                    }

                    if (this.helper.getMethodCode() === 'paymill_directdebit') {
                        new Validation($('#paymill_directdebit_holdername')[0].form.id).validate();
                    }

                    var data = this.methodInstance.getTokenParameter();
                    this.debug("Generating Token");
                    this.debug(data);
                    var that = this;
                    paymill.createToken(
                        data,
                        function(error, result){
                            tokenCallback(error, result, that, callbackTokenRes);
                        }
                    );
                }
                else
                    callbackTokenRes(false);
            },

            generateTokenOnSubmit: function (callbackTokenRes)
            {
                if (this.helper.getElementValue('.paymill-info-fastCheckout-' + this.helper.getShortCode()) !== 'true') {

                    if (this.helper.getMethodCode() === 'paymill_creditcard') {
                        if (this.helper.getElementValue('.paymill-info-pci-' + this.helper.getShortCode()) === 'SAQ A') {
                            var data = this.methodInstance.getFrameTokenParameter();
                            this.debug("Generating Token");
                            this.debug(data);
                            var that = this;
                            paymill.createTokenViaFrame(data, 
                                function(error, result) {
                                    tokenCallback(error, result, that, callbackTokenRes);
                                }
                            );
                            return;
                        } 
                        else if (new Validation($('#paymill_creditcard_cvc')[0].form.id).validate()) {
                            callbackTokenRes(true);
                            return;
                        }

                    }

                    else if (this.helper.getMethodCode() === 'paymill_directdebit') {
                        if (new Validation($('#paymill_directdebit_holdername')[0].form.id).validate()){
                            callbackTokenRes(true);
                            return
                        }
                    }
                    callbackTokenRes(false);
                    return;
                } 
                callbackTokenRes(true);
            },

            validate: function ()
            {
                this.debug("Start form validation");
                var valid = this.methodInstance.validate();
                this.debug(valid);
                return valid;
            },

            debug: function (message)
            {
                if (this.helper.getElementValue('.paymill-option-debug-' + this.helper.getShortCode()) === "1") {
                    console.log(message);
                }
            },

            logError: function (data)
            {
                var that = this;
                data.form_key = $.cookie('form_key');
                new Ajax.Request(this.helper.getElementValue('.paymill-payment-token-log-' + this.helper.getShortCode()), {
                    method: 'post',
                    parameters: data,
                    onSuccess: function (response) {
                        that.debug('Logging done.');
                    }, onFailure: function () {
                        that.debug('Logging failed.');
                    }
                });
            },

            setCreditcards: function (creditcards)
            {
                this.methodInstance.creditcards = creditcards;
            }
        };
    }
);
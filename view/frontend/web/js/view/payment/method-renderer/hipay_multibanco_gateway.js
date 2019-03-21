/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*browser:true*/
/*global define*/
define(
        [
            'jquery',
            'Magento_Checkout/js/view/payment/default',
            'Magento_Checkout/js/action/place-order',
            'Magento_Checkout/js/action/select-payment-method',
            'Magento_Customer/js/model/customer',
            'Magento_Checkout/js/checkout-data',
            'Magento_Checkout/js/model/payment/additional-validators',
            'mage/url',
        ],
        function ($,
                Component,
                placeOrderAction,
                selectPaymentMethodAction,
                customer,
                checkoutData,
                additionalValidators,
                url) {
            'use strict';

            return Component.extend({
                defaults: {
                    template: 'Hipay_HipayMultibancoGateway/payment/form',
                    transactionResult: ''
                },

                selectPaymentMethod: function () {
                    selectPaymentMethodAction(this.getData());
                    checkoutData.setSelectedPaymentMethod(this.item.method);
                    return true;
                },

                initObservable: function () {
                    this._super()
                            .observe([
                                'transactionResult'
                            ]);
                    return this;
                },

                getCode: function () {
                    return 'hipay_multibanco_gateway';
                },
                getHipayMultibancoIcon: function () {
                    return window.checkoutConfig.payment.hipay_multibanco_gateway.paymentImageSrc;
                },
                getData: function () {
                    return {
                        'method': this.item.method,
                        'additional_data': {
                            'transaction_result': this.transactionResult()
                        }
                    };
                },

                getTransactionResults: function () {
                    return _.map(window.checkoutConfig.payment.hipay_multibanco_gateway.transactionResults, function (value, key) {
                        return {
                            'value': key,
                            'transaction_result': value
                        }
                    });
                }
            });
        }
);

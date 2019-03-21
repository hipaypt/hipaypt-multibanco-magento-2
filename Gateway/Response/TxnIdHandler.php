<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Hipay\HipayMultibancoGateway\Gateway\Response;

use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Payment\Gateway\Response\HandlerInterface;

class TxnIdHandler implements HandlerInterface
{
    const ENTITY 			= 'ENTITY';
    const REFERENCE 		= 'REFERENCE';
    const AMOUNTOUT 		= 'AMOUNTOUT';
    const EXPIRY_DATE 		= 'EXPIRY_DATE';
    const ACCOUNT_TYPE 		= 'ACCOUNT_TYPE';
    const TRANSACTION_ID 	= 'TRANSACTION_ID';

    /**
     * Handles transaction id
     *
     * @param array $handlingSubject
     * @param array $response
     * @return void
     */
    public function handle(array $handlingSubject, array $response)
    {
        if (!isset($handlingSubject['payment'])
            || !$handlingSubject['payment'] instanceof PaymentDataObjectInterface
        ) {
            throw new \InvalidArgumentException('Payment data object should be provided');
        }

        $paymentDO = $handlingSubject['payment'];
        $payment = $paymentDO->getPayment();

        $payment->setTransactionId($response[self::TRANSACTION_ID]);
        $payment->setAdditionalInformation("MB_Entity",$response[self::ENTITY]);
        $payment->setAdditionalInformation("MB_Reference",$response[self::REFERENCE]);
        $payment->setAdditionalInformation("MB_AmountOut",$response[self::AMOUNTOUT]);
        $payment->setAdditionalInformation("MB_ExpiryDate",$response[self::EXPIRY_DATE]);
        $payment->setAdditionalInformation("accountType",$response[self::ACCOUNT_TYPE]);
        $payment->setIsTransactionClosed(false);
			
    }


}

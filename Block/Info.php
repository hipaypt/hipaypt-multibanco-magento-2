<?php
namespace Hipay\HipayMultibancoGateway\Block;

use Magento\Framework\Phrase;
use Magento\Framework\Registry;

class Info extends \Magento\Payment\Block\Info
{

    public function getPaymentInfoData()
    {

	$orderId = $this->getRequest()->getParam('order_id');
	$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
	$order = $objectManager->create('\Magento\Sales\Model\OrderRepository')->get($orderId);

	if ( $order->getPayment()->getMethod() == "hipay_multibanco_gateway"){

		$payment = $order->getPayment();	//->getAdditionalInformation('MB_Entity');

        $details['MB_Entity'] = $payment->getAdditionalInformation('MB_Entity');
       	$details['MB_Reference'] = $payment->getAdditionalInformation('MB_Reference');
		$details['MB_AmountOut'] = $payment->getAdditionalInformation('MB_AmountOut');
		$details['MB_ExpiryDate'] = $payment->getAdditionalInformation('MB_ExpiryDate');
	
        	return $details;
	}
	return;

    }

}



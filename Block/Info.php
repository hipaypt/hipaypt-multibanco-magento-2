<?php
namespace Hipay\HipayMultibancoGateway\Block;

use Magento\Framework\Registry;

class Info extends \Magento\Payment\Block\Info
{
    public $_template = 'Hipay_HipayMultibancoGateway::info/order_info.phtml';

    public function getPaymentInfoData()
    {
        return $this->getInfo()->getAdditionalInformation();
    }
    


}

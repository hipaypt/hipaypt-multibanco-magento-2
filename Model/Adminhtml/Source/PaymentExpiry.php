<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Hipay\HipayMultibancoGateway\Model\Adminhtml\Source;

use Magento\Payment\Model\Method\AbstractMethod;

class PaymentExpiry implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            [
                'value' => "3", 'label' => "3"     ],
            [
                'value' => "30", 'label' => "30"      ],
            [
                'value' => "90", 'label' => "90"     ],
            [
                'value' => "1",  'label' => "1"     ],                        
            [
                'value' => "0", 'label' => "0"      ]
        ];
    }
}

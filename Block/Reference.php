<?php
namespace Hipay\HipayMultibancoGateway\Block;

class Reference extends \Magento\Sales\Block\Order\Totals
{
    protected $checkoutSession;
    protected $customerSession;
    protected $_orderFactory;
    protected $_order;
    protected $_payment;
    protected $_showTable;
    protected $_orderId;
    protected $_methodCode;
    
    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        parent::__construct($context, $registry, $data);
        $this->checkoutSession = $checkoutSession;
        $this->customerSession = $customerSession;
        $this->_orderFactory = $orderFactory;
    }

    public function getOrder()
    {

	$this->_orderId = $this->checkoutSession->getLastRealOrderId();
        $this->_order = $this->_orderFactory->create()->loadByIncrementId($this->_orderId);
        $this->_payment = $this->_order->getPayment();

        $this->_methodCode = $this->_payment->getMethod();
		$this->_showTable = false;				
        if ( $this->_methodCode == "hipay_multibanco_gateway" )
        {
			$this->_showTable = true;
			$this->_payment->setIsTransactionClosed(false);
			$this->_order->setState(\Magento\Sales\Model\Order::STATE_PENDING_PAYMENT)->setStatus("pending");
			$this->_order->save();		
		}
        return  $this->_order;
    }

	public function getReferenceTable(){
		$referenceTable = "";
		
		if ($this->showTable() || $this->_methodCode == "hipay_professional_gateway") {

			$referenceTable = "<p><br>" . __('Your order # is: ') .  $this->getOrderId() . ".</p>";
            $referenceTable .= "<p><br>" . __('You will receive an email with your order details.') . "</p>";
            $referenceTable .= "<p><br>";

		}
		if ($this->showTable() ) {
			$referenceTable .= '<table cellpadding="6" cellspacing="2" style="width: 350px; height: 55px; margin: 10px 0 2px 0;border: 1px solid #ddd">
				<tr>
					<td style="background-color: #ccc;color:#313131;text-align:center;" colspan="3">';
				$referenceTable .= __('Pay the following Multibanco reference at an ATM machine or Homebanking.') . '</td>
				</tr>
				<tr>
					<td rowspan="4" style="width:110px;padding: 0px 5px 0px 5px;vertical-align: middle;"><img src="'. $this->getViewFileUrl("Hipay_HipayMultibancoGateway::images/multibanco.jpg"). '" style="margin-bottom: 0px; margin-right: 0px;"/></td>
					<td style="width:100px;">';
				$referenceTable .= __('ENTITY') . '</td>
					<td style="font-weight:bold;width:245px;">'. $this->getMultibancoEntity(). '</td>
				</tr>
				<tr>
					<td>';
				$referenceTable .= __('REFERENCE'). '</td>
					<td style="font-weight:bold;">'. $this->getMultibancoReference(). '</td>
				</tr>
				<tr>
					<td>';
				$referenceTable .= __('AMOUNT'). '</td>
					<td style="font-weight:bold;">'. $this->getMultibancoAmount(). ' &euro;</td>
				</tr>
				<tr>
					<td>';
				$referenceTable .= __('EXPIRY DATE'). '</td>
					<td style="font-weight:bold;">'. $this->getMultibancoExpiryDate(). '</td>
				</tr>
			</table>';

			$referenceTable .= "</p><p>" . __('This payment details are valid for the next 3 days. Your order will be canceled on the fourth day.') . "</p>";

		}
		return $referenceTable;	

	}

    public function showTable()
    {
        return $this->_showTable;
    }

    public function getMethodCode()
    {
        return $this->_methodCode;
    }

    
    public function getCustomerId()
    {
        return $this->customerSession->getCustomer()->getId();
    }

        public function getOrderId()
        {
                return $this->_orderId;
        }       
    
	public function getMultibancoEntity()
	{
		return $this->_payment->getAdditionalInformation('MB_Entity');
	}	

	public function getMultibancoReference()
	{
		return $this->_payment->getAdditionalInformation('MB_Reference');
	}	

	public function getMultibancoAmount()
	{
		return $this->_payment->getAdditionalInformation('MB_AmountOut');
	}	

	public function getMultibancoExpiryDate()
	{
		return $this->_payment->getAdditionalInformation('MB_ExpiryDate');
	}	
    
	public function getMultibancoAccountType()
	{
		return $this->_payment->getAdditionalInformation('accountType');
	}	

}

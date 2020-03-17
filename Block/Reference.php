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
        $this->_order = $this->_orderFactory->create()->loadByIncrementId($this->checkoutSession->getLastRealOrderId());
        $this->_payment = $this->_order->getPayment();
        $methodCode = $this->_payment->getMethod();
		$this->_showTable = false;				
        if ( $methodCode == "hipay_multibanco_gateway" )
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
		
		if ($this->showTable() ) {
		
			$referenceTable = '<table cellpadding="6" cellspacing="2" style="width: 350px; height: 55px; margin: 10px 0 2px 0;border: 1px solid #ddd">
				<tr>
					<td style="background-color: #ccc;color:#313131;text-align:center;" colspan="3">'. __('Pay the following Multibanco reference at an ATM machine or Homebanking') . '</td>
				</tr>
				<tr>
					<td rowspan="4" style="width:110px;padding: 0px 5px 0px 5px;vertical-align: middle;"><img src="'. $this->getViewFileUrl("Hipay_HipayMultibancoGateway::images/multibanco.jpg"). '" style="margin-bottom: 0px; margin-right: 0px;"/></td>
					<td style="width:100px;">'. __('ENTITY') . '</td>
					<td style="font-weight:bold;width:245px;">'. $this->getMultibancoEntity(). '</td>
				</tr>
				<tr>
					<td>'. __('REFERENCE'). '</td>
					<td style="font-weight:bold;">'. $this->getMultibancoReference(). '</td>
				</tr>
				<tr>
					<td>'. __('AMOUNT'). '</td>
					<td style="font-weight:bold;">'. $this->getMultibancoAmount(). ' &euro;</td>
				</tr>
				<tr>
					<td>'. __('EXPIRY DATE'). '</td>
					<td style="font-weight:bold;">'. $this->getMultibancoExpiryDate(). '</td>
				</tr>
			</table>';

		}
		return $referenceTable;	

	}

    public function showTable()
    {
        return $this->_showTable;
    }
    
    public function getCustomerId()
    {
        return $this->customerSession->getCustomer()->getId();
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

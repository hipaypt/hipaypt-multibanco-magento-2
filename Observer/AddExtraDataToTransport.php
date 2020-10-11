<?php

namespace Hipay\HipayMultibancoGateway\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\ObjectManager;

class AddExtraDataToTransport implements ObserverInterface
{  

	protected $_order;
	protected $_payment;
    protected $assetRepo;

	public function __construct(\Magento\Framework\View\Asset\Repository $assetRepo, \Magento\Sales\Api\Data\OrderInterface $order	) {
		$this->_order = $order;
        $this->assetRepo = $assetRepo;
	}

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $transport = $observer->getEvent()->getTransport();
		$incrementId = $transport['order']['increment_id'];
		$this->_order->loadByIncrementId($incrementId);
		$this->_payment = $this->_order->getPayment();
		if ( $this->_payment->getMethod() == 'hipay_multibanco_gateway'){	
			$method = $this->_payment->getMethodInstance();
			$methodTitle = $method->getTitle();
			$transport['payment_html'] = $methodTitle . $this->getReferenceTable() ;
		}
	}

	protected function getReferenceTable(){
		
		$referenceTable = '<table cellpadding="6" cellspacing="2" style="width: 300px; height: 55px; margin: 10px 0 2px 0;border: 1px solid #ddd">
			<tr>
				<td style="background-color: #ccc;color:#313131;text-align:center;" colspan="3">'. __('Pay the following Multibanco reference at an ATM machine or Homebanking.') . '</td>
			</tr>
			<tr>
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

		return $referenceTable;
	}

    protected function getLogoUrl() {
        return $this->assetRepo->getUrlWithParams('Hipay_HipayMultibancoGateway::images/multibanco.jpg', ['_secure' => true]);
    }
   
	protected function getMultibancoEntity()
	{
		return $this->_payment->getAdditionalInformation('MB_Entity');
	}	

	protected function getMultibancoReference()
	{
		return $this->_payment->getAdditionalInformation('MB_Reference');
	}	

	protected function getMultibancoAmount()
	{
		return $this->_payment->getAdditionalInformation('MB_AmountOut');
	}	

	protected function getMultibancoExpiryDate()
	{
		return $this->_payment->getAdditionalInformation('MB_ExpiryDate');
	}	
    
	protected function getMultibancoAccountType()
	{
		return $this->_payment->getAdditionalInformation('accountType');
	}	




}

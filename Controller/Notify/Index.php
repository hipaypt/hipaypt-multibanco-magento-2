<?php
namespace Hipay\HipayMultibancoGateway\Controller\Notify;

use Magento\Framework\App\Action\Action as AppAction;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Request\Http as HttpRequest;

class Index extends AppAction
{

	const HIPAY_PRODUCTION_ENVIRONMENT_10241 = "https://comprafacil1.hipay.pt/webservice/comprafacilWS.asmx?wsdl";
	const HIPAY_PRODUCTION_ENVIRONMENT_11249 = "https://comprafacil2.hipay.pt/webservice/comprafacilWS.asmx?wsdl";
	const HIPAY_SANDBOX_ENVIRONMENT_10241 = "https://comprafacil1.hipay.pt/webservice-test/comprafacilWS.asmx?wsdl";
	const HIPAY_SANDBOX_ENVIRONMENT_11249 = "https://comprafacil2.hipay.pt/webservice-test/comprafacilWS.asmx?wsdl";

    protected $_messageManager;
    protected $_context;
    protected $_order;
    protected $_sandbox;
    protected $_credentials;
 	protected $_payment;
 	protected $_entity;
 	
    public function __construct(
		\Magento\Framework\App\Action\Context $context
    ) {
		
        parent::__construct($context);
        $this->_messageManager = $context->getMessageManager();
    }
    
    public function execute()
    {
		
		if(!isset($_GET["order"])) {
			return;
		};
		
		$idformerchant = $_GET["order"];
		$reference = $_GET["ref"];
		$entity = $_GET["ent"]; 

		$this->_order = $this->_objectManager->create('\Magento\Sales\Model\Order')->loadByIncrementId($idformerchant);
		$this->_payment = $this->_order->getPayment();
	
		$storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORES;
		$this->_sandbox 		= $this->_objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface')->getValue('payment/hipay_multibanco_gateway/sandbox',$storeScope);
		$this->_entity	 		= $this->_objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface')->getValue('payment/hipay_multibanco_gateway/payment_entity',$storeScope);
		if ($this->_sandbox)
			$this->_credentials		= $this->_objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface')->getValue('payment/hipay_multibanco_gateway/api_sandbox' ,$storeScope);
		else
			$this->_credentials		= $this->_objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface')->getValue('payment/hipay_multibanco_gateway/api_production',$storeScope);
		
		$hipayTransaction = $this->getTransactionStatus($reference);
		
		if ($hipayTransaction ){

			echo " AND CAPTURE!";
			if ($this->_order->getState() != \Magento\Sales\Model\Order::STATE_CANCELED && $this->_order->getState() != \Magento\Sales\Model\Order::STATE_PROCESSING){
				$this->_order->setState(\Magento\Sales\Model\Order::STATE_PROCESSING)->setStatus("processing");
				$comment = "Captured, " . date('Y-m-d H:i:s');
				$this->_order->addStatusHistoryComment($comment)->setIsCustomerNotified(true)->setEntityName('order');
				$this->_order->save();
			}	
			
		} 

	}
	
	
	protected function getTransactionStatus($reference){
		
		switch($this->_entity){
			case "11249":
				if ($this->_sandbox )
					$ws_url= self::HIPAY_SANDBOX_ENVIRONMENT_11249;
				else
					$ws_url = self::HIPAY_PRODUCTION_ENVIRONMENT_11249;
				break;
				
			case "10241":
				if ($this->_sandbox )
					$ws_url = self::HIPAY_SANDBOX_ENVIRONMENT_10241;
				else
					$ws_url = self::HIPAY_PRODUCTION_ENVIRONMENT_10241;
				break;
		}
		

		$client = new \SoapClient($ws_url);
		$parameters = new \stdClass(); 

		$parameters = array(
			"reference" => $reference,
			"username" => $this->_credentials["merchant_api_login"],
			"password" => $this->_credentials["merchant_api_password"]
			);

		$res = $client->getInfoReference($parameters);
		if ($res->getInfoReferenceResult)
		{
			$paid = $res->paid;
			if ($paid)
			{
				echo "PAID";
				return true;

			} else
			{
				echo "NOT PAID";
				return false;
			}
		}
		else
		{
			return false;
		}

		return false;			
		
	}
	
	
}
	

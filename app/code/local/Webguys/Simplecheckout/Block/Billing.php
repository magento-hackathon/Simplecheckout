<?php

class Webguys_Simplecheckout_Block_Billing extends Mage_Checkout_Block_Onepage_Billing {

    protected function _construct()
    {
        $this->getCheckout()->setStepData('billing', 'is_show', true);
        Mage_Checkout_Block_Onepage_Abstract::_construct();
        
    }

	public function getPostAction()
    {
        return Mage::getUrl('checkout/simple/saveBilling', array('_secure'=>true));
    }
    
    public function getAddress() {
    	if ($this->getRequest()->isPost()) {
    		$postData = $this->getRequest()->getPost('billing', array());
    		$address = Mage::getModel('sales/quote_address')->setData( $postData );
    		return $address;
    	}
        return $this->getQuote()->getBillingAddress();        
    }
    
    public function isRegister() {
    	return ( $this->getMethod() == Mage_Checkout_Model_Type_Onepage::METHOD_REGISTER );
    }

}

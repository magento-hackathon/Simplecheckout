<?php

class Firegento_Simplecheckout_Block_Payment extends Mage_Checkout_Block_Onepage_Billing {

    protected function _construct()
    {
        $this->getCheckout()->setStepData('payment', 'is_show', true);
        Mage_Checkout_Block_Onepage_Abstract::_construct();
    }

	public function getPostAction()
    {
        return Mage::getUrl('checkout/simple/savePayment', array('_secure'=>true));
    }
   

}

<?php

class Webguys_Simplecheckout_Block_Shipping extends Mage_Checkout_Block_Onepage_Shipping {

    protected function _construct()
    {
        $this->getCheckout()->setStepData('shipping', 'is_show', true);
        Mage_Checkout_Block_Onepage_Abstract::_construct();
    }

	public function getPostAction()
    {
        return Mage::getUrl('checkout/simple/saveShipping', array('_secure'=>true));
    }

}

<?php

class Webguys_Simplecheckout_Block_Shipping_Method extends Mage_Checkout_Block_Onepage_Shipping_Method { 
    
    protected function _construct()
    {
        $this->getCheckout()->setStepData('shipping_method', 'is_show', true);
        Mage_Checkout_Block_Onepage_Abstract::_construct();
    }
    
    public function getPostAction()
    {
        return Mage::getUrl('checkout/simple/saveShippingMethod', array('_secure'=>true));
    }

}

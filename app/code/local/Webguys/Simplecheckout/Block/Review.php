<?php

class Webguys_Simplecheckout_Block_Review extends Mage_Checkout_Block_Onepage_Billing {

	public function getPostAction()
    {
        return Mage::getUrl('checkout/simple/saveOrder', array('_secure'=>true));
    }
    
}

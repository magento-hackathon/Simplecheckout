<?php

class Firegento_Simplecheckout_Block_Login extends Mage_Checkout_Block_Onepage_Login {

	public function getPostAction()
    {
        return Mage::getUrl('checkout/simple/login', array('_secure'=>true));
    }


}


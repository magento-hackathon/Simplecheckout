<?php

include_once("Mage/Checkout/controllers/OnepageController.php");

class Webguys_Simplecheckout_SimpleController extends Mage_Checkout_OnepageController {

	protected function initLayout() {
		$this->loadLayout();
        $this->_initLayoutMessages('customer/session');
	}

    public function loginAction() {
    	
    	$checkout_method = $this->getRequest()->getParam('checkout_method');
    	switch( $checkout_method ) {
    	
    		case Mage_Checkout_Model_Type_Onepage::METHOD_GUEST:
    		case Mage_Checkout_Model_Type_Onepage::METHOD_REGISTER:
    			$this->getOnepage()->saveCheckoutMethod( $checkout_method );
    			$this->getOnepage()->getQuote()->save();   			
    			return $this->_redirect('*/*/billing');
    		
    		case Mage_Checkout_Model_Type_Onepage::METHOD_CUSTOMER:
    			// TODO: Login prüfen und weiter ;)
    			die("TODO!");
    			break;
    			
    		default:
    			throw new Exception("Fehler ;)"); // TODO: Echte Exception werfen
    			break;
    	
    	} 
    	
    }
    
    public function billingAction() {
    	$this->initLayout();
    	
        $this->renderLayout();	
    }
    
    public function saveBillingAction()
    {
       if ($this->getRequest()->isPost()) {
            $postData = $this->getRequest()->getPost('billing', array());
            $data = $this->_filterPostData($postData);
            $customerAddressId = $this->getRequest()->getPost('billing_address_id', false);

            if (isset($data['email'])) {
                $data['email'] = trim($data['email']);
            }
            $result = $this->getOnepage()->saveBilling($data, $customerAddressId);

            if (!isset($result['error'])) {
                
                $this->getOnepage()->getQuote()->collectTotals();
		        $this->getOnepage()->getQuote()->save();
                
                /* check quote for virtual */
                if ($this->getOnepage()->getQuote()->isVirtual()) {
                    return $this->_redirect('*/*/payment');
                } elseif (isset($data['use_for_shipping']) && $data['use_for_shipping'] == 1) {
                    return $this->_redirect('*/*/shippingmethod');
                } else {                    
                    return $this->_redirect('*/*/shipping');                    
                }
            }
            
            foreach( $result['message'] AS $msg ) {
            	Mage::getSingleton('checkout/session')->addError( $msg );            	
            }      
                
        }
        
        return $this->_forward('billing');
    }
    
    public function shippingAction() {
    	$this->initLayout();    	
        $this->renderLayout();	
    }
        
 	public function saveShippingAction()
    {
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost('shipping', array());
            $customerAddressId = $this->getRequest()->getPost('shipping_address_id', false);
            $result = $this->getOnepage()->saveShipping($data, $customerAddressId);

            if (!isset($result['error'])) {
               return $this->_redirect('*/*/shippingmethod');
            }
             
            foreach( $result['message'] AS $msg ) {
            	Mage::getSingleton('checkout/session')->addError( $msg );            	
            }  
            
        }
        return $this->_redirect('*/*/shipping');
    }  
    
   public function shippingMethodAction()
   {
        $this->initLayout(); 
        $this->renderLayout();
   }
   
   public function saveShippingMethodAction()
   {
		if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost('shipping_method', '');
            $result = $this->getOnepage()->saveShippingMethod($data);
            
            if(!$result) {
                Mage::dispatchEvent('checkout_controller_onepage_save_shipping_method', array('request'=>$this->getRequest(), 'quote'=>$this->getOnepage()->getQuote()));
                $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));

                return $this->_redirect('*/*/payment'); 
            }
            
            foreach( $result['message'] AS $msg ) {
            	Mage::getSingleton('checkout/session')->addError( $msg );            	
            }
            
        }
        return $this->_redirect('*/*/shippingmethod');
   }
   
   public function paymentAction()
   {
        $this->initLayout(); 
        $this->renderLayout();
   } 
   
   public function savePaymentAction()
    {
        try {

            // set payment to quote
            $result = array();
            $data = $this->getRequest()->getPost('payment', array());
            $result = $this->getOnepage()->savePayment($data);
            
            $this->getOnepage()->getQuote()->save();
            
            // get section and redirect data
            $redirectUrl = $this->getOnepage()->getQuote()->getPayment()->getCheckoutRedirectUrl();
            if (empty($result['error']) && !$redirectUrl) {
                return $this->_redirect('*/*/review'); 
            }
            
            if ($redirectUrl) {
                header("LOCATION: ".$redirectUrl);
            }
            
        } catch (Mage_Payment_Exception $e) {
            
            if ($e->getFields()) {
                $result['fields'] = $e->getFields();
            }
            Mage::getSingleton('checkout/session')->addError( $e->getMessage() );
            
        } catch (Mage_Core_Exception $e) {
            Mage::getSingleton('checkout/session')->addError( $e->getMessage() );
        } catch (Exception $e) {
            Mage::logException($e);
            Mage::getSingleton('checkout/session')->addError( $this->__('Unable to set Payment Method.') );
        }
        
        return $this->_redirect('*/*/payment');        
    }
    
    public function reviewAction()
    {
        $this->initLayout(); 
        $this->renderLayout();
    }

}

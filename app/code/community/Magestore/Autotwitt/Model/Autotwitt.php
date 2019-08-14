<?php

class Magestore_Autotwitt_Model_Autotwitt extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('autotwitt/autotwitt');
    }
	
	
	/*
		
	*/
	public function add_product_to_twitter($observer)
	{
		if (!Mage::getStoreConfig('autotwitt/general/enable'))
		{
			return;
		}
		
		if (!Mage::getStoreConfig('autotwitt/site_events/add_to_cart'))
		{
			return;
		}
		
		$observer_data = $observer->getData();
		$product = $observer_data['product'];
		$prefix_text = Mage::getStoreConfig('autotwitt/general/prefix_text');
		
		$tt_message = $prefix_text . " " . Mage::getBaseUrl() . 'catalog/product/view/id/' . $product->getId() . " " . $product->getName();
		
		$this->postToTwitter($tt_message);		
	}
	
	function postToTwitter($message){
		$username = Mage::getStoreConfig('autotwitt/account/username');
		$password = Mage::getStoreConfig('autotwitt/account/password');
		
		$host = "http://twitter.com/statuses/update.xml?status=".urlencode(stripslashes(urldecode($message)));

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $host);
		curl_setopt($ch, CURLOPT_VERBOSE, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
		curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
		curl_setopt($ch, CURLOPT_POST, 1);

		$result = curl_exec($ch);
		// Look at the returned header
		$resultArray = curl_getinfo($ch);

		curl_close($ch);

		if($resultArray['http_code'] == "200"){
			 $twitter_status='Your message has been sended! <a href="http://twitter.com/'.$username.'">See your profile</a>';
		} else {
			 $twitter_status="Error posting to Twitter. Retry";
		}
		return $twitter_status;
	}	
}
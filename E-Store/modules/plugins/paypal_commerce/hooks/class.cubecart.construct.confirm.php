<?php
/**
 * CubeCart v6
 * ========================================
 * CubeCart is a registered trade mark of CubeCart Limited
 * Copyright CubeCart Limited 2019. All rights reserved.
 * UK Private Limited Company No. 5323904
 * ========================================
 * Web:   http://www.cubecart.com
 * Email:  sales@cubecart.com
 * License:  GPL-3.0 http://opensource.org/licenses/GPL-3.0
 */
$module_config = $GLOBALS['config']->get('paypal_commerce');
if(isset($_GET['paypal_commerce']) && !empty($_GET['paypal_commerce']) && $_GET['paypal_commerce']=='captureOrder' && isset($_GET['orderId'])) {
	
	$auth = isset($_GET['auth']) ? $_GET['auth'] : false;
	$card = isset($_GET['card']) ? $_GET['card'] : false;

	include_once (CC_ROOT_DIR.'/modules/plugins/paypal_commerce/paypal.class.php');
	$paypal = new paypalCheckout($module_config);
	$result = $paypal->settleOrder($_GET['orderId'], $auth, false, $card);

	header('Content-Type: application/json');

	if(is_array($result) && !isset($result['error'])) {
		if(!in_array($paypal->completeOrder($result), $paypal->successfulPaymentStatus)) { 
			echo json_encode(array('error' => 'Payment failed please try again or use a different method.')); exit;
		}
	}
	
	if (isset($result['error'])) {
		echo json_encode($result); exit;
	} else {
		if($result) {
			$result = array('error' => 'false');
		} else {
			$result = array('error' => 'true');
		}
		echo json_encode($result); exit;
	}
	exit;
} else if(isset($_GET['paypal_commerce']) && !empty($_GET['paypal_commerce']) && $_GET['paypal_commerce']=='getOrder' && isset($_GET['orderId'])) {
	$GLOBALS['session']->set('paypal_orderId', $_GET['orderId'], 'paypal_commerce');
	include_once (CC_ROOT_DIR.'/modules/plugins/paypal_commerce/paypal.class.php');
	
	$paypal = new paypalCheckout($module_config);
	$result = $paypal->getOrder($_GET['orderId']);

	if(!$result) {
		httpredir('index.php?_a=cancel&paypay_checkout_cancel=cancel&error=true');
	}
	
	if($result['status']=='APPROVED') {

		// Check customer isn't blocked
		if($GLOBALS['db']->select('CubeCart_customer', false, array('email' => $result['payer']['email_address'], 'status' => 0))) {
			$GLOBALS['gui']->setError("I'm sorry but we are not able to process your order at this time.");
			$GLOBALS['session']->delete('paypal_orderId','paypal_commerce');
			httpredir('index.php?_a=basket');
			exit;
		}
		$address_availble = isset($result['purchase_units'][0]['shipping']['address']['address_line_1']);
		$phone = (isset($result['payer']['phone']['phone_number']['national_number']) && !empty($result['payer']['phone']['phone_number']['national_number'])) ? $result['payer']['phone']['phone_number']['national_number'] : "<a href='https://cubecart.com/45'>Disabled</a>";
		$customer	= array(
			'title'			=> '',
			'first_name'	=> $result['payer']['name']['given_name'],
			'last_name'		=> $result['payer']['name']['surname'],
			'email'			=> $result['payer']['email_address'],
			'phone'			=> $address_availble ? $phone : '',
		);

		$state = $result['purchase_units'][0]['shipping']['address']['admin_area_1'];
		if(empty($state)) {
			$state = 'Undefined';
		}
		$field = strlen($state)==2 ? 'abbrev' : 'name';

		$address	= array(
			'company_name'	=> '',
			'title'			=> $customer['title'],
			'first_name'	=> $customer['first_name'],
			'last_name'		=> $customer['last_name'],
			'line1'			=> $address_availble ? $result['purchase_units'][0]['shipping']['address']['address_line_1'] : '',
			'line2'			=> '',
			'postcode'		=> $address_availble ? $result['purchase_units'][0]['shipping']['address']['postal_code'] : '',
			'town'			=> $address_availble ? $result['purchase_units'][0]['shipping']['address']['admin_area_2'] : '',

			'state_id'		=> $address_availble ? getStateFormat($state, $field, 'id') : '',
			'state'			=> $address_availble ? getStateFormat($state, $field, 'name') : '',
			'state_abbrev'	=> $address_availble ? getStateFormat($state, $field, 'abbrev') : '',

			'country'		=> $address_availble ? getCountryFormat($result['purchase_units'][0]['shipping']['address']['country_code'], 'iso', 'numcode') : '',
			'country_id'	=> $address_availble ? getCountryFormat($result['purchase_units'][0]['shipping']['address']['country_code'], 'iso', 'numcode') : '',
			'country_iso'	=> $address_availble ? $result['purchase_units'][0]['shipping']['address']['country_code'] : '',
			'country_iso3'	=> $address_availble ? getCountryFormat($result['purchase_units'][0]['shipping']['address']['country_code'], 'iso', 'iso3') : '',
			'user_defined'  => true

		);

		$this->_basket['customer']			= $customer;
		$this->_basket['billing_address']	= $address;
		$this->_basket['delivery_address']	= $address;
		$this->_basket['register']			= false;

		$GLOBALS['cart']->save();

		$address = array_merge($customer, $address);

		$address['customer_id'] = $customer_id;
		$address['billing'] = 1;
		$address['default'] = 1;
		$address['description'] = 'Default billing address';

		if(!$GLOBALS['user']->is()) {
			
			if($address_availble) {
				$customer['password'] = substr("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ",mt_rand(0,50),1).substr(md5(time()),1);
				$customer_id = $GLOBALS['user']->createUser($customer, false, 2);
				$GLOBALS['db']->update('CubeCart_sessions', array('customer_id' => $customer_id), array('session_id' => $GLOBALS['session']->getId()));

				$GLOBALS['db']->delete('CubeCart_addressbook', array('customer_id' => $customer_id));
				$GLOBALS['user']->saveAddress($address,$customer_id);
			} else {
				$GLOBALS['gui']->setNotify("Please enter your billing address.");
			}

			httpredir('index.php?_a=confirm');
		} else {
			
			if(strstr($customer['phone'],'Disabled')) unset($customer['phone']);
			unset($customer['email']); // It's taken so we can't update to this.
			$GLOBALS['db']->update('CubeCart_customer', $customer, array('customer_id' => $GLOBALS['user']->getId()));
			$GLOBALS['db']->delete('CubeCart_addressbook',array('customer_id' => $GLOBALS['user']->getId(), 'default' => 1, 'billing' => 1));
			$GLOBALS['user']->saveAddress($address);
			$button_text = $module_config['settlement']=='authorize' ? 'Authorize Payment' : 'Pay Now';
			$GLOBALS['gui']->setNotify("Please click &quot;$button_text&quot; to complete your order.");
		}
		httpredir('index.php?_a=checkout');
		exit;
	}
}
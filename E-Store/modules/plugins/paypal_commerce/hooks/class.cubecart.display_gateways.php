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
# Enable specific gateways for Website Payments Pro (Post-Checkout)
$module_config = $GLOBALS['config']->get('paypal_commerce');
if ($module_config['status']) {
	$desc = CC_ROOT_REL.'modules/plugins/paypal_commerce/images/us.jpg';
	$country = strtolower($GLOBALS['cart']->basket['billing_address']['country_iso']);
	if(file_exists(CC_ROOT_DIR."/modules/plugins/paypal_commerce/images/$country.jpg")) {
		$desc = CC_ROOT_REL."modules/plugins/paypal_commerce/images/$country.jpg";
	}
	
	if (isset($_POST['gateway']) || (isset($name) && !empty($name))) {
		$base_folder = isset($_POST['gateway']) ? $_POST['gateway'] : $name;
		if($base_folder=='paypal_commerce') {
			$gateways[0]	= array(
				'plugin'	=> true,
				'base_folder' => 'paypal_commerce',
				'folder'	=> 'paypal_commerce',
				'desc'		=> $desc,
			);
		}
	} else {
		$gateways[99]	= array(
			'plugin'	=> true,
			'base_folder' => 'paypal_commerce',
			'folder'	=> 'paypal_commerce',
			'desc'		=> $desc,
			'default'	=> true
		);
	}
}
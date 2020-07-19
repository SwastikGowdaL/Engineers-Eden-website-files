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
if ($module_config['status']) {
	if ($GLOBALS['session']->has('paypal_orderId','paypal_commerce')) {
		include_once (CC_ROOT_DIR.'/modules/plugins/paypal_commerce/paypal.class.php');
		$orderId = $GLOBALS['session']->get('paypal_orderId','paypal_commerce');
		$paypal = new paypalCheckout($module_config);
		if($module_config['settlement']=='capture') {
			$capture = true;
		} else {
			$capture = false;
		}
		$result = $paypal->settleOrder($orderId, 0, true);
		if($result === 'error') {
			httpredir("index.php?_a=cancel&paypay_checkout_cancel=cancel");
			exit;
		}
		if(in_array($paypal->completeOrder($result), $paypal->successfulPaymentStatus)) { 
			httpredir('index.php?_a=complete');
		} else {
			$GLOBALS['gui']->setError("Payment failed please try again or use a different method.");
			httpredir("index.php?_a=cancel&paypay_checkout_cancel=cancel");
		}
		exit;
	} 
}
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
if ($module_config = $GLOBALS['config']->get('paypal_commerce')) {
	if ($GLOBALS['session']->has('paypal_orderId','paypal_commerce')) {
		//$load_checkouts = false;
		if (in_array($_GET['_a'],array('checkout', 'confirm')) && (isset($this->_basket['shipping']) || isset($this->_basket['digital_only']))) {
			if(empty($this->_basket['billing_address']['line1'])) {
				$GLOBALS['smarty']->assign('CHECKOUT_BUTTON', "Continue");
			} else if($module_config['settlement']=='authorize') {
				$GLOBALS['smarty']->assign('CHECKOUT_BUTTON', "Authorize Payment");
			} else {
				$GLOBALS['smarty']->assign('CHECKOUT_BUTTON', "Pay Now");
			}
			$GLOBALS['smarty']->assign('DISABLE_GATEWAYS', true);
			$html = '<h2>Payment Method</h2><p style=\"line-height:26px;\"><img src=\"https://www.paypalobjects.com/webstatic/mktg/Logo/pp-logo-150px.png\" alt=\"PayPal Logo\" title=\"Payment will be sent from '.$GLOBALS['session']->get('payer', 'paypal_commerce').'.\"><br><strong>PayPal account:</strong> '.$GLOBALS['session']->get('payer', 'paypal_commerce').'</p>';
			$list_checkouts[99] = '<script>$("#delivery_comments").after("'.$html.'");</script>';
			$list_checkouts[99] .= '<a href="?_a=cancel&paypay_checkout_cancel=cancel">Cancel Order</a>';

			$GLOBALS['smarty']->assign('TERMS_CONDITIONS', (!$GLOBALS['config']->get('config', 'disable_checkout_terms') && $terms = $GLOBALS['db']->select('CubeCart_documents', false, array('doc_terms' => '1'))) ? $GLOBALS['seo']->buildURL('doc', $terms[0]['doc_id'], '&') : false);
			$GLOBALS['smarty']->assign('ALTERNATE_TERMS', $this->_basket['terms_agree']=='1' ? '1':'0');
		} else {
			$GLOBALS['smarty']->assign('DISABLE_CHECKOUT_BUTTON', true);
		}
	} else {	
		$scope = (isset($module_config['scope']) && !empty($module_config['scope']) && ($module_config['scope']=='main' && $GLOBALS['gui']->mobile) || ($module_config['scope']=='mobile' && !$GLOBALS['gui']->mobile)) ? false : true;

		if ($module_config['status'] && $scope && $_GET['_a']=='basket') {
			if(is_numeric($module_config['position']) && !isset($list_checkouts[$module_config['position']])) {
				$position = $module_config['position'];
			} else {
				$position = '';
			}
			if(!isset($list_checkouts)) {
				$list_checkouts = array();
			}
			require_once(CC_ROOT_DIR.'/modules/plugins/paypal_commerce/paypal.class.php');
			$paypal = new paypalCheckout($module_config, false);
			array_unshift($list_checkouts , $paypal->renderSmartButton());
		}
	}
}
$payment_choices = $GLOBALS['db']->count('CubeCart_modules', array('module_id'), array('status' => 1, 'module' => 'gateway')); 
if(!$payment_choices) {
	$GLOBALS['smarty']->assign('DISABLE_GATEWAYS', true);
}
<?php
/**
 * CubeCart v6
 * ========================================
 * CubeCart is a registered trade mark of CubeCart Limited
 * Copyright CubeCart Limited 2014. All rights reserved.
 * UK Private Limited Company No. 5323904
 * ========================================
 * Web:   http://www.cubecart.com
 * Email:  sales@cubecart.com
 * License:  GPL-3.0 http://opensource.org/licenses/GPL-3.0
 */
class Gateway {
	private $_module;
	private $_basket;	
	private $_paypal;

	public function __construct($module = false, $basket = false) {
		$this->_module	= $module;
		$this->_basket =& $GLOBALS['cart']->basket;
		require_once(CC_ROOT_DIR.'/modules/plugins/paypal_commerce/paypal.class.php');
				
	}
	public function call() {
		if (!function_exists('getallheaders')) {
            function getallheaders() {
                $headers = [];
                foreach ($_SERVER as $name => $value) {
					if(substr($name, 0, 12) == 'HTTP_PAYPAL_') {
						// e.g. convert HTTP_PAYPAL_AUTH_ALGO to PAYPAL-AUTH-ALGO
						$headers[str_replace('_','-', substr($name, 5))] = $value;
					} else if (substr($name, 0, 5) == 'HTTP_') {
                        $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
                    }
                }
                return $headers;
            }
		}
		$headers = getallheaders(); // Array
		$body = file_get_contents('php://input'); // JSON String
		$this->_paypal = new paypalCheckout($this->_module);
		$verify = $this->_paypal->verifySignature($headers, $body);

		if(isset($verify['verification_status']) && $verify['verification_status']=='SUCCESS') {
			$body = json_decode($body, true);
			$cart_order_id = $body['resource']['invoice_id'];
			$order         = Order::getInstance();
			$order_summary = $order->getSummary($cart_order_id);

			switch($body['event_type']) {
				case 'PAYMENT.CAPTURE.COMPLETED':
					// Update order if all funds are captured
					if (!in_array((int)$order_summary['status'], array(2,3)) && $body['resource']['final_capture'] && $this->_module['status_change_time'] !== 'authorize') {
						$order->paymentStatus(Order::PAYMENT_SUCCESS, $cart_order_id);
						$order->orderStatus(Order::ORDER_PROCESS, $cart_order_id);
					}
				break;
				case 'PAYMENT.CAPTURE.REFUNDED':
				case 'PAYMENT.CAPTURE.REVERSED':
					// Cancel order if refund is full amount
					if ($body['resource']['amount']['value']==$cart_order_id['total']) {
						$order->paymentStatus(Order::PAYMENT_CANCEL, $cart_order_id);
						$order->orderStatus(Order::ORDER_CANCELLED, $cart_order_id);
					}
				break;
				case 'PAYMENT.AUTHORIZATION.VOIDED':
				case 'PAYMENT.CAPTURE.DENIED':
					if($this->_module['settlement'] == 'authorize') {
						$order->paymentStatus(Order::PAYMENT_CANCEL, $cart_order_id);
						$order->orderStatus(Order::ORDER_CANCELLED, $cart_order_id);
					}
				break;
			}

			$notes = array(
				$body['summary']
			);
			if(isset($result['payer']['payer_id'])) {
				array_push($notes, 'PayerID: '.$result['payer']['payer_id']);
			}
			$transData['notes']       = implode("<br>", $notes);
			$transData['gateway']     = 'PayPal Commerce';
			$transData['order_id']    = $cart_order_id;
			$transData['trans_id']    = $body['resource']['id'];
			$transData['amount']      = $body['resource']['amount']['value'];
			$transData['status']      = $body['resource']['status'];
			$transData['customer_id'] = $order_summary['customer_id'];
			$order->logTransaction($transData);

		} else {
			trigger_error('Failed to verify PayPal webhook signature.');
		}
	}
	public function form() {
		$file_name = 'form.tpl';
		$form_file = $GLOBALS['gui']->getCustomModuleSkin('gateway', dirname(__FILE__), $file_name);
		$GLOBALS['gui']->changeTemplateDir($form_file);
		$this->_paypal = new paypalCheckout($this->_module);
		$smart_button = $this->_paypal->renderSmartButton(true);
		$GLOBALS['smarty']->assign("PAYPAL_HTML", $smart_button);
		$GLOBALS['smarty']->assign("DISPLAY_3DS", true);
		
		$ret = $GLOBALS['smarty']->fetch($file_name);
		$GLOBALS['gui']->changeTemplateDir();
		return $ret;
	}
	public function transfer() {}
	public function repeatVariables() {}
	public function fixedVariables() {}
	public function process() {}
}
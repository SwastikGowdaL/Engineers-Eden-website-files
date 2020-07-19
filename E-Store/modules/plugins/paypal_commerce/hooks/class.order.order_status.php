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
if($status_id==6) {
    if($paypal_index = $GLOBALS['db']->select('CubeCart_PayPal_auth_index', false, array('cubecart_order_id' => $order_id))) {
        // Void auth
        include_once (CC_ROOT_DIR.'/modules/plugins/paypal_commerce/paypal.class.php');
        $module_config = $GLOBALS['config']->get('paypal_commerce');
        $paypal = new paypalCheckout($module_config);
        if($paypal->voidOrder($paypal_index[0]['auth_id'])) {
            $result = "SUCCESS";
            $transData['notes']       = 'Void';
        } else {
            $result = "FAIL";
            $transData['notes']       = 'Void unsuccessful. Please check the request log for further information.';
        }
        $order         = Order::getInstance();
        $order_summary = $order->getSummary($order_id);
        
        $transData['gateway']     = 'PayPal Commerce';
        $transData['order_id']    = $cart_order_id;
        $transData['trans_id']    = $paypal_index[0]['auth_id'];
        $transData['amount']      = $order_summary['total'];
        $transData['status']      = $result;
        $transData['customer_id'] = $order_summary['customer_id'];
        $order->logTransaction($transData);
    }
}
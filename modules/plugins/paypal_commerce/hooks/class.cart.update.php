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

 if ($GLOBALS['session']->has('paypal_orderId', 'paypal_commerce')) {
    include_once (CC_ROOT_DIR.'/modules/plugins/paypal_commerce/paypal.class.php');
    $module_config = $GLOBALS['config']->get('paypal_commerce');
    $orderId = $GLOBALS['session']->get('paypal_orderId', 'paypal_commerce');
    $paypal = new paypalCheckout($module_config);
    $result = $paypal->updateOrder($orderId);
    if(!$result) {
        $GLOBALS['gui']->setError('Failed to update PayPal order. Please try an alternate payment method or contact us for support.');
        //$GLOBALS['session']->delete('paypal_orderId', 'paypal_commerce');
    }
}

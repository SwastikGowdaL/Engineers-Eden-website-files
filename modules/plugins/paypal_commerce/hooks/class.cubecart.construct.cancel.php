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
if(!defined('CC_INI_SET')) die('Access Denied');
if(isset($_GET['paypay_checkout_cancel'])) {
    $GLOBALS['session']->delete('paypal_orderId','paypal_commerce');
    if(isset($_GET['error'])) {
        $GLOBALS['gui']->setError('Sorry but something went wrong please try again later or choose an alternate payment method.');
    }
    if($GLOBALS['session']->has('placement', 'paypal_commerce')) {
        $action = $GLOBALS['session']->get('placement', 'paypal_commerce');
    } else {
        $action = "basket";
    }
    httpredir("index.php?_a=$action");
    exit;
}

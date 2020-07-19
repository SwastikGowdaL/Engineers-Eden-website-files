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
if(isset($_GET['paypal_commerce']) && !empty($_GET['paypal_commerce']) && $_GET['paypal_commerce']=='createOrder'){
    $module_config = $GLOBALS['config']->get('paypal_commerce');
    require_once(CC_ROOT_DIR.'/modules/plugins/paypal_commerce/paypal.class.php');
    $paypal = new paypalCheckout($module_config);
    header('Content-Type: application/json');
    echo $paypal->createOrder(false, true);
    $GLOBALS['debug']->supress();
    exit;        
}
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
if ($GLOBALS['session']->has('paypal_orderId','paypal_commerce')) {
    
    $GLOBALS['smarty']->assign('QUAN_READ_ONLY', 'readonly="readonly"');
    $basket_change_attempt = false;
    $message = "Sorry but your basket can't be changed after signing in to PayPal. If you need to make changes please click &quot;Cancel PayPal Authorization&quot;";
    
    if(is_array($_POST['quan'])) {
        $basket = $GLOBALS['session']->get('', 'basket');
        foreach($_POST['quan'] as $hash => $quantity) {
            if($basket['contents'][$hash]['quantity']!==(int)$quantity) {
                $basket_change_attempt = true;
            }
        }
    } elseif($_POST['add']) {
        if ($GLOBALS['config']->get('config', 'basket_jump_to')==='0') {
            $GLOBALS['debug']->supress();
            $GLOBALS['gui']->setError($message);
            die('Redir:'.$GLOBALS['rootRel'].'index.php?_a=basket');
        }
        $basket_change_attempt = true;
    } elseif(isset($_GET['remove-item'])) {
        $basket_change_attempt = true;
    }

    if($basket_change_attempt) {
        $GLOBALS['gui']->setError($message);
        httpredir('index.php?_a=basket');
        exit;
    }
}
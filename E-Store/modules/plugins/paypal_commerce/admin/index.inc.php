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
if (!defined('CC_INI_SET')) {
    die('Access Denied');
}

$legacy_path = CC_ROOT_DIR.'/modules/plugins/paypal_checkout';
if(file_exists($legacy_path)) {
    $GLOBALS['db']->update('CubeCart_modules', array('folder' => 'paypal_commerce'), array('folder' => 'paypal_checkout'));
    $GLOBALS['db']->update('CubeCart_config', array('name' => 'paypal_commerce'), array('name' => 'paypal_checkout'));
    recursiveDelete($legacy_path);
    httpredir('?_g=plugins&type=plugins&module=paypal_commerce');
}

define('SELLER_NONCE', bin2hex(openssl_random_pseudo_bytes(64)));
define('SANDBOX', false);
define('API_ENDPOINT', SANDBOX ? 'api.sandbox.paypal.com' : 'api.paypal.com');
define('PARTNER_PAYER_ID', SANDBOX ? 'ZX5XT6TADE7JN' : 'HERPFL26KN7YY');
define('PARTNER_CLIENT_ID', SANDBOX ? 'AY-LybJ89uyujeUy6fFxKXeAeERW6XCf-rbLFDJ71IZrzk5BIoE8rKpWcrkGxXfaQ9MUJGlyXlulzW-F' : 'AbJf2mwvIkg3gR3CdWpgDV2idRETv8cz-4U3HFiLlXdEoqbObeum0AlvlUfb-9p3AE221nS2yOOVH_Km');

if (isset($_GET['disconnect'])) {
    $GLOBALS['session']->delete('', 'paypal_commerce');
    $GLOBALS['db']->delete('CubeCart_config', array('name' => 'paypal_commerce'));
    $GLOBALS['db']->delete('CubeCart_modules', array('folder' => 'paypal_commerce'));
    $GLOBALS['main']->successMessage('Account disconnected.');
    httpredir('?_g=plugins&type=plugins&module=paypal_commerce');
}

if (isset($_GET['merchantId']) && !empty($_GET['merchantId']) && isset($_GET['merchantIdInPayPal']) && !empty($_GET['merchantIdInPayPal'])) {
    $GLOBALS['db']->misc("CREATE TABLE IF NOT EXISTS `" . $GLOBALS['config']->get('config', 'dbprefix') . "CubeCart_PayPal_auth_index` (
		`paypal_order_id` varchar(32) NOT NULL,
		`cubecart_order_id` varchar(18) NOT NULL,
		`auth_id` varchar(32) NOT NULL,
		UNIQUE KEY `paypal_order_id` (`paypal_order_id`),
		UNIQUE KEY `cubecart_order_id` (`cubecart_order_id`) USING BTREE,
		KEY `auth_id` (`auth_id`)
	  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");

    $GLOBALS['config']->set('paypal_commerce', 'merchantIdInPayPal', $_GET['merchantIdInPayPal']);
    $GLOBALS['config']->set('paypal_commerce', 'merchantId', $_GET['merchantId']);
    // Defaults
    $GLOBALS['config']->set('paypal_commerce', 'status_change_time', 'capture');
    $GLOBALS['config']->set('paypal_commerce', 'settlement', 'capture');
    $GLOBALS['config']->set('paypal_commerce', 'smart_layout', 'horizontal');
    $GLOBALS['config']->set('paypal_commerce', 'smart_color', 'gold');
    $GLOBALS['config']->set('paypal_commerce', 'smart_shape', 'rect');
    $GLOBALS['config']->set('paypal_commerce', 'smart_label', 'checkout');
    $GLOBALS['config']->set('paypal_commerce', 'smart_tagline', 'true');
    $GLOBALS['config']->set('paypal_commerce', 'smart_fundingicons', 'false');
    $GLOBALS['config']->set('paypal_commerce', 'smart_size', 'small');
    $GLOBALS['config']->set('paypal_commerce', 'ccf', '1');
    $GLOBALS['config']->set('paypal_commerce', '3ds', '1');
    $GLOBALS['config']->set('paypal_commerce', 'install_time', time());

    // Disable all other PayPal integrations
    $GLOBALS['db']->update('CubeCart_modules', array('status' => 0), array('folder' => array("'Braintree'", "'PayPal'", "'PayPal_Pro'")));
    $GLOBALS['config']->set('Braintree', 'status', false);
    $GLOBALS['config']->set('PayPal', 'status', false);
    $GLOBALS['config']->set('PayPal_Pro', 'status', false);

    // Enable this one
    $GLOBALS['config']->set('paypal_commerce', 'status', true);
    if ($GLOBALS['db']->select('CubeCart_modules', 'status', array('folder' => 'paypal_commerce'))) {
        $GLOBALS['db']->update('CubeCart_modules', array('status' => 1), array('folder' => 'paypal_commerce'));
    } else {
        $GLOBALS['db']->insert('CubeCart_modules', array('module' => 'plugins', 'status' => 1, 'folder' => 'paypal_commerce'));
    }
    $GLOBALS['cache']->clear();
    $GLOBALS['main']->successMessage('Congratulations! You are now ready to start taking payments with PayPal Commerce.');
    httpredir('?_g=plugins&type=plugins&module=paypal_commerce');
}
if (isset($_GET['returnMessage']) && !empty($_GET['returnMessage'])) {
    $GLOBALS['main']->successMessage(urldecode($_GET['returnMessage']));
    httpredir('?_g=plugins&type=plugins&module=paypal_commerce');
    exit;
}

$input = json_decode(file_get_contents("php://input"), true);
if (isset($input['authCode']) && !empty($input['authCode']) && isset($input['sharedId']) && !empty($input['sharedId']) && isset($input['seller_nonce']) && !empty($input['seller_nonce'])) {

    $request = new Request(API_ENDPOINT, '/v1/oauth2/token');
    $request->customHeaders('PayPal-Request-Id: '.uniqid('', true));
    $request->authenticate($input['sharedId'], '');
    $request->customHeaders('Cache-Control: no-cache');
    $request->customHeaders('Content-Type: text/plain');
    $request->setData('grant_type=authorization_code&code=' . $input['authCode'] . '&code_verifier=' . $input['seller_nonce']);
    $request->skiplog(false);
    $request->setMethod('post');
    $request->cache(false);
    $request->setSSL();

    if ($response = $request->send()) {
        $data = json_decode($response, true);
        if (isset($data['access_token']) && !empty($data['access_token'])) {
            $request = new Request(API_ENDPOINT, '/v1/customer/partners/' . PARTNER_PAYER_ID . '/merchant-integrations/credentials');
            $request->customHeaders('PayPal-Request-Id: '.uniqid('', true));
            $request->customHeaders('Cache-Control: no-cache');
            $request->customHeaders('Content-Type: application/json');
            $request->customHeaders('Authorization: Bearer ' . $data['access_token']);
            $request->skiplog(false);
            $request->setMethod('get');
            $request->cache(false);
            $request->setSSL();
            if ($response = $request->send()) {
                $data = json_decode($response, true);
                if (isset($data['client_id']) && isset($data['client_secret'])) {
                    $GLOBALS['config']->set('paypal_commerce', 'client_id', $data['client_id']);
                    $GLOBALS['config']->set('paypal_commerce', 'client_secret', $data['client_secret']);
                    echo 'success';
                } else {
                    echo 'There was an error obtaining seller credential.';
                }
            } else {
                echo 'Failed to obtain seller credential.';
            }
        } else {
            echo 'Failed to obtain access token.';
        }
    } else {
        echo 'Failed to authorize.';
    }
    exit;
}

$module = new Module(__FILE__, $_GET['module'], 'admin/index.tpl', true, false);
$connected = isset($module->_settings['connected']);

$client_id = $GLOBALS['config']->get('paypal_commerce', 'client_id');
$client_secret = $GLOBALS['config']->get('paypal_commerce', 'client_secret');
if (!empty($client_id) && !empty($client_secret)) {
    $connected = true;

    require_once CC_ROOT_DIR . '/modules/plugins/paypal_commerce/paypal.class.php';
    $paypal = new paypalCheckout($module->_settings);
    $access_token = $paypal->getAccessToken();
    if ($access_token) {
        if (empty($module->_settings['primary_email'])) {
            $order = $paypal->createOrder(true);
            $order = $paypal->getOrder($order['id']);
            if (isset($order['purchase_units'][0]['payee']["email_address"])) {
                $GLOBALS['config']->set('paypal_commerce', 'primary_email', $order['purchase_units'][0]['payee']["email_address"]);
                httpredir('?_g=plugins&type=plugins&module=paypal_commerce');
                exit;
            }
        }
    } else {
        $GLOBALS['main']->errorMessage('Failed verify merchant credentials');
    }

    if (isset($_GET['delete_webhook'])) {
        $paypal->deleteWebhook($_GET['delete_webhook']);
        httpredir('?_g=plugins&type=plugins&module=paypal_commerce');
        exit;
    }

    // List webhooks
    $webhooks = $paypal->listWebhooks();
    if (!isset($module->_settings['webhook_id']) || $module->_settings['webhook_id'] !== $webhooks['id']) {
        $GLOBALS['config']->set('paypal_commerce', 'webhook_id', $webhooks['id']);
    }
    if(preg_match('/paypal_checkout$/', $webhooks['url'])) { # fix beta hangover
        $paypal->deleteWebhook($webhooks['id']);
        httpredir('?_g=plugins&type=plugins&module=paypal_commerce');
        exit;
    }
}

$matrix_3dx = array(
    array(
        'desc' => $GLOBALS['language']->paypal_commerce['undefined_undefined_undefined'],
        'name' => 'undefined_undefined_undefined',
        'value' => isset($module->_settings['rule']['undefined_undefined_undefined']) ? $module->_settings['rule']['undefined_undefined_undefined'] : '1',
        'default' => '1'
    ),
    array(
        'desc' => $GLOBALS['language']->paypal_commerce['false_ERROR_ERROR'],
        'name' => 'false_ERROR_ERROR',
        'value' => isset($module->_settings['rule']['false_ERROR_ERROR']) ? $module->_settings['rule']['false_ERROR_ERROR'] : '0',
        'default' => '0'
    ),
    array(
        'desc' => $GLOBALS['language']->paypal_commerce['false_NO_SKIPPED_BY_BUYER'],
        'name' => 'false_NO_SKIPPED_BY_BUYER',
        'value' => isset($module->_settings['rule']['false_NO_SKIPPED_BY_BUYER']) ? $module->_settings['rule']['false_NO_SKIPPED_BY_BUYER'] : '0',
        'default' => '0'
    ),
    array(
        'desc' => $GLOBALS['language']->paypal_commerce['false_NO_FAILURE'],
        'name' => 'false_NO_FAILURE',
        'value' => isset($module->_settings['rule']['false_NO_FAILURE']) ? $module->_settings['rule']['false_NO_FAILURE'] : '0',
        'default' => '0'
    ),
    array(
        'desc' => $GLOBALS['language']->paypal_commerce['false_NO_BYPASSED'],
        'name' => 'false_NO_BYPASSED',
        'value' => isset($module->_settings['rule']['false_NO_BYPASSED']) ? $module->_settings['rule']['false_NO_BYPASSED'] : '0',
        'default' => '0'
    ),
    array(
        'desc' => $GLOBALS['language']->paypal_commerce['false_NO_ATTEMPTED'],
        'name' => 'false_NO_ATTEMPTED',
        'value' => isset($module->_settings['rule']['false_NO_ATTEMPTED']) ? $module->_settings['rule']['false_NO_ATTEMPTED'] : '1',
        'default' => '1'
    ),
    array(
        'desc' => $GLOBALS['language']->paypal_commerce['false_NO_UNAVAILABLE'],
        'name' => 'false_NO_UNAVAILABLE',
        'value' => isset($module->_settings['rule']['false_NO_UNAVAILABLE']) ? $module->_settings['rule']['false_NO_UNAVAILABLE'] : '0',
        'default' => '0'
    ),
    array(
        'desc' => $GLOBALS['language']->paypal_commerce['false_NO_CARD_INELIGIBLE'],
        'name' => 'false_NO_CARD_INELIGIBLE',
        'value' => isset($module->_settings['rule']['false_NO_CARD_INELIGIBLE']) ? $module->_settings['rule']['false_NO_CARD_INELIGIBLE'] : '1',
        'default' => '1'
    )
);



$onboard_domain = SANDBOX ? 'www.sandbox.paypal.com' : 'www.paypal.com';
$return_url = CC_STORE_URL . '/' . $GLOBALS['config']->get('config', 'adminFile') . '?_g=plugins&type=plugins&module=paypal_commerce';
$logo_url = 'https://www.cubecart.com/img/paypal_commerce/cubecart-logo.png';

$onboard_params = array(
    'partnerId' => PARTNER_PAYER_ID,
    'partnerClientId' => PARTNER_CLIENT_ID,
    'features' => 'PAYMENT,REFUND',
    'integrationType' => 'FO',
    'returnToPartnerUrl' => $return_url,
    'displayMode' => 'minibrowser',
    'sellerNonce' => SELLER_NONCE,
    'partnerLogoUrl' => $logo_url,
    'displayMode' => 'minibrowser'
);

$onboard_url = "https://$onboard_domain/bizsignup/partner/entry?";

if(!isset($module->_settings['install_time'])) {
    $show_disconnect = true;
} else {
    $show_disconnect = ($module->_settings['install_time']+180 > time()) ? false : true;
}

$template_vars = array(
    'connected' => $connected,
    'seller_nonce' => SELLER_NONCE,
    'country' => $GLOBALS['config']->get('config', 'store_country'),
    'webhooks' => $webhooks,
    'sandbox' => SANDBOX,
    'nonce' => SELLER_NONCE,
    'matrix_3dx' => $matrix_3dx,
    'ec_link' => $onboard_url.http_build_query(array_merge($onboard_params, array('product' => 'EXPRESS_CHECKOUT'))),
    'ppcp_link' => $onboard_url.http_build_query(array_merge($onboard_params, array('product' => 'ppcp'))),
    'show_disconnect' =>  $show_disconnect
);

$module->assign_to_template($template_vars);
$module->fetch();
$page_content = $module->display();
if (isset($input['module'])) {
    httpredir('?_g=plugins&type=plugins&module=paypal_commerce');
}
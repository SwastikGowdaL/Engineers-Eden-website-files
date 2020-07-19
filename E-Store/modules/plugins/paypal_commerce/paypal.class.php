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
class paypalCheckout
{
    private $_sandbox = false; // Set to false for live!!
    private $_bn_code = '';
    private $_api_endpoint = '';
    private $_api_endpoint_sandbox = 'api.sandbox.paypal.com';
    private $_api_endpoint_live = 'api.paypal.com';
    private $_http_endpoint = 'www.paypal.com';
    private $_config = array();
    private $_access_token = '';
    private $_breakdown = true;

    private $_transaction_notes = array(); // 3d secure status
    private $_currency = '';
    private $_item_total = 0.00;
    private $_order_data = array();
    private $_auth_error_message;
    private $_curl_response_code = 0; // For stores older than 6.2.8
    private $_reference_id = '';
    
    public $successfulPaymentStatus = array('CREATED','PENDING','COMPLETED','APPROVED');

    public function __construct($config)
    {
        $this->_api_endpoint = $this->_sandbox ? $this->_api_endpoint_sandbox : $this->_api_endpoint_live;
        $this->_config = $config;
        $this->_currency = $GLOBALS['config']->get('config', 'default_currency');
        $this->_bn_code = $GLOBALS['config']->has('config', 'cid') ? 'CUBECARTLIMITED_Ecom_CubePCPHOST' : 'CUBECARTLIMITED_Cart_CubeCartPCP';
        
        if($GLOBALS['session']->has('reference_id', 'paypal_commerce')) {
            $this->_reference_id = $GLOBALS['session']->get('reference_id', 'paypal_commerce');
        } else {
            $this->_reference_id = $this->_request_id(false);
            $GLOBALS['session']->set('reference_id', $this->_reference_id, 'paypal_commerce');
        }
        $this->_access_token();
        // We disable breakdown for tax inclusive items to prevent crash
        foreach($GLOBALS['cart']->basket['contents'] as $item) {
            $product = $GLOBALS['catalogue']->getProductData($item['id']);
            if($product['tax_inclusive']=='1') {
                $this->_breakdown = false;
                break;
            }
        }
    }
    private function _request_id($more_entropy = true) {
        return uniqid('', $more_entropy);
    }
    private function _access_token()
    {
        if ($GLOBALS['session']->has('token_expiry', 'paypal_commerce')) {
            $token_expiry = $GLOBALS['session']->get('token_expiry', 'paypal_commerce');
            if ($token_expiry < time()) {
                $GLOBALS['session']->delete('token_expiry', 'paypal_commerce');
                $GLOBALS['session']->delete('access_token', 'paypal_commerce');
            } else {
                $this->_access_token = $GLOBALS['session']->get('access_token', 'paypal_commerce');
                return;
            }
        }

        $request = new Request($this->_api_endpoint, '/v1/oauth2/token');
        $request->authenticate($this->_config['client_id'], $this->_config['client_secret']);
        $request->customHeaders('PayPal-Request-Id: '.$this->_request_id());
        $request->customHeaders('Accept-Language: en_US');
        $request->customHeaders('Accept: application/json');
        $request->setData('grant_type=client_credentials');
        $request->skiplog(false);
        $request->cache(false);
        $request->setSSL();
        $result = $request->send();

        if ($result) {
            $result = json_decode($result, true);
            if (isset($result['access_token'])) {
                $this->_access_token = $result['access_token'];
                $GLOBALS['session']->set('token_expiry', $result['expires_in'] + time(), 'paypal_commerce');
                $GLOBALS['session']->set('access_token', $result['access_token'], 'paypal_commerce');
            }
        }
    }
    public function completeOrder($result)
    {
        $seller_protection_key = ($this->_config['settlement'] == 'capture') ? 'captures' : 'authorizations';
        
        $cart_order_id = $GLOBALS['cart']->basket['cart_order_id'];
        if(empty($cart_order_id) && !empty($data['purchase_units'][0]['payments'][$seller_protection_key][0]['invoice_id'])) {
            $cart_order_id = $data['purchase_units'][0]['payments'][$seller_protection_key][0]['invoice_id'];
        }

        if ($this->_config['settlement'] == 'authorize') {
            $orderId = $GLOBALS['session']->get('paypal_orderId', 'paypal_commerce');
            $authId = $result['purchase_units'][0]['payments']['authorizations'][0]['id'];
            $GLOBALS['db']->misc("REPLACE INTO `" . $GLOBALS['config']->get('config', 'dbprefix') . "CubeCart_PayPal_auth_index` SET `paypal_order_id` = '$orderId', `cubecart_order_id` = '$cart_order_id', `auth_id` = '$authId'");
        }

        $GLOBALS['session']->delete('paypal_orderId', 'paypal_commerce');
        
        // Log order_ids against
        $order = Order::getInstance();
        $order_summary = $order->getSummary($cart_order_id);

        $status = $result['purchase_units'][0]['payments'][$seller_protection_key][0]['status'];
    
        $seller_protection = isset($result["purchase_units"][0]["payments"][$seller_protection_key][0]["seller_protection"]["status"]) ? $result["purchase_units"][0]["payments"][$seller_protection_key][0]["seller_protection"]["status"] : 'Not available';

        if (in_array($status, array('COMPLETED','APPROVED','CREATED'))) {
            $GLOBALS['session']->delete('reference_id', 'paypal_commerce');
            if(($this->_config['settlement'] == 'capture' && !in_array((int)$order_summary['status'], array(2,3))) || ($this->_config['settlement'] == 'authorize' && $this->_config['status_change_time'] == 'authorize')) {
                $order->paymentStatus(Order::PAYMENT_SUCCESS, $cart_order_id);
                $order->orderStatus(Order::ORDER_PROCESS, $cart_order_id); 
            }
        }
        
        $notes = array(
            "Seller Protection: " . $seller_protection,
            "Settlement Mode: " . ucfirst($this->_config['settlement']),
        );
        if(isset($result['payer']['payer_id'])) {
            array_push($notes, 'PayerID: '.$result['payer']['payer_id']);
        }
        if(isset($result['purchase_units'][0]['payments'][$seller_protection_key][0]['status_details']['reason'])) {
            array_push($notes, 'Reason Code: '.$result['purchase_units'][0]['payments'][$seller_protection_key][0]['status_details']['reason']);
        }
        $notes = array_merge($notes, $this->_transaction_notes);
        $transData['notes'] = implode("<br>", $notes);
        $transData['gateway'] = 'PayPal Commerce';
        $transData['order_id'] = $cart_order_id;
        $transData['trans_id'] = $result['id'];
        $transData['amount'] = $result['seller_receivable_breakdown']['gross_amount'];
        $transData['status'] = $status;
        $transData['customer_id'] = $order_summary['customer_id'];
        $order->logTransaction($transData);
        return $status;
    }

    public function createOrder($test = false, $json = false)
    {
        if ($test) { // Used to obtain payee->email_address during onboarding
            $this->_order_data = array(
                'intent' => strtoupper($this->_config['settlement']),
                'purchase_units' => array(
                    0 => array(
                        'reference_id' => $this->_reference_id,
                        'amount' => array(
                            'currency_code' => $this->_currency,
                            'value' => 10.00,

                        ),
                    ),
                ),
            );
        } else {
            $GLOBALS['session']->delete('invoice_id_set', 'paypal_commerce');
            $this->_buildOrder();
        }
        $request = new Request($this->_api_endpoint, '/v2/checkout/orders/');
        $request->customHeaders('PayPal-Request-Id: '.$this->_request_id());
        $request->customHeaders('Content-Type: application/json');
        $request->customHeaders('Authorization: Bearer ' . $this->_access_token);
        $request->customHeaders('PayPal-Partner-Attribution-Id: '.$this->_bn_code);
        $request->customHeaders('prefer: return=representation');
        $request->setData(json_encode($this->_order_data));
        $request->skiplog(false);
        $request->cache(false);
        $request->setSSL();
        $result = $request->send();

        if ($this->_errorDetected($result)) {
            return $json ? json_encode('error') : 'error';
        }

        if ($result) {
            $array = json_decode($result, true);
            if ($test) {
                return $array;
            } elseif (isset($array['id'])) {
                return $json ? $result : $array['id'];
            }
            return false;
        }
    }
    public function createWebhooks()
    {
        $data = '{
        "url": "' . $GLOBALS['storeURL'] . '/index.php?_g=rm&type=plugins&cmd=call&module=paypal_commerce",
        "event_types": [
          {
            "name": "PAYMENT.CAPTURE.COMPLETED"
          },
          {
            "name": "PAYMENT.CAPTURE.REVERSED"
          },
          {
            "name": "PAYMENT.CAPTURE.REFUNDED"
          },
          {
            "name": "PAYMENT.AUTHORIZATION.VOIDED"
          },
          {
            "name": "PAYMENT.CAPTURE.DENIED"
          },
          {
            "name": "CHECKOUT.ORDER.APPROVED"
          },
          {
            "name": "CHECKOUT.ORDER.COMPLETED"
          },
          {
            "name": "PAYMENT.CAPTURE.PENDING"
          }
        ]
      }';
        $request = new Request($this->_api_endpoint, "/v1/notifications/webhooks");
        $request->customHeaders('PayPal-Request-Id: '.$this->_request_id());
        $request->customHeaders('Content-Type: application/json');
        $request->customHeaders('Authorization: Bearer ' . $this->_access_token);
        $request->setData($data);
        $request->setMethod('post');
        $request->skiplog(false);
        $request->cache(false);
        $request->setSSL();
        $result = $request->send();

        $data = json_decode($result, true);

        if(isset($data['id']) && !empty($data['id'])) {
            return array(
                'id' => $data['id'],
                'url' => $data['url'],
                'event_types' => $data['event_types'],
            );
        }
        return false;
    }
    public function getAccessToken()
    {
        return $this->_access_token;
    }
    private function _getClientToken()
    {
        $request = new Request($this->_api_endpoint, "/v1/identity/generate-token");
        $request->customHeaders('PayPal-Request-Id: '.$this->_request_id());
        $request->customHeaders('Content-Type: application/json');
        $request->customHeaders('Authorization: Bearer ' . $this->_access_token);
        $request->setMethod('post');
        $request->skiplog(false);
        $request->cache(false);
        $request->setSSL();
        $result = $request->send();
        if ($result) {
            $result = json_decode($result, true);
            if (isset($result['client_token'])) {
                return $result['client_token'];
            }
        }
        return false;
    }
    public function getOrder($orderId)
    {
        $request = new Request($this->_api_endpoint, "/v2/checkout/orders/$orderId");
        $request->customHeaders('PayPal-Request-Id: '.$this->_request_id());
        $request->customHeaders('Content-Type: application/json');
        $request->customHeaders('Authorization: Bearer ' . $this->_access_token);
        $request->setMethod('get');
        $request->skiplog(false);
        $request->cache(false);
        $request->setSSL();
        $result = $request->send();

        if ($this->_errorDetected($result)) {
            return 'error';
        }

        if ($result) {
            $result = json_decode($result, true);
            $GLOBALS['session']->set('payer', $result['payer']['email_address'], 'paypal_commerce');
            return $result;
        } else {
            return false;
        }
    }
    public function listWebhooks()
    {
        $request = new Request($this->_api_endpoint, "/v1/notifications/webhooks");
        $request->customHeaders('PayPal-Request-Id: '.$this->_request_id());
        $request->customHeaders('Content-Type: application/json');
        $request->customHeaders('Authorization: Bearer ' . $this->_access_token);
        $request->setMethod('get');
        $request->skiplog(false);
        $request->cache(false);
        $request->setSSL();
        $result = $request->send();

        if ($result) {
            $data = json_decode($result, true);
            if (isset($data['webhooks']) && count($data['webhooks']) === 0) {
                return $this->createWebhooks();
            } else {
                return array(
                    'id' => $data['webhooks'][0]['id'],
                    'url' => $data['webhooks'][0]['url'],
                    'event_types' => $data['webhooks'][0]['event_types'],
                );
            }
        }
        return false;
    }
    public function renderSmartButton($native_checkout = false)
    {
        if ($native_checkout) {
            $GLOBALS['session']->set('placement', 'gateway', 'paypal_commerce');
            $this->_config['smart_size'] = 'responsive';
            $this->_config['smart_label'] = 'pay';
            $this->_config['smart_layout'] = 'vertical';
            $this->_config['smart_fundingicons'] = 'true';
            $components = 'buttons,hosted-fields';
            $showHostedFields = ($this->_config['disable_cards']=='1') ? false : true;
            $commit = 'true';
            $onApprove = "function(payload, actions) {
          $('#paypal-button-container').hide();
          $('#paypal_commerce_loading').show();
          $('#paypal-card-wrapper').hide();
          return fetch('index.php?_a=checkout&paypal_commerce=captureOrder&orderId='+payload.orderID, {
            method: 'GET'
          }).then(function(res) {
            return res.json();
          }).then(function(response) {
              if(response==='error') {
                window.location.href = 'index.php?_a=gateway';
              } else {
                window.location.href = 'index.php?_a=complete';
              }
          });
        }";
        } else {
            $GLOBALS['session']->set('placement', 'basket', 'paypal_commerce');
            $components = 'buttons';
            $showHostedFields = false;
            $commit = 'false';
            $onApprove = "function(payload, actions) {
                window.location.href = 'index.php?_a=checkout&paypal_commerce=getOrder&orderId='+payload.orderID
        }";
        }

        if ($this->_config['smart_layout'] == 'vertical' && $this->_config['smart_size'] == 'small') {
            $this->_config['smart_size'] == 'medium';
        }

        if ($this->_config['smart_layout'] == 'horizontal') {
            $horizontal_styles = <<<EOT
    tagline: '{$this->_config['smart_tagline']}',
      fundingicons: '{$this->_config['smart_fundingicons']}',
EOT;
        }

        if ($showHostedFields) {
            $clientToken = ' data-client-token="' . $this->_getClientToken() . '"';
            if ($this->_config['3ds']) {
                $contingencies = "{contingencies:['3D_SECURE']}";
                $completeOrderParams = "+'&auth[liabilityShifted]='+payload.liabilityShifted
                +'&auth[authenticationStatus]='+payload.authenticationStatus
                +'&auth[authenticationReason]='+payload.authenticationReason";
            } else {
                $contingencies = '';
                $completeOrderParams = '';
            }

            $hostedFieldsJs = <<<EOT
if (paypal.HostedFields.isEligible() === true) {
    var form = document.querySelector('#paypal-card-container');
    var button_wrapper = document.querySelector('#paypal-button-container');
    var submit = document.querySelector('#button-pay');
    var card_wrapper = document.querySelector('#paypal-card-wrapper');
    card_wrapper.style.display = "block";
    paypal.HostedFields.render({
        createOrder: function (data, actions) {
            var orderId = "{$this->createOrder()}";
            if(orderId === 'error') {
                window.location.href = 'index.php?_a=cancel&paypay_checkout_cancel=cancel';
                return false;
            } else {
                return orderId;
            }
        },
        styles: {
            'input': {
                'color': '#000',
                'font-size': '16px',
                'transition': 'color 0.1s',
                'line-height': '3',
                'font-family': '"Courier New", Courier, "Lucida Sans Typewriter"'
            },
            'input.valid': {
                'color': '#43AC6A'
            },
            'input.invalid': {
                'color': '#E53A40',
            },
            '::-webkit-input-placeholder': {
                'color': 'rgba(0,0,0,0.6)'
            },

            ':-moz-placeholder': {
                'color': 'rgba(0,0,0,0.6)'
            },

            '::-moz-placeholder': {
                'color': 'rgba(0,0,0,0.6)'
            },

            ':-ms-input-placeholder': {
                'color': 'rgba(0,0,0,0.6)'
            }
        },
        fields: {
            number: {
                selector: '#card-number',
                placeholder: ''
            },
            cvv: {
                selector: '#cvv',
                placeholder: '{$GLOBALS['language']->paypal_commerce['cvv_placeholder']}'
            },
            expirationDate: {
                selector: '#expiration-date',
                placeholder: '{$GLOBALS['language']->paypal_commerce['expire_placeholder']}'
            }
        }
    }).then(function(hostedFieldsInstance) {
        hostedFieldsInstance.on('blur', function(event) {});
        hostedFieldsInstance.on('focus', function(event) {
            cardErrorMessage('');
        });
        hostedFieldsInstance.on('validityChange', function(event) {
            var formValid = Object.keys(event.fields).every(function(key) {
                return event.fields[key].isValid;
            });
            if (formValid) {
                $('#button-pay').addClass('show-button');
            } else {
                $('#button-pay').removeClass('show-button');
            }
        });
        hostedFieldsInstance.on('notEmpty', function(event) {});
        hostedFieldsInstance.on('empty', function(event) {
            $('header').removeClass('header-slide');
            $('#card-image').removeClass();
            $(form).removeClass();
        });
        hostedFieldsInstance.on('cardTypeChange', function(event) {
            if (event.cards.length === 1) {
                $(form).removeClass().addClass(event.cards[0].type);
                $('#card-image').removeClass().addClass(event.cards[0].type);
                $('header').addClass('header-slide');
                if (event.cards[0].code.size === 4) {
                    hostedFieldsInstance.setAttribute({
                        field: 'cvv',
                        attribute: 'placeholder',
                        value: '1234'
                    });

                }
            } else {
                hostedFieldsInstance.setAttribute({
                    field: 'cvv',
                    attribute: 'placeholder',
                    value: '123'
                });
            }
        });
        var process = true;
        submit.addEventListener('click', function (event) {
            event.preventDefault();
            $('#button-pay').html("{$GLOBALS['language']->paypal_commerce['please_wait']}");
            hostedFieldsInstance.submit($contingencies).then(function (payload) {
                if(process==true) {
                    process = false;
                    $('#paypal-button-container').hide();
                    $('#paypal_commerce_loading').show();
                    $('#paypal-card-wrapper').hide();
                    return fetch('index.php?_a=checkout&paypal_commerce=captureOrder&orderId='+payload.orderId$completeOrderParams+'&card=true', {
                        method: 'GET'
                    }).then(function(res) {
                        return res.json();
                    }).then(function(response) {
                        $('#button-pay').removeClass('show-button');
                        $('#button-pay').text("Make Payment");
                        process = true;
                        if(response === 'error') { // API ERROR
                            window.location.href = 'index.php?_a=cancel&paypay_checkout_cancel=cancel&error=true';
                            return false;
                        }
                        if(response.error === 'false') {
                            window.location.href = 'index.php?_a=complete';
                            return false;
                        } else if (response.error === 'true') { // card errors
                            window.location.href = 'index.php?_a=cancel&paypay_checkout_cancel=cancel&error=true';
                        } else {
                            $('#paypal-button-container').show();
                            $('#paypal_commerce_loading').hide();
                            $('#paypal-card-wrapper').show();
                            $('#card-image').removeClass();
                            hostedFieldsInstance.clear('number');
                            hostedFieldsInstance.clear('cvv');
                            hostedFieldsInstance.clear('expirationDate');
                            cardErrorMessage(response.error);
                        }
                    });
                }
            }).catch(error => cardErrorMessage(error.name));   
        });
    });
    function cardErrorMessage(message) {
        var error_message = document.querySelector('#error-message');
        switch(message) {
            case 'SERVICE_UNAVAILABLE':
                message = 'Service unavailable. Please try again later.';
            break;
        }
        error_message.innerHTML = message;
        if(message=='') {
            error_message.style.display = "none";
        } else {
            error_message.style.display = "block";
        }
    }
} else {
    console.log('Card processing is not enabled for this merchant.');
}
EOT;

            $root_path = CC_ROOT_DIR . '/modules/plugins/paypal_commerce/skin/';
            $path = file_exists($root_path . 'card_form_custom.tpl') ? $root_path . 'card_form_custom.tpl' : $root_path . 'card_form.tpl';
            $paypalHtml = $GLOBALS['smarty']->fetch($path);
        } else {
            $hostedFieldsJs = '';
            $text_align = $_GET['_a'] == 'gateway' ? 'center' : 'right';
            $paypalHtml = <<<EOT
                <style>
                #paypal-button-container, #paypal-button-container div {
                    text-align: $text_align;
                    padding-right: 5px;
                }
                @media screen and (min-width: 420px) {
                    #paypal-button-container div {
                        max-width: 208px;
                        
                        text-align: center;
                    }
                }
                </style>
                <div id="paypal-button-container"></div>

EOT;
            $clientToken = '';
        }

        $currency = $GLOBALS['config']->get('config', 'default_currency');

        $sandbox_params = '';
        if ($this->_sandbox) {
            $debug = ($this->_config['debug'] == '0' || !isset($this->_config['debug'])) ? 'false' : 'true';
            if ($this->_config['debug']) {
                $sandbox_params .= '&debug=' . $debug;
            }

            if (!empty($this->_config['buyer_country'])) {
                $sandbox_params .= '&buyer-country=' . $this->_config['buyer_country'];
            }

            if (!empty($this->_config['locale'])) {
                $sandbox_params .= '&locale=' . $this->_config['locale'];
            }

        }

        return <<<EOT
<script src="https://{$this->_http_endpoint}/sdk/js?client-id={$this->_config['client_id']}&merchant-id={$this->_config['merchantIdInPayPal']}&currency=$currency&intent={$this->_config['settlement']}&commit=$commit&components=$components$sandbox_params" data-partner-attribution-id="{$this->_bn_code}"$clientToken></script>
$paypalHtml
<script>
    paypal.Buttons({
      style: {
        size:  '{$this->_config['smart_size']}',
        label: '{$this->_config['smart_label']}',
        shape: '{$this->_config['smart_shape']}',
        color: '{$this->_config['smart_color']}',
        layout: '{$this->_config['smart_layout']}',
        $horizontal_styles
    },
    createOrder: function(data, actions) {
        return fetch('index.php?_a=basket&paypal_commerce=createOrder', {
          method: 'GET'
        }).then(function(res) {
          return res.json();
        }).then(function(data) {
            if(data==='error') {
                window.location.href = 'index.php?_a=cancel&paypay_checkout_cancel=cancel&error=true';
                return false;
            }
            return data.id;
        }).catch(error => console.log(error.message));
    },
    onApprove: $onApprove,
    onCancel: function(data, actions) {
        window.location.href = 'index.php?_a=cancel&paypay_checkout_cancel=cancel';
    },
    onError: function (err) {
        window.location.href = 'index.php?_a=cancel&paypay_checkout_cancel=cancel&error=true';
    }
  }).render('#paypal-button-container');
  $hostedFieldsJs
</script>
EOT;
    }
    public function settleOrder($orderId, $auth = false, $update = false, $card = false)
    {

        if ($card) {
            $this->_transaction_notes[] = 'Payment Method: Card';
        }
        
        if (is_array($auth) && !$this->_authSuccess($auth, $orderId)) {
            return array('error' => $this->_auth_error_message);
        }
        
        if ($update && !$this->updateOrder($orderId)) {
            return false;
        }

        $request = new Request($this->_api_endpoint, "/v2/checkout/orders/$orderId/" . $this->_config['settlement']);
        $request->customHeaders('PayPal-Request-Id: '.$this->_request_id());
        $request->customHeaders('Content-Type: application/json');
        $request->customHeaders('PayPal-Partner-Attribution-Id: '.$this->_bn_code);
        $request->customHeaders('Authorization: Bearer ' . $this->_access_token);
        // Error Handling Testing!
        //$request->customHeaders('PayPal-Mock-Response: {"mock_application_codes": "DUPLICATE_INVOICE_ID"}');
        $request->setMethod('post');
        $request->skiplog(false);
        $request->cache(false);
        $request->setSSL();
        $result = $request->send();

        if ($this->_errorDetected($result)) {
            return 'error';
        }

        if ($result) {
            return json_decode($result, true);
        }
        return false;

    }
    public function updateOrder($orderId)
    {
        $items = $this->_items();
        $breakdown = $this->_breakdown();
        if($this->_breakdown) {
            $amount = array(
                'value' => $GLOBALS['cart']->basket['total'],
                'currency_code' => $this->_currency,
                'breakdown' => $breakdown,
            );
        } else {
            $amount = array(
                'value' => $GLOBALS['cart']->basket['total'],
                'currency_code' => $this->_currency
            );
        }
        
        $patchData = array();
        
        $patchData[] = array(
            'op' => 'replace',
            'path' => '/purchase_units/@reference_id==\''.$this->_reference_id.'\'/amount',
            'value' => $amount,
        );
        if (isset($GLOBALS['cart']->basket['cart_order_id'])) {
            $action = $GLOBALS['session']->has('invoice_id_set', 'paypal_commerce') ? 'replace' : 'add';
            $patchData[] = array(
                'op' => $action,
                'path' => '/purchase_units/@reference_id==\''.$this->_reference_id.'\'/invoice_id',
                'value' => $GLOBALS['cart']->basket['cart_order_id'],
            );

            $patchData[] = array(
                'op' => $action,
                'path' => '/purchase_units/@reference_id==\''.$this->_reference_id.'\'/description',
                'value' => 'Payment for order ' . $GLOBALS['cart']->basket['cart_order_id'],
            );
        }
        if (!isset($GLOBALS['cart']->basket['digital_only']) && isset($GLOBALS['cart']->basket['delivery_address']['user_defined']) && $GLOBALS['cart']->basket['delivery_address']['user_defined'] == 1) {
            $patchData[] = array(
                'op' => 'replace',
                'path' => '/purchase_units/@reference_id==\''.$this->_reference_id.'\'/shipping/name',
                'value' => $this->_shippingAddress('name'),
            );
            $patchData[] = array(
                'op' => 'replace',
                'path' => '/purchase_units/@reference_id==\''.$this->_reference_id.'\'/shipping/address',
                'value' => $this->_shippingAddress('address'),
            );

        }
        /* HANDY FOR DEBUG TO SEE ERROR FOR EACH PATCH
        foreach($patchData as $patch) {
        $this->_patchOrder($orderId, array(0=>$patch));
        }
        return true;
         */
        return $this->_patchOrder($orderId, $patchData);
    }
    public function deleteWebhook($webhookId)
    {
        $request = new Request($this->_api_endpoint, "/v1/notifications/webhooks/$webhookId");
        $request->customHeaders('PayPal-Request-Id: '.$this->_request_id());
        $request->customHeaders('Content-Type: application/json');
        $request->customHeaders('Authorization: Bearer ' . $this->_access_token);
        $request->customOption(CURLOPT_CUSTOMREQUEST, 'DELETE');
        $request->setMethod('GET');
        $request->skiplog(false);
        $request->cache(false);
        $request->setSSL();
        return $request->send();
    }
    public function voidOrder($authId)
    {
        $request = new Request($this->_api_endpoint, "/v2/payments/authorizations/$authId/void");
        $request->customHeaders('PayPal-Request-Id: '.$this->_request_id());
        $request->customHeaders('Content-Type: application/json');
        $request->customHeaders('Authorization: Bearer ' . $this->_access_token);
        $request->setMethod('post');
        $request->skiplog(false);
        $request->cache(false);
        $request->setSSL();
        $result = $request->send();

        if ($this->_errorDetected($result)) {
            return 'error';
        }

        if (empty($result) && $request->server_response_code == 204) {
            return true;
        }
        trigger_error("Failed to void PayPal auth id authId: " . curl_error($ch), E_USER_WARNING);
        return false;
    }
    private function _patchOrder($orderId, $patchData)
    {

        if (version_compare(CC_VERSION, '6.2.8') >= 0) { // Request class only supports PATCH requests since 6.2.8
            $request = new Request($this->_api_endpoint, "/v2/checkout/orders/$orderId");
            $request->customHeaders('PayPal-Request-Id: '.$this->_request_id());
            $request->customHeaders('Content-Type: application/json');
            $request->customHeaders('Authorization: Bearer ' . $this->_access_token);
            $request->customOption(CURLOPT_CUSTOMREQUEST, 'PATCH');
            $request->setData(json_encode($patchData));
            $request->setMethod('POST');
            $request->skiplog(false);
            $request->cache(false);
            $request->setSSL();
            $result = $request->send();

            if ($this->_errorDetected($result)) {
                return 'error';
            }

            if (empty($result) && $request->server_response_code == 204) {
                return true;
            } else {
                trigger_error('Failed to update PayPal Commerce Order. Curl error: ' . curl_error($ch), E_USER_WARNING);
                return false;
            }
        } else {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://" . $this->_api_endpoint . "/v2/checkout/orders/$orderId");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($patchData));
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
            $headers = array();
            $headers[] = 'Content-Type: application/json';
            $headers[] = 'PayPal-Request-Id: '.$this->_request_id();
            $headers[] = 'Authorization: Bearer ' . $this->_access_token;
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            $result = curl_exec($ch);

            $this->_curl_response_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            if (empty($result) && curl_getinfo($ch, CURLINFO_HTTP_CODE) == 204) {
                return true;
            } else {
                trigger_error('Failed to update PayPal Commerce Order. Curl error: ' . curl_error($ch), E_USER_WARNING);
                return false;
            }
        }
    }
    public function verifySignature($headers, $body)
    {
        $data = <<<EOF
{
    "transmission_id": "{$headers['PAYPAL-TRANSMISSION-ID']}",
    "transmission_time": "{$headers['PAYPAL-TRANSMISSION-TIME']}",
    "cert_url": "{$headers['PAYPAL-CERT-URL']}",
    "auth_algo": "{$headers['PAYPAL-AUTH-ALGO']}",
    "transmission_sig": "{$headers['PAYPAL-TRANSMISSION-SIG']}",
    "webhook_id": "{$this->_config['webhook_id']}",
    "webhook_event": $body
}
EOF;
        $request = new Request($this->_api_endpoint, "/v1/notifications/verify-webhook-signature");
        $request->customHeaders('PayPal-Request-Id: '.$this->_request_id());
        $request->customHeaders('Content-Type: application/json');
        $request->customHeaders('Authorization: Bearer ' . $this->_access_token);
        $request->setData($data);
        $request->setMethod('POST');
        $request->skiplog(false);
        $request->cache(false);
        $request->setSSL();
        $result = $request->send();

        if ($result) {
            return json_decode($result, true);
        }
    }
    private function _errorDetected($result)
    {
        
        if($result->server_response_code > 0 && !in_array($result->server_response_code, array(200, 201, 202, 203, 204, 205, 206, 207, 208, 226))) {
            $GLOBALS['gui']->setError('An API error occured. Please try later.');
            return true;
        }

        $result = json_decode($result, true);
        if (isset($result['name']) && $result['name'] == 'UNPROCESSABLE_ENTITY') {
            foreach ($result['details'] as $value) {
                $GLOBALS['gui']->setError($value['description']);
            }
            return true;
        }
        return false;
    }
    private function _authSuccess($auth, $orderId)
    {
        if($this->_config['3ds']=='0') return true;
        
        $this->_transaction_notes[] = 'liabilityShifted: ' . $auth['liabilityShifted'];
        $this->_transaction_notes[] = 'authenticationStatus: ' . $auth['authenticationStatus'];
        $this->_transaction_notes[] = 'authenticationReason: ' . $auth['authenticationReason'];

        $rule_key = $auth['liabilityShifted']."_".$auth['authenticationStatus']."_".$auth['authenticationReason'];

        ## See: https://developer.paypal.com/docs/limited-release/custom-card-fields/integration-features/3d-secure/
        ## Test cases: https://cardinaldocs.atlassian.net/wiki/spaces/CCen/pages/400654355/3DS+1.0+Test+Cases
            
        switch($rule_key) {
            case 'true_YES_SUCCESSFUL':
                $this->_transaction_notes[] = 'Buyer successfully authenticated using 3D secure.';
                $result = true;
            break;
            case 'true_NO_ATTEMPTED':
                $this->_transaction_notes[] = 'Passive Authentication - cardholder not prompted for authentication credential.';
                $result = true;
            break;
            case 'undefined_undefined_undefined':
                $this->_transaction_notes[] = 'You have not required 3D Secure for the buyer or the card network did not require a 3D Secure.';
                $this->_auth_error_message = 'Please use a card that is enrolled for 3D secure.';
                $result = (bool)$this->_config['rule'][$rule_key];
            break;
            case 'false_ERROR_ERROR':
                $this->_transaction_notes[] = 'An error occurred with the 3DS authentication system.';
                $this->_auth_error_message = 'An error occurred with the 3DS authentication system.';
                $result = (bool)$this->_config['rule'][$rule_key];
            break;
            case 'false_NO_SKIPPED_BY_BUYER':
                $this->_transaction_notes[] = 'Buyer was presented the 3D Secure challenge but chose to skip the authentication.';
                $this->_auth_error_message = 'Sorry but 3D secure must not be skipped.';
                $result = (bool)$this->_config['rule'][$rule_key];
            break;
            case 'false_NO_FAILURE':
                $this->_transaction_notes[] = 'Buyer may have failed the challenge or the device was not verified.';
                $this->_auth_error_message = '3D secure challenge failed please try again.';
                $result = (bool)$this->_config['rule'][$rule_key];
            break;
            case 'false_NO_BYPASSED':
                $this->_transaction_notes[] = '3D Secure was skipped as authentication system did not require a challenge';
                $this->_auth_error_message = '3D secure was not required by the authentication system but required by merchant.';
                $result = (bool)$this->_config['rule'][$rule_key];
            break;
            case 'false_NO_ATTEMPTED':
                $this->_transaction_notes[] = 'Card is not enrolled in 3D Secure as card issuing bank is not participating in 3D Secure.';
                $this->_auth_error_message = 'Please use a card that is enrolled for 3D secure.';
                $result = (bool)$this->_config['rule'][$rule_key];
            break;
            case 'false_NO_UNAVAILABLE':
                $this->_transaction_notes[] = 'Issuing bank is not able to complete authentication.';
                $this->_auth_error_message = 'Issuing bank is not able to complete authentication.';
                $result = (bool)$this->_config['rule'][$rule_key];
            break;
            case 'false_NO_CARD_INELIGIBLE':
                $this->_transaction_notes[] = 'Card is not eligible for 3DS Secure authentication';
                $this->_auth_error_message = 'Please use a card that is enrolled for 3D secure.';
                $result = (bool)$this->_config['rule'][$rule_key];
            break;
        }
    

        if(!isset($result)) {
            $this->_auth_error_message = 'Unspecified error.';
            $this->_transaction_notes[] = 'The combination of liabilityShifted, authenticationStatus & authenticationReason was unexpected.';
            $result = false;
        }

        if($result) {
            $this->_auth_error_message = '';
        } else {
            $order = Order::getInstance();
            $cart_order_id = $GLOBALS['cart']->basket['cart_order_id'];
            $order_summary = $order->getSummary($cart_order_id);
            $transData['notes'] = implode("<br>", $this->_transaction_notes);
            $transData['gateway'] = 'PayPal Commerce';
            $transData['order_id'] = $cart_order_id;
            $transData['trans_id'] = $orderId;
            $transData['amount'] = $order_summary['total'];
            $transData['status'] = '3D SECURE FAILURE';
            $transData['customer_id'] = $order_summary['customer_id'];
            $order->logTransaction($transData);
        }
        return $result;
    }
    private function _breakdown()
    {

        $basket_total = round((float) $GLOBALS['cart']->basket['total'], 2);

        $item_total = round((float) $this->_item_total, 2);
        $total_tax = round((float) $GLOBALS['cart']->basket['total_tax'], 2);
        $shipping = round((float) $GLOBALS['cart']->basket['shipping']['value'], 2);
        $discount = round((float) $GLOBALS['cart']->basket['discount'], 2);

        $grand_total = $item_total + $total_tax + $shipping - $discount;

        if ((string)$grand_total !== (string)$basket_total) $this->_breakdown = false; // Remove breakdown for rounding problem

        return array(
            'item_total' => array('value' => (string) $item_total, 'currency_code' => $this->_currency),
            'shipping' => array('value' => (string) $shipping, 'currency_code' => $this->_currency),
            'tax_total' => array('value' => (string) $total_tax, 'currency_code' => $this->_currency),
            'discount' => array('value' => (string) $discount, 'currency_code' => $this->_currency),
        );
        
    }
    private function _buildOrder()
    {
        if ($GLOBALS['cart']->basket['digital_only'] == 1) {
            $shipping_preference = 'NO_SHIPPING';
            $shipping_address = false;
        } elseif ($GLOBALS['cart']->basket['delivery_address']['user_defined'] == 1) {
            $shipping_preference = 'SET_PROVIDED_ADDRESS';
            $shipping_address = $this->_shippingAddress();
        } else {
            $shipping_preference = 'GET_FROM_FILE';
            $shipping_address = false;
        }

        if ($GLOBALS['cart']->basket['billing_address']['user_defined'] == 1) {
            $billing_address = $this->_shippingAddress();
        } else {
            $billing_address = false;
        }

        $items = $this->_items();
        $breakdown = $this->_breakdown();
        if($this->_breakdown) {
            $purchase_units = array(
                'reference_id' => $this->_reference_id,
                'items' => $items,
                'amount' => array(
                    'currency_code' => $this->_currency,
                    'value' => (string) $GLOBALS['cart']->basket['total'],
                    'breakdown' => $breakdown,
                ),
            );
        } else {
            $purchase_units = array(
                'reference_id' => $this->_reference_id,
                'amount' => array(
                    'currency_code' => $this->_currency,
                    'value' => (string) $GLOBALS['cart']->basket['total']
                ),
            );
        }

        $this->_order_data = array(
            'intent' => strtoupper($this->_config['settlement']),
            'purchase_units' => array(
                0 => $purchase_units,
            ),
            'application_context' => array(
                'shipping_preference' => $shipping_preference,
                'brand_name' => $GLOBALS['config']->get('config', 'store_name'),
                'locale' => $GLOBALS['language']->current(),
                'user_action' => $_GET['_a'] == 'gateway' ? 'PAY_NOW' : 'CONTINUE',
            ),
        );
        if ($billing_address) {
            $this->_order_data['payer']['name'] = array(
                'given_name' => $GLOBALS['cart']->basket['billing_address']['first_name'],
                'surname' => $GLOBALS['cart']->basket['billing_address']['last_name']
            );
            $this->_order_data['payer']['address'] = $this->_billingAddress();
        }

        if ($shipping_address) {
            $this->_order_data['purchase_units'][0]['shipping'] = $shipping_address;
        }
        if (!$GLOBALS['session']->has('invoice_id_set', 'paypal_commerce') && isset($GLOBALS['cart']->basket['cart_order_id']) && !empty($GLOBALS['cart']->basket['cart_order_id'])) {
            $GLOBALS['session']->set('invoice_id_set', true, 'paypal_commerce');
            $this->_order_data['purchase_units'][0]['invoice_id'] = $GLOBALS['cart']->basket['cart_order_id'];
            $this->_order_data['purchase_units'][0]['description'] = 'Payment for order ' . $GLOBALS['cart']->basket['cart_order_id'];
        }
    }
    private function _items()
    {
        $items = array();
        foreach ($GLOBALS['cart']->basket['contents'] as $hash => $value) {
            /*
            $description = $GLOBALS['db']->select('CubeCart_inventory', array('description', 'description_short'), array('product_id' => $value['id']));
            $description_key = empty($description[0]['description_short']) ? 'description' : 'description_short';
            $description = substr(strip_tags($description[0][$description_key]), 0, 127);
            if (empty($description)) {
                $description = null;
            }
            */
            $items[] = array(
                'name' => substr(preg_replace("/[^ \w]+/",'',$value['name']),0,127),
                'unit_amount' => array('value' => (string) round($value['total_price_each'],2), 'currency_code' => $this->_currency),
                'quantity' => (int) $value['quantity'],
                'sku' => $value['product_code'],
                'category' => $value['digital'] > 0 ? 'DIGITAL_GOODS' : 'PHYSICAL_GOODS',
                //'description' => $description,
            );
            $this->_item_total += round(((float) $value['total_price_each'] * (int) $value['quantity']), 2);
        }
        return $items;
    }
    private function _shippingAddress($part = false)
    {
        $state_field = in_array($GLOBALS['cart']->basket['delivery_address']['country_iso'], array('US')) ? 'state_abbrev' : 'state';

        $name = array(
            'full_name' => $GLOBALS['cart']->basket['delivery_address']['first_name'] . ' ' . $GLOBALS['cart']->basket['delivery_address']['last_name'],
        );

        $address = array(
            'address_line_1' => $GLOBALS['cart']->basket['delivery_address']['line1'],
            'address_line_2' => $GLOBALS['cart']->basket['delivery_address']['line2'],
            'admin_area_1' => $GLOBALS['cart']->basket['delivery_address'][$state_field],
            'admin_area_2' => $GLOBALS['cart']->basket['delivery_address']['town'],
            'postal_code' => $GLOBALS['cart']->basket['delivery_address']['postcode'],
            'country_code' => $GLOBALS['cart']->basket['delivery_address']['country_iso'],
        );
        if(empty($address['address_line_2'])) {
            unset($address['address_line_2']);
        }

        switch ($part) {
            case 'name':
                return $name;
                break;
            case 'address':
                return $address;
                break;
            default:
                return array(
                    'name' => $name,
                    'email_address' => $GLOBALS['user']->get('email'),
                    'phone' => $GLOBALS['user']->get('phone'),
                    'address' => $address,
                );
        }

    }
    private function _billingAddress()
    {
        $state_field = in_array($GLOBALS['cart']->basket['billing_address']['country_iso'], array('US')) ? 'state_abbrev' : 'state';
        return array(
            'address_line_1' => $GLOBALS['cart']->basket['billing_address']['line1'],
            'address_line_2' => $GLOBALS['cart']->basket['billing_address']['line2'],
            'admin_area_1' => $GLOBALS['cart']->basket['billing_address'][$state_field],
            'admin_area_2' => $GLOBALS['cart']->basket['billing_address']['town'],
            'postal_code' => $GLOBALS['cart']->basket['billing_address']['postcode'],
            'country_code' => $GLOBALS['cart']->basket['billing_address']['country_iso'],
        );
    }
}

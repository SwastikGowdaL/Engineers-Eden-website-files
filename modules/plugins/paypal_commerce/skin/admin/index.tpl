{**
 * CubeCart v6
 * ========================================
 * CubeCart is a registered trade mark of CubeCart Limited
 * Copyright CubeCart Limited 2014. All rights reserved.
 * UK Private Limited Company No. 5323904
 * ========================================
 * Web:   http://www.cubecart.com
 * Email:  sales@cubecart.com
 * License:  GPL-3.0 http://opensource.org/licenses/GPL-3.0
 *}

<form action="{$VAL_SELF}" method="post" enctype="multipart/form-data">
   <div id="paypal_commerce" class="tab_content">
      <h3>{$TITLE}</h3>
      <p>{$LANG.paypal_commerce.strap}</p>
      {if $connected} 
      <fieldset>
         <legend>{$LANG.paypal_commerce.onboarding}</legend>
         <div><label for="primary_email">{$LANG.paypal_commerce.primary_email}</label><span><strong>{$MODULE.primary_email}</strong></span></div>
         <div><label for="client_id">{$LANG.paypal_commerce.client_id}</label><span>{$MODULE.client_id}</span></div>
         <div><label for="client_secret">{$LANG.paypal_commerce.secret}</label><span style="color: transparent;text-shadow: 0 0 4px rgba(0,0,0,0.5);" class="reveal_secret">{$MODULE.client_secret}</span> <i class="fa fa-eye" aria-hidden="true" style="cursor:pointer" title="{$LANG.paypal_commerce.click_to_reveal}" onclick="$('.reveal_secret').removeAttr('style');"></i></div>
      </fieldset>
      <fieldset>
         <legend>{$LANG.paypal_commerce.payment_settings}</legend>
         <div><label for="paypal_status">{$LANG.paypal_commerce.enable_ppc}</label><span><input type="hidden" name="module[status]" id="paypal_status" class="toggle" value="{$MODULE.status}" /></span></div>
         <div><label for="settlement">{$LANG.paypal_commerce.settlement_mode}</label><span>
            <select name="module[settlement]">
               <option value="capture"{if $MODULE.settlement=='capture'} selected="selected"{/if}>{$LANG.paypal_commerce.capture}</option>
               <option value="authorize"{if $MODULE.settlement=='authorize'} selected="selected"{/if}>{$LANG.paypal_commerce.authorize}</option>
            </select>
            </span>
         </div>
         <div>
         <label for="paypal_disable_cards">{$LANG.paypal_commerce.disable_cards}</label><span>
            <select name="module[disable_cards]" id="paypal_disable_cards">
               <option value="0"{if $MODULE.disable_cards=='0' || !isset($MODULE.disable_cards)} selected="selected"{/if}>{$LANG.common.enable} - {$LANG.common.recommended}</option>
               <option value="1"{if $MODULE.disable_cards=='1'} selected="selected"{/if}>{$LANG.common.disable}</option>
            </select>
         </span></div>
         <div>
            <label for="3ds">{$LANG.paypal_commerce.3ds}</label>
            <span>
               <select name="module[3ds]" id="3ds" onchange="toggleLiabilityMatrix(this)">
                  <option value="0"{if $MODULE.3ds=='0'} selected="selected"{/if}>{$LANG.common.disable}</option>
                  <option value="1"{if $MODULE.3ds=='1'} selected="selected"{/if}>{$LANG.common.enable} - {$LANG.common.recommended}</option>
               </select>
            </span>
            <div>{$LANG.paypal_commerce.3ds_note}</div>
         </div>
      </fieldset>
      <script data-cfasync="false">
      function toggleLiabilityMatrix(e) {
         if(e.value=='1') {
            $('#liabilityMatrix').show();
         } else {
            $('#liabilityMatrix').hide();
         }   
      }
      </script>
      <div {if $MODULE.3ds=='0'} style="display:none"{/if} id="liabilityMatrix">
      <p>{$LANG.paypal_commerce.3ds_note_2}</p>
      <table>
            <thead>
               <tr>
                  <th>{$LANG.paypal_commerce.3ds_scenario}</th>
                  <th>Transaction</th>
               </tr>
            </thead>
            <tbody>
               {foreach $matrix_3dx as $action}
               <tr>
                  <td>{$action.desc}</td>
                  <td align="center">
                     <select name="module[rule][{$action.name}]" id="{$action.name}">
                        <option value="0"{if $action.value=='0'} selected="selected"{/if}>Decline {if $action.default=='0'} - {$LANG.common.recommended}{/if}</option>
                        <option value="1"{if $action.value=='1'} selected="selected"{/if}>Accept {if $action.default=='1'} - {$LANG.common.recommended}{/if}</option>
                     </select>
                  </td>
               </tr>
               {/foreach}
            </tbody>
         </table>
      </div>
      {if $sandbox}
      <fieldset>
         <legend>Test Settings (This only shows in sandbox mode)</legend>
         <div><label for="debug">Debug</label>
            <span>
               <select name="module[debug]">
                  <option value="0"{if $MODULE.debug=='0'} selected="selected"{/if}>Off</option>
                  <option value="1"{if $MODULE.debug=='1'} selected="selected"{/if}>On</option>
               </select>
            </span>
         </div>
         <div><label for="buyer_country">Buyer Country</label>
            <span>
               <input type="text" name="module[buyer_country]" maxlength="2" value="{$MODULE.buyer_country}"> (Leave blank for automatic) <a href="https://developer.paypal.com/docs/checkout/reference/customize-sdk/#buyer-country" target="_blank">Docs</a>
            </span>
         </div>
         <div>
            <label for="locale">Locale</label>
            <span>
               <input type="text" name="module[locale]" maxlength="5" value="{$MODULE.locale}">  (Leave blank for automatic) <a href="https://developer.paypal.com/docs/checkout/reference/customize-sdk/#locale" target="_blank">Docs</a>
            </span>
         </div>
      </fieldset>
      {/if}
      <p class="paypal_advanced" style="display:none"><a href="#" onclick="$('.paypal_advanced').hide();$('.paypal_simple').show();">{$LANG.paypal_commerce.hide_advanced} [-]</a></p>
      <p class="paypal_simple"><a href="#" onclick="$('.paypal_advanced').show();$('.paypal_simple').hide();">{$LANG.paypal_commerce.show_advanced} [+]</a></p>
      <fieldset class="paypal_advanced" style="display: none">
         <legend>{$LANG.paypal_commerce.order_status_behaviour}</legend>
         <div><label for="status_change_time">{$LANG.paypal_commerce.order_status_change_on} </label>
            <span>
               <select name="module[status_change_time]" id="status_change_time">
                  <option value="capture"{if $MODULE.status_change_time=='capture'} selected="selected"{/if}>{$LANG.paypal_commerce.payment_capture}</option>
                  <option value="authorize"{if $MODULE.status_change_time=='authorize'} selected="selected"{/if}>{$LANG.paypal_commerce.payment_authorization}</option>
               </select>
            </span>
         </div>
      </fieldset>
      <fieldset class="paypal_advanced" style="display: none">
         <legend>{$LANG.paypal_commerce.button_styling}</legend>
         <div><label for="smart_layout">{$LANG.paypal_commerce.button_layout}</label>
            <span>
               <select name="module[smart_layout]" id="smart_layout">
                  <option value="horizontal"{if $MODULE.smart_layout=='horizontal'} selected="selected"{/if}>{$LANG.paypal_commerce.horizontal}  - {$LANG.common.default}</option>
                  <option value="vertical"{if $MODULE.smart_layout=='vertical'} selected="selected"{/if}>{$LANG.paypal_commerce.vertical}</option>
               </select>
            </span>
         </div>
         <div>
            <label for="smart_color">{$LANG.paypal_commerce.button_color}</label>
            <span>
               <select name="module[smart_color]">
                  <option value="gold"{if $MODULE.smart_color=='gold'} selected="selected"{/if}>{$LANG.paypal_commerce.gold}  - {$LANG.common.default}</option>
                  <option value="blue"{if $MODULE.smart_color=='blue'} selected="selected"{/if}>{$LANG.paypal_commerce.blue}</option>
                  <option value="silver"{if $MODULE.smart_color=='silver'} selected="selected"{/if}>{$LANG.paypal_commerce.silver}</option>
                  <option value="white"{if $MODULE.smart_color=='white'} selected="selected"{/if}>{$LANG.paypal_commerce.white}</option>
                  <option value="black"{if $MODULE.smart_color=='black'} selected="selected"{/if}>{$LANG.paypal_commerce.black}</option>
               </select>
            </span>
         </div>
         <div>
            <label for="smart_shape">{$LANG.paypal_commerce.button_shape}</label>
            <span>
               <select name="module[smart_shape]">
                  <option value="rect"{if $MODULE.smart_shape=='rect'} selected="selected"{/if}>{$LANG.paypal_commerce.rectangle}  - {$LANG.common.default}</option>
                  <option value="pill"{if $MODULE.smart_shape=='pill'} selected="selected"{/if}>{$LANG.paypal_commerce.pill}</option>
               </select>
            </span>
         </div>
         <div><label for="smart_tagline">{$LANG.paypal_commerce.button_tagline}</label>
            <span>
               <select name="module[smart_tagline]">
                  <option value="true"{if $MODULE.smart_tagline=='true'} selected="selected"{/if}>{$LANG.common.show}  - {$LANG.common.default}</option>
                  <option value="false"{if $MODULE.smart_tagline=='false'} selected="selected"{/if}>{$LANG.common.hide}</option>
               </select>
            </span>
         </div>
         <div><label for="smart_fundingicons">{$LANG.paypal_commerce.button_funding}</label>
            <span>
               <select name="module[smart_fundingicons]">
                  <option value="false"{if $MODULE.smart_fundingicons=='false'} selected="selected"{/if}>{$LANG.common.hide}  - {$LANG.common.default}</option>
                  <option value="true"{if $MODULE.smart_fundingicons=='true'} selected="selected"{/if}>{$LANG.common.show}</option>
               </select>
            </span>
         </div>
         <div>
            <label for="smart_size">{$LANG.paypal_commerce.button_size}</label>
            <span>
               <select name="module[smart_size]" id="smart_size">
                  <option value="small"{if $MODULE.smart_size=='small'} selected="selected"{/if}>{$LANG.paypal_commerce.small}  - {$LANG.common.default}</option>
                  <option value="medium"{if $MODULE.smart_size=='medium'} selected="selected"{/if}>{$LANG.paypal_commerce.medium}</option>
                  <option value="large"{if $MODULE.smart_size=='large'} selected="selected"{/if}>{$LANG.paypal_commerce.large}</option>
                  <option value="responsive"{if $MODULE.smart_size=='responsive'} selected="selected"{/if}>{$LANG.paypal_commerce.responsive}</option>
               </select>
            </span>
         </div>
      </fieldset>

      <fieldset class="paypal_advanced" style="display: none">
         <legend>{$LANG.paypal_commerce.webhooks}</legend>
         <div><label for="webhooks_url">{$LANG.paypal_commerce.url}</label><span>{$webhooks.url}</span></div>
         <div><label for="webhook_id">{$LANG.paypal_commerce.webhook_id}</label><span>{$webhooks.id}</span></div>
         <div style="text-align:center">
            [{foreach $webhooks.event_types as $event}
               {$event.name}{if !$event@last} | {/if} 
            {/foreach}]
            </span>
         </div>
      </fieldset>
      {else}
         <h2>{$LANG.paypal_commerce.title}</h2>
         <p>{$LANG.paypal_commerce.start_today}</p>
         <style>
         a[data-paypal-button="PPLtBlue"]::before {
            content: "";
            background: url(https://www.paypalobjects.com/webstatic/mktg/Logo/pp-logo-200px.png) 11px no-repeat #fff;
            background-size: 80px;
            display: table-cell;
            width: 100px;
            height: 42px;
            float: left;
            margin-right: 16px;
            border-bottom-left-radius: 5px;
            border-top-left-radius: 5px;
            margin-top: -12px;
         }
         </style>
         <a target="_blank" data-paypal-onboard-complete="onboardedCallback" href="{$ppcp_link}" data-paypal-button="PPLtBlue">{$LANG.paypal_commerce.sign_up_or}</a>
         <p>{$LANG.paypal_commerce.express_signup|replace:'%SIGN_UP_LINK%':$ec_link}</p>
         
      {/if}
   </div>
   {$MODULE_ZONES}
   {if $connected}
   <div class="form_control">
      {if $show_disconnect}<a href="?_g=plugins&type=plugins&module=paypal_commerce&disconnect" class="button delete" title="{$LANG.paypal_commerce.disconnect_check}" style="float: right">{$LANG.paypal_commerce.disconnect}</a>{/if}<input type="submit" value="{$LANG.common.save}" />
   </div>
   <input type="hidden" name="module[scope]" value="both" />
   <input type="hidden" name="module[position]" value="1" />
   <input type="hidden" name="module[merchantId]" value="{$MODULE.merchantId}">
   <input type="hidden" name="module[merchantIdInPayPal]" value="{$MODULE.merchantIdInPayPal}">
   <input type="hidden" name="module[client_id]" value="{$MODULE.client_id}">
   <input type="hidden" name="module[client_secret]" value="{$MODULE.client_secret}">
   <input type="hidden" name="module[webhook_id]" value="{$MODULE.webhook_id}">
   <input type="hidden" name="module[smart_label]" value="checkout">
   {else}
   {literal}
   <script data-cfasync="false">
      function onboardedCallback(authCode, sharedId) {
         var data = {authCode: authCode, sharedId: sharedId, seller_nonce: {/literal}'{$seller_nonce}'{literal}};
         $.ajax({
               url: '?_g=plugins&type=plugins&module=paypal_commerce',
               type: 'post',
               data: JSON.stringify(data),
               success: function (data) {
                  if(data!='success') {
                     alert(data);
                  }
               },
         });
      }
   </script>
   <script data-cfasync="false" id="paypal-js" src="https://www.paypal.com/webapps/merchantboarding/js/lib/lightbox/partner.js"></script>
   {/literal}
   {/if}
</form>

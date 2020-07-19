{*
  Rename this file to `card_form_custom.tpl`
  This will prevent customisations getting
  overwritten on upgrade!!
*}
<style>
  .form-container {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100%;
    flex-direction: column;
    border: 1em solid #fff;
    box-sizing: border-box;
    position: relative;
  }
  @media (max-width: 476px) {
    .form-container {
      border: none;
    }
  }
  .cardinfo-wrapper {
    display: flex;
    justify-content: space-around;
  }
  #paypal-card-container {
    border-radius: 6px;
    background-color: #eee;
    padding: 2em 1.5em 1em;
    width: 20em;
    margin-bottom: 1em;
    transition: all 600ms cubic-bezier(0.2, 1.3, 0.7, 1);
    -webkit-animation: cardIntro 500ms cubic-bezier(0.2, 1.3, 0.7, 1);
    animation: cardIntro 500ms cubic-bezier(0.2, 1.3, 0.7, 1);
    z-index: 1;
  }
  #paypal-card-container:hover {
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.06);
  }
  @media (max-width: 476px) {
    #paypal-card-container {
      box-sizing: border-box;
      padding: 7em 2em 2em;
      width: 100%;
    }
  }
  .cardinfo-label {
    display: block;
    font-size: 11px;
    margin-bottom: 0.5em;
    text-transform: uppercase;
    width: 100%;
    text-align: left;
  }
  .cardinfo-exp-date {
    margin-right: 1em;
    width: 100%;
  }
  .cardinfo-cvv {
    width: 100%;
  }
  #button-pay {
    visibility: hidden;
    cursor: pointer;
    width: 21.5em;
    font-size: 15px;
    border: 0;
    padding: 1.2em 2em;
    color: #fff;
    background: #43AC6A;
    border-radius: 6px;
    z-index: 0;
    height: inherit;
    line-height: inherit;
  }
  #button-pay:hover {
    background: #3a945b;
  }
  #button-pay:active {
    background: #43AC6A
  }
  #button-pay.show-button {
    visibility: visible;
  }
  .cardinfo-card-number {
    position: relative;
  }
  #card-image {
    position: absolute;
    top: 2em;
    right: 1em;
    width: 44px;
    height: 28px;
    background-image: url(https://s3-us-west-2.amazonaws.com/s.cdpn.io/346994/card_sprite.png);
    background-size: 86px 458px;
    border-radius: 4px;
    background-position: -100px 0;
    background-repeat: no-repeat;
    margin-bottom: 1em;
  }
  #card-image.visa {
    background-position: 0 -398px;
  }
  #card-image.master-card {
    background-position: 0 -281px;
  }
  #card-image.american-express {
    background-position: 0 -370px;
  }
  #card-image.discover {
    background-position: 0 -163px;
  }
  #card-image.maestro {
    background-position: 0 -251px;
  }
  #card-image.jcb {
    background-position: 0 -221px;
  }
  #card-image.diners-club {
    background-position: 0 -133px;
  }
  /*-------------------- Inputs --------------------*/
  .input-wrapper {
    border-radius: 2px;
    background: rgba(255, 255, 255, 0.86);
    height: 2.75em;
    border: 1px solid #eee;
    box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.06);
    padding: 5px 10px;
    margin-bottom: 1em;
  }
  .cardinfo-card-number,
  .cardinfo-exp-date,
  .cardinfo-cvv {
    transition: -webkit-transform 0.3s;
    transition: transform 0.3s;
    transition: transform 0.3s, -webkit-transform 0.3s;
  }
  .braintree-hosted-fields-focused {
    border-color: #5db6e8;
  }
  .braintree-hosted-fields-invalid {
    border-color: #e53a40;
    -webkit-animation: shake 500ms cubic-bezier(0.2, 1.3, 0.7, 1) both;
    animation: shake 500ms cubic-bezier(0.2, 1.3, 0.7, 1) both;
    -webkit-transform: translate3d(0, 0, 0);
    transform: translate3d(0, 0, 0);
    -webkit-backface-visibility: hidden;
    backface-visibility: hidden;
    -webkit-perspective: 1000px;
    perspective: 1000px;
  }
  /*-------------------- Animations --------------------*/
  @-webkit-keyframes cardIntro {
    0% {
      -webkit-transform: scale(0.8) translate(0, 0);
      transform: scale(0.8) translate(0, 0);
      opacity: 0;
    }
    100% {
      -webkit-transform: scale(1) translate(0, 0);
      transform: scale(1) translate(0, 0);
      opacity: 1;
    }
  }
  @keyframes cardIntro {
    0% {
      -webkit-transform: scale(0.8) translate(0, 0);
      transform: scale(0.8) translate(0, 0);
      opacity: 0;
    }
    100% {
      -webkit-transform: scale(1) translate(0, 0);
      transform: scale(1) translate(0, 0);
      opacity: 1;
    }
  }
  @-webkit-keyframes shake {
    10%,
    90% {
      -webkit-transform: translate3d(-1px, 0, 0);
      transform: translate3d(-1px, 0, 0);
    }
    20%,
    80% {
      -webkit-transform: translate3d(1px, 0, 0);
      transform: translate3d(1px, 0, 0);
    }
    30%,
    50%,
    70% {
      -webkit-transform: translate3d(-3px, 0, 0);
      transform: translate3d(-3px, 0, 0);
    }
    40%,
    60% {
      -webkit-transform: translate3d(3px, 0, 0);
      transform: translate3d(3px, 0, 0);
    }
  }
  @keyframes shake {
    10%,
    90% {
      -webkit-transform: translate3d(-1px, 0, 0);
      transform: translate3d(-1px, 0, 0);
    }
    20%,
    80% {
      -webkit-transform: translate3d(1px, 0, 0);
      transform: translate3d(1px, 0, 0);
    }
    30%,
    50%,
    70% {
      -webkit-transform: translate3d(-3px, 0, 0);
      transform: translate3d(-3px, 0, 0);
    }
    40%,
    60% {
      -webkit-transform: translate3d(3px, 0, 0);
      transform: translate3d(3px, 0, 0);
    }
  }
  #paypal-button-container {
    text-align: center;
  }
  /*--------------------  START LOADING ANIMATION --------------------*/
  #paypal_commerce_loading {
    text-align: center;
    margin-bottom: 6em;
  }
  @keyframes blink {
    0% {
      opacity: 0.2;
    }
    20% {
      opacity: 1;
    }
    100% {
      opacity: 0.2;
    }
  }
  .loading {
    display: none;
    font-weight: bold;
    font-size: 120px;
  }
  .loading span {
    animation-name: blink;
    animation-duration: 1.4s;
    animation-iteration-count: infinite;
    animation-fill-mode: both;
  }
  .loading span:nth-child(2) {
    animation-delay: 0.2s;
  }
  .loading span:nth-child(3) {
    animation-delay: 0.4s;
  }
  /*--------------------  END LOADING ANIMATION --------------------*/
  #error-message {
    color: #f44336;
    text-align: center;
  }
  #paypal-button-container,
  #paypal-button-container div {
    text-align: center;
  }
  #paypal-button-container div {
    width: 320px;
  }
</style>
<h1 id="paypal_commerce_loading" class="loading">
  <span>.</span><span>.</span><span>.</span>
</h1>
<div id="paypal-button-container"></div>
<div id="paypal-card-wrapper" style="display:none">
  <div id="error-message" style="display:none;"></div>
  <div class="form-container">
    <header>
      <h1>{$LANG.paypal_commerce.pay_by_card}</h1>
    </header>
    <div id="paypal-card-container">
      <div class="cardinfo-card-number">
        <label class="cardinfo-label" for="card-number">{$LANG.paypal_commerce.card_number}</label>
        <div class="input-wrapper" id="card-number"></div>
        <div id="card-image"></div>
      </div>
      <div class="cardinfo-wrapper">
        <div class="cardinfo-exp-date">
          <label class="cardinfo-label" for="expiration-date"
            >{$LANG.paypal_commerce.expiry_date}</label>
          <div class="input-wrapper" id="expiration-date"></div>
        </div>
        <div class="cardinfo-cvv">
          <label class="cardinfo-label" for="cvv">{$LANG.paypal_commerce.cvv}</label>
          <div class="input-wrapper" id="cvv"></div>
        </div>
      </div>
    </div>
    <button id="button-pay" class="button">{$LANG.paypal_commerce.make_payment}</button>
  </div>
  <div id="payments-sdk__contingency-lightbox"></div>
</div>

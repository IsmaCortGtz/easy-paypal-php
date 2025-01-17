<?php

namespace IsmaCortGtz\EasyPayPalPhp\Order;

use IsmaCortGtz\EasyPayPalPhp\Request\VanillaRequest;

class PaypalOrder extends PaypalOrderCapture {

  private $orderResponse;

  public function __construct($ctx) {
    parent::__construct($ctx);
  }

  /**
   * Crear un intento de pago usando la API de PayPal
   * @param string|integer|float $amountTotal Monto total de la compra
   * @return bool `true` si se creo el intento de pago, `false` en caso contrario
   */
  public function create($amountTotal) {
    $paypalOrderBody = [
      "intent" => "CAPTURE", 
      "purchase_units" => [[
          "amount" => [
              "currency_code" => $this->ctx->getCurrency(), 
              "value" =>  strval($amountTotal)
          ] 
      ]], 
      "payment_source" => [
          "paypal" => [
              "experience_context" => [
                  "shipping_preference" => "NO_SHIPPING", 
                  "brand_name" => $this->ctx->getBrandName(), 
                  "locale" => $this->ctx->getLocale(), 
                  "user_action" => "PAY_NOW", 
                  "return_url" => $this->ctx->getReturnUrl(), 
                  "cancel_url" => $this->ctx->getCancelUrl() 
              ] 
          ] 
      ] 
    ];

    $resultado = VanillaRequest::auth($this->ctx->getClientId(), $this->ctx->getSecretKey())
                              ->post($this->ctx->getApiUrl()."/v2/checkout/orders", $paypalOrderBody);

    if ($resultado->failed()) {
      $this->orderResponse = null;
      return false;
    }
    
    $this->orderResponse = $resultado->object();
    return true;
      
  }

  
  /**
   * Obtener la respuesta de la petición como un objeto
   * @return mixed
   */
  public function response() {
    return $this->orderResponse;
  }


  /**
   * Devuelve el ID del intento de pago de PayPal
   * @return string
   */
  public function id() {
    return $this->orderResponse->id;
  }


  /**
   * Devuelve el URL de redirección para el pago
   * @return string
   */
  public function link() {
    return $this->orderResponse->links[1]->href;
  }
}

?>
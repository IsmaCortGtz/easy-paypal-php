<?php

namespace IsmaCortGtz\EasyPayPalPhp\Order;

use IsmaCortGtz\EasyPayPalPhp\Request\VanillaRequest;

class PaypalOrderCapture {

  protected $ctx;
  private $orderResponse;

  public function __construct($ctx) {
    $this->ctx = $ctx;
  }

  public function capture($idToken) {

    $resultado = VanillaRequest::auth($this->ctx->getClientId(), $this->ctx->getSecretKey())
                              ->post("{$this->ctx->getApiUrl()}/v2/checkout/orders/{$idToken}/capture", null);
    
    $this->orderResponse = $resultado->object();
    return !$resultado->failed();
  }

  public function response() {
    return $this->orderResponse;
  }

  public function isCompleted() {
    if ($this->orderResponse == null) return false;

    if (isset($this->orderResponse->status)) 
      return $this->orderResponse->status === "COMPLETED";
      
    if (isset($this->orderResponse->details[0]->issue)) 
      return $this->orderResponse->details[0]->issue === "ORDER_ALREADY_CAPTURED";

    return false;
  }
}

?>
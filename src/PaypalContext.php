<?php

namespace IsmaCortGtz\EasyPayPalPhp;

class PaypalContext {
  private $apiURL = [
    "sandbox" => "https://api-m.sandbox.paypal.com",
    "live" => "https://api-m.paypal.com",
  ];
  private $config;
  private $platform;

  /**
   * Crearel contexto de configuración para usar en las operaciones de PayPal
   * @param mixed $config Un arreglo con la configuración de la API
   * @param string $platform="default" La plataforma a usar, por defecto "default"
   */
  public function __construct($config, $platform = "default") {
    $this->config = $config;
    $this->platform = $platform;
  }

  private function getConfig($key) {
    if (isset($this->config[$this->platform][$key])) return $this->config[$this->platform][$key];
    else if (isset($this->config["default"][$key])) return $this->config["default"][$key];
    return null;
  }

  private function getModesConfig($key) {
    $mode = $this->getConfig("mode");
    return $this->getConfig($mode)[$key];
  }

  public function getCurrency() {
    return $this->getConfig("currency");
  }

  public function getBrandName() {
    return $this->getConfig("brand_name");
  }

  public function getLocale() {
    return $this->getConfig("locale");
  }

  public function getReturnUrl() {
    return $this->getConfig("return_url");
  }

  public function getCancelUrl($mobile = false) {
    return $this->getConfig("cancel_url");
  }

  public function getClientId() {
    return $this->getModesConfig("client_id");
  }

  public function getSecretKey() {
    return $this->getModesConfig("secret_key");
  }

  public function getApiUrl() {
    return $this->apiURL[$this->getConfig("mode")];
  }
}

?>
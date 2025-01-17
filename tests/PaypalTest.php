<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use IsmaCortGtz\EasyPayPalPhp\Order\PaypalOrder;
use IsmaCortGtz\EasyPayPalPhp\PaypalContext;

final class PaypalTest extends TestCase {

    private $configCustomArray = [
        "default" => [
            "mode" => "sandbox", // 'sandbox' o 'live'
            "currency" => "MXN",
            "locale" => "es-MX",
            "brand_name" => "Empresa Generica",
            "return_url" => "http://localhost:8001/paypal/aprobado",
            "cancel_url" => "http://localhost:8001/paypal/cancelado",
    
            "sandbox" => [
                "client_id" => "PAYPAL_SANDBOX_CLIENT_ID",
                "secret_key" => "PAYPAL_SANDBOX_SECRET_KEY",
                "api_url" => "https://api-m.sandbox.paypal.com"
            ],
    
            "live" => [
                "client_id" => "PAYPAL_LIVE_CLIENT_ID",
                "secret_key" => "PAYPAL_LIVE_SECRET_KEY",
                "api_url" => "https://api-m.paypal.com"
            ],
        ],
        "android" => [
            "return_url" => "http://localhost:8001/android/paypal/aprobado",
            "cancel_url" => "http://localhost:8001/android/paypal/cancelado",
        ]
    ];

    public function createContext($platform = "default") {
        return new PaypalContext($this->configCustomArray, $platform);
    }

    public function testPaypalDefault(){
        $ctx = $this->createContext();

        $order = new PaypalOrder($ctx);
        $success = $order->create(12);

        $this->assertTrue($success);
        $this->assertIsString($order->id());
        $this->assertIsString($order->link());

        echo "Pay with paypal - default: ". $order->link() . PHP_EOL;
        echo "Order id: ". $order->id() . PHP_EOL;
        echo "Press enter when you finish the payment... ";
        ob_flush();
        flush();
        fgets(STDIN);

        $orderCapture = new PaypalOrder($ctx);
        $successCapture = $orderCapture->capture($order->id());

        $this->assertTrue($successCapture);
        $this->assertTrue($orderCapture->isCompleted());
    }



    public function testPaypalAndroid(){
        $ctx = $this->createContext("android");

        $order = new PaypalOrder($ctx);
        $success = $order->create(20);

        $this->assertTrue($success);
        $this->assertIsString($order->id());
        $this->assertIsString($order->link());

        echo "Pay with paypal - android: ". $order->link() . PHP_EOL;
        echo "Order id: ". $order->id() . PHP_EOL;
        echo "Press enter when you finish the payment... ";
        ob_flush();
        flush();
        fgets(STDIN);

        $orderCapture = new PaypalOrder($ctx);
        $successCapture = $orderCapture->capture($order->id());

        $this->assertTrue($successCapture);
        $this->assertTrue($orderCapture->isCompleted());
    }
}



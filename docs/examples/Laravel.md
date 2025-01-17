# Ejemplo de uso: `Laravel`

Este ejemplo fue probado usando Laravel 10, pero debería de funcionar sin problemas para otras versiones de Laravel haciendo los cambios necesarios.

Este ejemplo contempla una serie de 5 pasos que representan las acciones necesarias para implementar esta librería.

1. Crear las variables de entorno.
2. Crear el archivo de configuración.
3. Crear el contexto en el controlador.
4. Crear un intento de pago.
5. Capturar el pago.

## 1. Crear las variables de entorno

> [!IMPORTANT]  
> Para llevar a cabo este paso es importante primero generar las credenciales necesarias en la pagina para desarrolladores de PayPal. No es necesario generar claves live si solo trabajaras en modo sandbox y viceversa.

Para realizar este paso, dirígete a tu archivo `.env` que se encuentra en la raíz del proyecto y agrega las siguientes variables.

```bash
# Solamente es obligatorio si usaras el modo sandbox
PAYPAL_SANDBOX_CLIENT_ID=XXXXXX
PAYPAL_SANDBOX_SECRET_KEY=XXXXXX

# Solamente es obligatorio si usaras el modo live
PAYPAL_LIVE_CLIENT_ID=XXXXXX
PAYPAL_LIVE_SECRET_KEY=XXXXXX
```

Una vez creadas estas variables (remplazando las `XXXXXX` por tus credenciales reales), has terminado con este paso. Puedes usar otro nombre si así lo deseas, pero deberás usar el mismo nombre que establezcas aquí en el archivo de configuración.

## 2. Crear el archivo de configuración

Para este paso deberás crear un archivo llamado `paypal.php` dentro de la carpeta `config` que está en la raíz del proyecto, de esta forma `config/paypal.php`. Dentro del archivo deberás agregar tu configuración para el objeto `PaypalContext`. Puedes seguir [esta guía](../Class/PaypalContext.md).

```php
<?php

// IsmaCortGtz\EasyPaypalPHP configuration file

return [
    "default" => [
        "mode" => "sandbox", // 'sandbox' o 'live'
        "currency" => "MXN",
        "locale" => "es-MX",
        "brand_name" => "Empresa Generica",
        "return_url" => "http://localhost:8000/paypal/resultado",
        "cancel_url" => "http://localhost:8000/paypal/resultado",

        "sandbox" => [
            "client_id" => env("PAYPAL_SANDBOX_CLIENT_ID", ""),
            "secret_key" => env("PAYPAL_SANDBOX_SECRET_KEY", "")
        ],

        "live" => [
            "client_id" => env("PAYPAL_LIVE_CLIENT_ID", ""),
            "secret_key" => env("PAYPAL_LIVE_SECRET_KEY", "")
        ],
    ],
];
```

> [!TIP]
> Usando la funcion `env` puedes usar las varibles de entorno que quieras.

## 3. Crear el contexto en el controlador.

Dirígete al controlador donde procesarás los pagos con PayPal y dentro del constructor crea una instancia de `PaypalContext` usando el archivo de configuración que acabas de crear.

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Config;
use Illuminate\Http\Request;

use IsmaCortGtz\EasyPayPalPhp\Order\PaypalOrder;
use IsmaCortGtz\EasyPayPalPhp\PaypalContext;

class PaypalController extends Controller {
    private $paypalContext;

    public function __construct() {
        $config = Config::get("paypal");
        $this->paypalContext = new PaypalContext($config);
    }

    // ...
}
```

## 4. Crear un intento de pago

Dentro del mismo controlador que acabamos de crear, añadiremos un método para procesar la nueva compra, método el cual después añadiremos a nuestro archivo `web.php` para definir una ruta nueva.

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Config;
use Illuminate\Http\Request;

use IsmaCortGtz\EasyPayPalPhp\Order\PaypalOrder;
use IsmaCortGtz\EasyPayPalPhp\PaypalContext;

class PaypalController extends Controller {
    private $paypalContext;

    public function __construct() {
        $config = Config::get("paypal");
        $this->paypalContext = new PaypalContext($config);
    }

    public function crear() {
        // Logica adicional para procesar ventas (dependera de tu desarrollo y lo que quieras hacer)

        $order = new PaypalOrder($this->paypalContext);
        $success = $order->create($venta->getTotal());

        if (!$success) {
            // Accion en caso de error
            // Puedes hacer un redirect o algo similar
        }

        /*
            Te recomiendo que aqui guardes el id de la order usando $order->id() en la base de datos
            Puedes agregarlo como un nuevo campo en tu modelo Venta o similar.
        */

        // Redirigir al usuario al link para aprobar el pago
        return redirect()->away($order->link());
    }

    // ...
}
```

> [!IMPORTANT]  
> Este ejemplo solo usa el codigo minimo para hacer la libreria funcionar, considera añadir más codigo en los controladores para procesar tus ventas correctamente.

## 5. Capturar el pago

El último paso es crear el controlador para capturar el pago y validar si se llevó a cabo de forma correcta o no.

Para ello crearemos un último controlador y usaremos los métodos `capture` y `isCompleted`.

> [!WARNING]
> Estoy usando el mismo controlador (y el mismo URL) tanto para compras aprobadas como canceladas. No es una acción que recomiende pero es posible si lo deseas. En caso de usar controladores diferentes no es necesario intentar capturar la compra en el controlador de cancelar debido a que es obvio que obtendras un ***false***.

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Config;
use Illuminate\Http\Request;

use IsmaCortGtz\EasyPayPalPhp\Order\PaypalOrder;
use IsmaCortGtz\EasyPayPalPhp\PaypalContext;

class PaypalController extends Controller {

    private $paypalContext;

    public function __construct() {
        $config = Config::get("paypal");
        $this->paypalContext = new PaypalContext($config);
    }



    public function crear() {
        // Logica adicional para procesar ventas (dependera de tu desarrollo y lo que quieras hacer)

        $order = new PaypalOrder($this->paypalContext);
        $success = $order->create($venta->getTotal());

        if (!$success) {
            // Accion en caso de error
            // Puedes hacer un redirect o algo similar
        }

        /*
            Te recomiendo que aqui guardes el id de la order usando $order->id() en la base de datos
            Puedes agregarlo como un nuevo campo en tu modelo Venta o similar.
        */

        // Redirigir al usuario al link para aprobar el pago
        return redirect()->away($order->link());
    }



    public function resultado(Request $request) {
        // El token esta presente siempre (pago aprovado o canceldo)
        $request->validate([
            "token" => "required"
        ]);

        $order = new PaypalOrder($this->paypalContext);
        $success = $order->capture($request->token);

        if (!$success || !$order->isCompleted()) {
            // EN caso de pago cancelado o ya capturado anteriormente

            // Este if validara que el pago solo se puede capturar una vez. SI unicamente quieres validad el estado del pago usa
            // if(!$order->isCompleted())
        }

        /*
            Realizar operaciones adicionales para procesar la venta con exito 
            o, por ejemplo, mostrar un mensaje en caso de cancelar.
        */
    }
}
```
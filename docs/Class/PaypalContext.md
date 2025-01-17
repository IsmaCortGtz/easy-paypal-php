# El objeto `PaypalContext`

Este objeto está destinado a ser el medio de configuración que usará el objeto `PaypalOrder` cuando lo necesite.

Este objeto cuenta con varios métodos para obtener los datos de la configuración establecida de forma rápida; sin embargo, dichos métodos no están diseñados para ser usados por tu web, sino simplemente de manera interna por `PaypalOrder`.

Para implementar este objeto solo necesitas pasar un `array` con la configuración en el constructor y la instancia resultante para crear objetos de tipo `PaypalOrder`.

### Plataformas

El objeto `PaypalContext` está preparado para aceptar diferentes perfiles o plataformas en los cuales alguna configuración podría ser diferente. Por ejemplo, si se quiere implementar pagos en una app móvil mediante una API, los URL de redirección deberían de ser diferentes. 

Esta característica es muy útil cuando tenemos que crear dos instancias de `PaypalContext` para controladores distintos, pero usando el mismo `array` de configuración, lo que vuelve a nuestro software un poco más modular y flexible (_este es el caso para frameworks como ***Laravel***_).

En caso de no especificar el nombre de la plataforma a utilizar para la instancia de `PaypalContext` se usará por defecto `default`. Además, en caso de que un dato de la configuración no se encuentre dentro de la plataforma especificada, también se buscará dentro de `default`.


## Array de configuración

La configuración se da mediante un [array asociativo](https://www.php.net/manual/es/language.types.array.php) de PHP (_el cual es como un [JSON](https://www.json.org/)_) configurado en niveles, siendo el primero el que respecta a las plataformas, mira el siguiente ejemplo.

```php
$config = [
    "default" => [],
    "mobile" => [],
    "api" => [],
    ...
 ]
```

Para el archivo de configuración se espera que el perfil `default` tenga todas las configuraciones, mientras que otros perfiles pueden tener solo aquellos datos que son diferentes a `default`.

Dentro de cada perfil, el array puede tener los siguientes campos.

### Campo `mode`: string

Este es un dato de tipo `string` que representa si la `API de PayPal` usará el modo `sandbox` o `live`, siendo estos dos últimos los valores posibles del campo.

### Campo `currency`: string

Este es un dato de tipo `string` que configura el tipo de moneda que usara PayPal para procesar el pago. Para ver los valores disponibles, consulta esta [guía oficial](https://developer.paypal.com/api/rest/reference/currency-codes/).

### Campo `locale`: string

Este es un dato de tipo `string` que configura el idioma de la interfaz de pago. Este campo acepta un string de 5 caracteres, por ejemplo: `es-MX`, `da-DK`, `he-IL`, `id-ID`, `ja-JP`, `no-NO`, `pt-BR`, `ru-RU`, `sv-SE`, `th-TH`, `zh-CN`, `zh-HK`, `zh-TW`, etc. Puedes consultar [aquí](https://www.techonthenet.com/js/language_tags.php) la lista completa.

### Campo `brand_name`: string

Este es un dato de tipo `string` que configura el nombre de la tienda o empresa que aparecerá en la interfaz de pago de PayPal. Tiene un máximo de 127 caracteres.

### Campo `return_url`: string

Este es un dato de tipo `string` que configura el URL de **nuestra página** al que PayPal redirigirá al usuario cuando el pago sea ***aprobado***.

### Campo `cancel_url`: string

Este es un dato de tipo `string` que configura el URL de **nuestra página** al que PayPal redirigirá al usuario cuando el pago sea ***cancelado***.

### Campo `sandbox`: string

Este es un dato de tipo `array` que configura las credenciales que usará la librería cuando trabaja en modo `sandbox`.

Este array tendrá dos campos de tipo `string` los cuales son los siguientes.

- `client_id`. En este campo de tipo `string` configurará el `Client ID` conseguido desde la página de `PayPal Dev`.
- `secret_key`. En este campo de tipo `string` configurará el `Secret` conseguido desde la página de `PayPal Dev`.

### Campo `sandbox`: string

Este es un dato de tipo `array` que configura las credenciales que usará la librería cuando trabaja en modo `live` (_para ***producción***_).

Este array tendrá dos campos de tipo `string` los cuales son los siguientes.

- `client_id`. En este campo de tipo `string` configurará el `Client ID` conseguido desde la página de `PayPal Dev` en modo `live`.
- `secret_key`. En este campo de tipo `string` configurará el `Secret` conseguido desde la página de `PayPal Dev` en modo `live`.


## Ejemplo de un array de configuración

A continuación se muestra un array con la configuración mínima necesaria para trabajar.

```bash
$config = [
    "default" => [
        "mode" => "sandbox",
        "currency" => "MXN",
        "locale" => "es-MX",
        "brand_name" => "Empresa Generica",
        "return_url" => "http://localhost:8001/paypal/aprobado",
        "cancel_url" => "http://localhost:8001/paypal/cancelado",

        "sandbox" => [
            "client_id" => "PAYPAL_SANDBOX_CLIENT_ID",
            "secret_key" => "PAYPAL_SANDBOX_SECRET_KEY"
        ],

        "live" => [
            "client_id" => "PAYPAL_LIVE_CLIENT_ID",
            "secret_key" => "PAYPAL_LIVE_SECRET_KEY"
        ],
    ],
];
```

Esta configuración únicamente tiene el perfil `default`, por lo que si queremos agregar nuevas plataformas, basta con agregar dentro los valores a remplazar.

```bash
$config = [
    "default" => [
        "mode" => "sandbox",
        "currency" => "MXN",
        "locale" => "es-MX",
        "brand_name" => "Empresa Generica",
        "return_url" => "http://localhost:8001/paypal/aprobado",
        "cancel_url" => "http://localhost:8001/paypal/cancelado",

        "sandbox" => [
            "client_id" => "PAYPAL_SANDBOX_CLIENT_ID",
            "secret_key" => "PAYPAL_SANDBOX_SECRET_KEY"
        ],

        "live" => [
            "client_id" => "PAYPAL_LIVE_CLIENT_ID",
            "secret_key" => "PAYPAL_LIVE_SECRET_KEY"
        ]
    ],

    "mobile" => [
        "return_url" => "http://localhost:8001/mobile/paypal/aprobado",
        "cancel_url" => "http://localhost:8001/mobile/paypal/cancelado",

        "sandbox" => [
            "client_id" => "PAYPAL_SANDBOX_CLIENT_ID"
        ]
    ]
];
```

En este caso, podemos ver que la plataforma mobile remplaza los URL de retorno de la API y el `client_id` del modo `sandbox`.

> [!WARNING]  
> No se recomienda sobreescribir las credenciales de forma individual, es decir, unicamente el `client_id` o `secret_key`.

> [!CAUTION]
> Agregar las credenciales directamente al archivo PHP podria ser un riesgo de seguridad. Se recomienda hacerlo mediante variables de entorno con la variable [`$_ENV`](https://www.php.net/manual/en/reserved.variables.environment.php) o alguna función especifica del framework.


## Creando una instancia

Para crear una instancia de nuestro `PaypalContext` puedes usar este código de ejemplo.

```php
use IsmaCortGtz\EasyPayPalPhp\PaypalContext;


//   Si estas usando php vanilla podrias necesitar usar
//   require_once 'vendor/autoload.php'; 


$config = [...];

// Usando la plataforma por defecto 'default'
$ctx = new PaypalContext($config);

// Usando la plataforma 'mobile'
$ctx = new PaypalContext($config, "mobile");
```
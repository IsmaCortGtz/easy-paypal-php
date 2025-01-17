# Easy PayPal PHP

<p>
  <a href="#"><img src="https://img.shields.io/badge/stable-v1.0.0-blue.svg" alt="Stable: v1.0.0"></a>
  <a href="https://www.php.net/"><img src="https://img.shields.io/badge/php->=5.5.0-8892BF.svg" alt="PHP: >=5.5.0"></a>
  <a href="https://opensource.org/license/mit"><img src="https://img.shields.io/badge/License-MIT-4b9081.svg" alt="License: MIT"></a>
</p>

Esta es una librería liviana para usar la [`API de PayPal`](https://developer.paypal.com/api/rest/) en PHP. 

`Easy PayPal PHP` no tiene dependencias, por lo que puede funcionar sin problemas para cualquier desarrollo con cualquier framework, ya sea [`Laravel`](https://laravel.com/) o `vanilla`.


### Tabla de contenido

1. [Requisitos](#requisitos)
2. [Instalación](#instalación)
3. [Documentación](#documentación)
4. [Licencia](#licencia)


## Requisitos

`Easy PayPal PHP` no tiene dependencias adicionales, por lo que los requisitos son en realidad pocos.

- [`php`](https://www.php.net/) (_mínimo **5.5.0**_).
- [`composer`](https://getcomposer.org/) (_Cualquier versión debería funcionar_). 

Las peticiones se realizan mediante [`fopen`](https://www.php.net/manual/es/function.fopen.php), por lo que incluso funciona sin [`php-curl`](https://www.php.net/manual/es/book.curl.php).


## Instalación

Instalar `Easy PayPal PHP` es tan sencillo como ejecutar el siguiente comando en la raíz de nuestro proyecto.

```bash
composer require ismacortgtz/easy-paypal-php
```


## Documentación

Para ver la documentación y la forma de usar la librería, dirígete a la carpeta [`docs`](./docs/) o usa el siguiente índice.

1. [Uso](./docs/usage.md)
2. [Contexto](./docs/Class/PaypalContext.md)
3. [Crear orden](./docs/Class/PaypalOrder.md#paypalorder-crear-pago)
4. [Capturar orden](./docs/Class/PaypalOrder.md#paypalorder-capturar-pago)
5. Ejemplos
    - [Laravel](./docs/examples/Laravel.md)


## Licencia

Este proyecto está publicado bajo la licencia [MIT](https://opensource.org/license/mit).
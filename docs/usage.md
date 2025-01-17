# ¿Cómo usar Easy PayPal PHP?

Esta librería solo permite realizar cobros simples de un solo pago a través de la [`API de PayPal`](https://developer.paypal.com/api/rest/), por lo que si quieres realizar operaciones más complejas como suscripciones, pagos mensuales o configurar más a detalle las opciones enviadas a la [`API`](https://developer.paypal.com/api/rest/) tendrás que buscar otra alternativa.

## ¿Cómo funciona?

### API vanilla

La [`API de PayPal`](https://developer.paypal.com/api/rest/) tiene un flujo de 3 pasos para poder realizar un pago.

1. Crear el intento (INTENT) de pago desde el servidor.
2. Autorizar el pago desde la página de PayPal (por el cliente).
3. Capturar el pago realizado por dicho cliente para abonar el saldo a la cuenta del negocio.

### Con `Easy PayPal PHP`

La librería trabaja con 2 clases que realizan todas las llamadas a la API de forma automática. El flujo de trabajo sería el siguiente.

1. Usar un URL de nuestra aplicación para generar el pago y redirigir al usuario a él.
2. El usuario deberá de aprobar o rechazar el pago y PayPal nos enviará al URL que configuremos para ello.
3. Dentro de este nuevo URL validaremos el estado del pago y, usando el `id` realizaremos las operaciones pertinentes.

La configuración que usaremos en el paso 1 y 3 se hará mediante el objeto `PaypalContext`, las operaciones con los pagos se harán mediante `PaypalOrder`.


## ¿Como usar los objetos?

A continuación se dará una breve descripción de cómo se utiliza cada objeto en orden. Esta descripción es agnóstica de cualquier framework, por lo que si quieres ver ejemplos de código para resolver cualquier duda, puedes dirigirte a la sección de ejemplos.

### `PaypalContext`

Este es el objeto de configuración básico desde donde la librería obtendrá las claves de API, las URL a donde PayPal nos redirigirá después del pago y configuraciones extras.

En un contexto en el que se usa el MVC usando métodos, se recomienda que se cree la instancia de este objeto en el constructor de nuestro controlador, aunque también puede hacerse en un archivo por separado e importarse cada vez que se necesite.


### `PaypalOrder`: Crear intento de pago

Este es el primer paso dentro de nuestro flujo de acción. Para crear una instancia de este objeto, necesitamos pasar por parámetro al constructor nuestro contexto creado usando `PaypalContext`.

Una vez teniendo el objeto creado, usaremos su método `create` el cual acepta un parámetro con el total a pagar.
- Este valor se convertirá internamente a string usando `strval`, por lo que puedes usar números enteros o flotantes. 
- En caso de utilizar un string **asegúrate de solo utilizar números** y no ningún símbolo. 
- Este método devuelve un booleano representando si se realizó la llamada a la API con éxito (***true***) o no (***false***).

Después de haber usado el método `create` podremos usar los métodos `id` y `link`. Estos nos devolverán el ID que PayPal asignó a este intento de pago y el link al que debemos redirigir el usuario para que realice el pago respectivamente.
- Si el método `create` no se realizó con éxito, estos dos métodos devolverán **`null`**.
- Es recomendable guardar el `id` en la base de datos en caso de ser necesario, pues después de aprobarse o cancelarse el pago, el usuario será redirigido a una URL nueva de nuestra web.

> [!NOTE]  
> Una vez que el usuario acepte o cancele el pago sera redirigido nuevamente a nuestra pagina en los links que hayamos configurado en el `PaypalContext`. PayPal agregara un parametro en el URL con el `id` del pago bajo el nombre `token` sin importar si el pago se aprobo o no.


### `PaypalOrder`: Capturar el pago

Esta será la página donde PayPal nos redirigirá una vez que el usuario acepte o cancele el pago. PayPal nos agregará un parámetro en el URL con el nombre `token`, el cual será igual al `id` generado en la página anterior. Podemos obtener este `token` usando `$_GET["token"]` o alguna función específica de tu framework.

```bash
# Ejemplo de URL a la que nos redirige Paypal con el pago aprobado
http://localhost:8001/paypal/aprobado?token=XXXXXXXXXXXXXXXXX&PayerID=XXXXXXXXXXXXX

# Ejemplo de URL a la que nos redirige Paypal con el pago cancelado
http://localhost:8001/paypal/cancelado?token=XXXXXXXXXXXXXXXXX
```

El primer paso en esta página es instancia de un objeto `PaypalOrder` pasando como parámetro nuestro contexto al constructor.

Después tendremos que usar el método `capture` de nuestro objeto `PaypalOrder`.
- Este método devolverá un `bool` que representa si se capturó el pago correctamente (***true***) o no (***false***).
- Un pago solo se puede capturar una vez, por lo que si el pago ya se capturó anteriormente, este método devolverá ***false***.

Después de intentar capturar el pago, podremos usar el método `isCompleted` del objeto `PaypalOrder`.
- Este devolverá un `bool` que nos dice si el pago se aprobó correctamente (***true***) o no (***false***).
- Este valor siempre será ***true*** cuando el pago sea aprobado por el usuario, sin importar si ya fue capturado anteriormente o no.
- Si usas este método sin antes usar el método `capture` este devolverá `null`.

> [!TIP]
> Con estos metodos ya tendras el pago aprovado y los fondos se transferiran a nuestra cuenta de PayPal, por lo que ahora solo necesitas realizar las operaciones que tu web necesite para completar la venta/compra. Por ello se recomienda almacenar el `id` / `token` en la base de datos junto a la venta o compra (_en caso de ser necesario_).


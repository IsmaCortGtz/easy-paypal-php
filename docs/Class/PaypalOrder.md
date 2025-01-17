# El objeto `PaypalOrder`

Este objeto está destinado a usarse para crear y capturar las compras (order) de la API de PayPal usando una instancia de `PaypalContext`.

A la hora de crear una instancia de este objeto, se debe pasar como parámetro un objeto de tipo `PaypalContext`.


## `PaypalOrder`: Crear pago

Una vez teniendo una instancia del objeto `PaypalOrder` podemos utilizar el método `create` para crear el intento de pago.
- Este método acepta como parámetro la cantidad total a pagar por el usuario.
- El total a pagar puede ser un número entero, flotante o string (Se usa `strval` para convertir el dato a string). Pero el string solo debe de ser numérico y sin símbolos.
- Este método devuelve un booleano representando si se realizó la llamada a la API con éxito (***true***) o no (***false***).

```php
// Crear la instancia
$order = new PaypalOrder($ctx);

// Crear
$success = $order->create("159.52");

echo var_dump($success); // true o false
```

Después de haber usado el método `create` podremos usar los métodos `id` y `link`. Estos nos devolverán el ID que PayPal asignó a este intento de pago y el link al que debemos redirigir el usuario para que realice el pago respectivamente.
- Si el método `create` no se realizó con éxito, estos dos métodos devolverán **`null`**.
- Es recomendable guardar el `id` en la base de datos en caso de ser necesario, pues después de aprobarse o cancelarse el pago, el usuario será redirigido a una URL nueva de nuestra web.

```php
// Crear la instancia
$order = new PaypalOrder($ctx);

// Intentar ver antes de crear la orden
echo var_dump($order->id());    // null
echo var_dump($order->link());  // null

// Crear
$success = $order->create("159.52");

// Ver valores
echo var_dump($order->id());    // "XXXXXXXX" (id)
echo var_dump($order->link());  // "https://paypal..." (link de pago)

// REALIZAR OPERACIONES DE VENTA

// Redirigir al usuario a la pagina de pago
header("Location: ".$order->link());
die();
```

## `PaypalOrder`: Capturar pago

Después de que el usuario apruebe o cancele el pago, ahora deberemos de capturar el pago para que los fondos se ingresen a nuestra cuenta. Para ello debemos de utilizar el `id` (llamado también `token`) que la `API de PayPal` nos devuelve en la página de retorno.

En este nuevo controlador (tanto para aceptar como cancelar) deberemos crear una instancia del objeto `PaypalOrder` con el contexto.

Este objeto además tiene el método `capture` el cual acepta como parámetro el token enviado por el URL de nuestra página.
- Si en el `PaypalContext` configuramos un URL de retorno como `http://localhost:8000/paypal/aceptado` entonces la API nos devolverá algo como `http://localhost:8000/paypal/aceptado?token=XXXXXXXXXXXXXXXXX&PayerID=XXXXXXXXXXXXX`. (Si se cancela solo tendremos el `token` pero no la variable `PayerID`).
- Este método devuelve un `bool` que representa si se ***capturó*** el pago correctamente. El pago solo puede capturarse una vez, por lo que si se ejecuta este método con un `token` ya capturado, devolverá `false`.

Una vez ejecutado el método `capture` podemos usar otro método llamado `isCompleted`. Este método nos dirá si los fondos fueron capturados correctamente, independientemente de si se capturaron anteriormente o ahora.
- Este método devolverá `true` siempre que se use con una orden aceptada y capturada, sin importar si se repite o no.
- Si se rechaza el pago o la captura tuvo algún error de conexión, entonces devolverá `false`.
- Si se usa antes de capturar un pago, entonces devolverá `false`.

```php
// Crear la instancia
$order = new PaypalOrder($ctx);

// Crear
$success = $order->capture("XXXXXXXXXXXXXXXXX"); // Remplaza XXXXXXXXXXXXXXXXX por tu token obtenido del URL

// Ver valores
echo var_dump($success);              // true (bool)
echo var_dump($order->isCompleted()); // true (bool)

// REALIZAR OPERACIONES DE VENTA
```
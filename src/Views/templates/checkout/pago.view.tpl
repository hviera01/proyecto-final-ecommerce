<h2>Pago</h2>

{{if errorMessage}}
<p class="mensaje-error">{{errorMessage}}</p>
{{endif errorMessage}}

<h3>Resumen del pedido</h3>
<table border="1" cellpadding="4" cellspacing="0">
<tr><th>Producto</th><th>Cantidad</th><th>Subtotal</th></tr>
{{foreach items}}
<tr><td>{{proddsc}}</td><td>{{cantidad}}</td><td>L {{subtotal}}</td></tr>
{{endfor items}}
</table>
<p class="producto-precio">Total a pagar: L {{total}}</p>

<h3>Datos de pago</h3>
<div class="panel">
<p>Pasarela de pago simulada para fines academicos. No se procesan pagos reales.</p>

<form method="post" action="index.php?page=Checkout_Checkout">
<input type="hidden" name="mode" value="PAY">
<p>
  <label>Nombre en la tarjeta</label><br>
  <input type="text" name="nombretarjeta" required>
</p>
<p>
  <label>Numero de tarjeta</label><br>
  <input type="text" name="numerotarjeta" maxlength="19" placeholder="4111111111111111" required>
</p>
<p>
  <label>Vencimiento (MM/AA)</label><br>
  <input type="text" name="vencimiento" placeholder="12/27" required>
</p>
<p>
  <label>CVV</label><br>
  <input type="text" name="cvv" maxlength="4" required>
</p>
<p><button type="submit" class="boton-principal">Pagar</button></p>
</form>
</div>

<p><a href="index.php?page=Checkout_Checkout">&larr; Volver al carrito</a></p>

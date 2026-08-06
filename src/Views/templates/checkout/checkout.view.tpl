<h2>Carretilla de Compra</h2>

{{if errorMessage}}
<p class="mensaje-error">{{errorMessage}}</p>
{{endif errorMessage}}
{{if successMessage}}
<p class="mensaje-ok">{{successMessage}}</p>
{{endif successMessage}}

{{ifnot hasItems}}
<p>Su carrito esta vacio.</p>
{{endifnot hasItems}}

{{if hasItems}}
<table border="1" cellpadding="4" cellspacing="0">
<tr>
  <th>Producto</th>
  <th>Cantidad</th>
  <th>Precio</th>
  <th>Subtotal</th>
  <th></th>
</tr>
{{foreach items}}
<tr>
  <td data-label="Producto">{{proddsc}}</td>
  <td data-label="Cantidad">
    <form method="post" action="index.php?page=Checkout_Checkout">
      <input type="hidden" name="mode" value="UPDATE">
      <input type="hidden" name="prodcod" value="{{prodcod}}">
      <input type="number" name="cantidad" value="{{cantidad}}" min="0" max="{{prodstock}}" size="3">
      <button type="submit">Actualizar</button>
    </form>
  </td>
  <td class="precio" data-label="Precio">L {{precio_unitario}}</td>
  <td class="precio" data-label="Subtotal">L {{subtotal}}</td>
  <td data-label="Acciones">
    <form method="post" action="index.php?page=Checkout_Checkout">
      <input type="hidden" name="mode" value="REMOVE">
      <input type="hidden" name="prodcod" value="{{prodcod}}">
      <button type="submit">Quitar</button>
    </form>
  </td>
</tr>
{{endfor items}}
</table>

<p class="producto-precio">Total: L {{total}}</p>
<a class="boton boton-principal" href="index.php?page=Checkout_Checkout&mode=PAYFORM">Proceder al pago</a>
{{endif hasItems}}

<p><a href="index.php?page=Products_Products">&larr; Seguir comprando</a></p>

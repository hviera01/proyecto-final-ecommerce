<h2>Detalle de Compra #{{ordencod}}</h2>

<p>Fecha: {{ordenfecha}} &middot; Estado: <span class="badge {{ordenestBadge}}">{{ordenest}}</span></p>

<table border="1" cellpadding="4" cellspacing="0">
<tr><th>Producto</th><th>Cantidad</th><th>Precio</th><th>Subtotal</th></tr>
{{foreach items}}
<tr>
  <td data-label="Producto">{{proddsc}}</td>
  <td data-label="Cantidad">{{cantidad}}</td>
  <td class="precio" data-label="Precio">L {{precio_unitario}}</td>
  <td class="precio" data-label="Subtotal">L {{subtotal}}</td>
</tr>
{{endfor items}}
</table>

<p class="producto-precio">Total: L {{ordentotal}}</p>

<p><a href="index.php?page=Orders_Orders">&larr; Volver al historial</a></p>

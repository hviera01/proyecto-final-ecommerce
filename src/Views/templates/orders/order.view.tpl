<h2>Detalle de Compra #{{ordencod}}</h2>

<p>Fecha: {{ordenfecha}} &middot; Estado: <span class="badge {{ordenestBadge}}">{{ordenest}}</span></p>

<table border="1" cellpadding="4" cellspacing="0">
<tr><th>Producto</th><th>Cantidad</th><th>Precio</th><th>Subtotal</th></tr>
{{foreach items}}
<tr>
  <td>{{proddsc}}</td>
  <td>{{cantidad}}</td>
  <td class="precio">L {{precio_unitario}}</td>
  <td class="precio">L {{subtotal}}</td>
</tr>
{{endfor items}}
</table>

<p class="producto-precio">Total: L {{ordentotal}}</p>

<p><a href="index.php?page=Orders_Orders">&larr; Volver al historial</a></p>

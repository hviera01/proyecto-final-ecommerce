<h2>Mis Compras</h2>

{{ifnot hasOrders}}
<p>Aun no ha realizado compras.</p>
{{endifnot hasOrders}}

{{if hasOrders}}
<table border="1" cellpadding="4" cellspacing="0">
<tr>
  <th>No. Orden</th>
  <th>Fecha</th>
  <th>Total</th>
  <th>Estado</th>
  <th>Transaccion</th>
  <th></th>
</tr>
{{foreach orders}}
<tr>
  <td>{{ordencod}}</td>
  <td>{{ordenfecha}}</td>
  <td class="precio">L {{ordentotal}}</td>
  <td><span class="badge {{ordenestBadge}}">{{ordenest}}</span></td>
  <td><span class="badge {{transestBadge}}">{{transest}}</span> <span class="categoria-tag">{{transref}}</span></td>
  <td><a href="index.php?page=Orders_Order&ordencod={{ordencod}}">Ver detalle</a></td>
</tr>
{{endfor orders}}
</table>
{{endif hasOrders}}

<p><a href="index.php?page=Products_Products">Ir al catalogo</a></p>

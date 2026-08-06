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
  <td data-label="No. Orden">{{ordencod}}</td>
  <td data-label="Fecha">{{ordenfecha}}</td>
  <td class="precio" data-label="Total">L {{ordentotal}}</td>
  <td data-label="Estado"><span class="badge {{ordenestBadge}}">{{ordenest}}</span></td>
  <td data-label="Transaccion"><span class="badge {{transestBadge}}">{{transest}}</span> <span class="categoria-tag">{{transref}}</span></td>
  <td data-label="Detalle"><a href="index.php?page=Orders_Order&ordencod={{ordencod}}">Ver detalle</a></td>
</tr>
{{endfor orders}}
</table>
{{endif hasOrders}}

<p><a href="index.php?page=Products_Products">Ir al catalogo</a></p>

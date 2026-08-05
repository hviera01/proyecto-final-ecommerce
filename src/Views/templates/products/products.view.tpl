<h2>Catalogo de Productos</h2>

{{if product_INS}}
<a class="boton boton-principal enlace-nuevo" href="index.php?page=Products_Product&mode=INS">+ Nuevo producto</a>
{{endif product_INS}}

<div class="grid-productos">
{{foreach products}}
<div class="tarjeta-producto">
  <div class="categoria-tag">{{catdsc}}</div>
  <div class="producto-nombre">
    {{if ~product_DSP}}
    <a href="index.php?page=Products_Product&mode=DSP&prodcod={{prodcod}}">{{proddsc}}</a>
    {{endif ~product_DSP}}
    {{ifnot ~product_DSP}}
    {{proddsc}}
    {{endifnot ~product_DSP}}
  </div>
  <div class="producto-precio">L {{prodprecio}}</div>
  <div class="producto-stock">Stock: {{prodstock}} &middot; {{prodest}}</div>
  <div class="acciones-producto">
    {{if ~product_UPD}}
    <a href="index.php?page=Products_Product&mode=UPD&prodcod={{prodcod}}">Editar</a>
    {{endif ~product_UPD}}
    {{if ~product_DEL}}
    <a href="index.php?page=Products_Product&mode=DEL&prodcod={{prodcod}}">Eliminar</a>
    {{endif ~product_DEL}}
  </div>
</div>
{{endfor products}}
</div>

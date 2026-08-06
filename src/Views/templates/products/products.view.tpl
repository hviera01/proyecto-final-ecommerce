<div class="hero">
  <div class="hero-texto">
    <span class="hero-kicker">Catalogo</span>
    <h2>Todo en tecnologia, en un solo lugar</h2>
    <p>Telefonos, accesorios y audio con entrega inmediata.</p>
  </div>
</div>

{{if product_INS}}
<a class="boton boton-principal enlace-nuevo" href="index.php?page=Products_Product&mode=INS">+ Nuevo producto</a>
{{endif product_INS}}

<div class="grid-productos">
{{foreach products}}
<div class="tarjeta-producto">
  <div class="tarjeta-foto">
    {{if hasImage}}
    <img src="public/img/productos/{{prodimg}}" alt="{{proddsc}}" loading="lazy">
    {{endif hasImage}}
    {{ifnot hasImage}}
    <div class="producto-img-placeholder {{placeholderClass}}">{{placeholderLetter}}</div>
    {{endifnot hasImage}}
  </div>
  <div class="tarjeta-cuerpo">
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
    <div class="producto-stock">
      Stock: {{prodstock}}
      {{if agotado}}
      <span class="badge badge-err">Agotado</span>
      {{endif agotado}}
      {{ifnot agotado}}
      <span class="badge badge-ok">Disponible</span>
      {{endifnot agotado}}
    </div>
    <div class="acciones-producto">
      {{if ~product_UPD}}
      <a href="index.php?page=Products_Product&mode=UPD&prodcod={{prodcod}}">Editar</a>
      {{endif ~product_UPD}}
      {{if ~product_DEL}}
      <a href="index.php?page=Products_Product&mode=DEL&prodcod={{prodcod}}">Eliminar</a>
      {{endif ~product_DEL}}
    </div>
  </div>
</div>
{{endfor products}}
</div>

<h2>{{modeTitle}}</h2>

<div class="panel panel-producto">

{{ifnot isInsert}}
<div class="producto-foto-grande">
{{if hasImage}}
<img src="public/img/productos/{{prodimg}}" alt="{{proddsc}}">
{{endif hasImage}}
{{ifnot hasImage}}
<div class="producto-img-placeholder {{placeholderClass}}">{{placeholderLetter}}</div>
{{endifnot hasImage}}
</div>
{{endifnot isInsert}}

<form method="post" action="index.php?page=Products_Product" enctype="multipart/form-data">
<input type="hidden" name="mode" value="{{mode}}">
{{ifnot isInsert}}
<input type="hidden" name="prodcod" value="{{prodcod}}">
{{endifnot isInsert}}

<p>
  <label>Descripcion</label><br>
  <input type="text" name="proddsc" value="{{proddsc}}" {{readonly}} required>
</p>
<p>
  <label>Detalle</label><br>
  <input type="text" name="proddet" value="{{proddet}}" {{readonly}}>
</p>
<p>
  <label>Categoria</label><br>
  <select name="catcod" {{readonly}}>
    {{foreach categorias}}
    <option value="{{catcod}}" {{if selected}}selected{{endif selected}}>{{catdsc}}</option>
    {{endfor categorias}}
  </select>
</p>
<p>
  <label>Precio (L)</label><br>
  <input type="number" step="0.01" name="prodprecio" value="{{prodprecio}}" {{readonly}} required>
</p>
<p>
  <label>Stock</label><br>
  <input type="number" name="prodstock" value="{{prodstock}}" {{readonly}} required>
</p>

{{if canEditImage}}
<p>
  <label>Foto del producto</label><br>
  <input type="file" name="prodimg" accept=".jpg,.jpeg,.png,.webp">
</p>
{{endif canEditImage}}

{{if showCommitBtn}}
{{ifnot isDelete}}
<p><button type="submit" class="boton-principal">Guardar</button></p>
{{endifnot isDelete}}
{{if isDelete}}
<p><button type="submit" class="boton-principal">Confirmar eliminacion</button></p>
{{endif isDelete}}
{{endif showCommitBtn}}
</form>
</div>

{{if canAddToCart}}
<h3>Agregar al carrito</h3>
<div class="panel">
<form method="post" action="index.php?page=Checkout_Checkout">
  <input type="hidden" name="mode" value="ADD">
  <input type="hidden" name="prodcod" value="{{prodcod}}">
  <label>Cantidad</label>
  <input type="number" name="cantidad" value="1" min="1" max="{{prodstock}}">
  <p><button type="submit" class="boton-principal">Agregar al carrito</button></p>
</form>
</div>
{{endif canAddToCart}}

<p><a href="index.php?page=Products_Products">&larr; Volver al catalogo</a></p>

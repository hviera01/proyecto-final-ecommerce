<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>{{pageTitle}}</title>
<link rel="stylesheet" href="public/css/style.css?v=2">
</head>
<body>

<div class="cabecera">
  {{if isLogged}}
  <span class="usuario-actual">{{username}}</span>
  {{endif isLogged}}
  <div class="nombre-negocio">Variedades <strong>Lopsi</strong></div>
  <span class="eslogan">Tecnologia &middot; Telefonia &middot; Accesorios</span>
</div>

<div class="menu">
  {{foreach nav}}
  <a href="{{nav_url}}">{{nav_label}}</a>
  {{endfor nav}}
</div>

<div class="contenido">
{{!content}}
</div>

<div class="pie">
  Variedades Lopsi - Proyecto Final
</div>

</body>
</html>

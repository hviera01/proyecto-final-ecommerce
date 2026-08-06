# Variedades Lopsi - Proyecto Final (Comercio Electronico)

Tienda en linea de tecnologia, telefonia y accesorios. Construido sobre el
esquema de MVC en PHP OOP visto en clase (simplePHPMvcOop): controladores
publicos/privados, esquema de seguridad por roles y funciones, plantillas
`.view.tpl` con `{{if}}` / `{{foreach}}`, y ruteo `index.php?page=Namespace_Class`.

## Requisitos

- XAMPP (incluye PHP 8+ y MySQL/MariaDB): https://www.apachefriends.org/
- Composer: https://getcomposer.org/
- Git

## Instalacion (primera vez, cada integrante en su propia maquina)

1. Instalar XAMPP y Composer si no los tienen.
2. Abrir una terminal dentro de la carpeta `htdocs` de XAMPP y clonar el repo
   ahi mismo (importante: el nombre de la carpeta clonada define la URL local):
   ```
   cd C:\xampp\htdocs
   git clone https://github.com/hviera01/proyecto-final-ecommerce.git ecommerce_pf
   cd ecommerce_pf
   ```
3. Instalar el autoload de Composer (el proyecto no usa librerias externas,
   solo el autoload PSR-4, asi que esto es rapido):
   ```
   composer install
   ```
   Esto genera la carpeta `vendor/`, que no viene en el repo (esta en
   `.gitignore`) y es **obligatoria** para que `index.php` funcione.
4. Abrir el panel de XAMPP y arrancar **Apache** y **MySQL**.
5. Crear la base de datos importando el script (trae la estructura completa
   + los datos de ejemplo, incluye `CREATE DATABASE`, no hace falta crearla
   antes a mano):
   - Opcion phpMyAdmin: entrar a `http://localhost/phpmyadmin`, pestaña
     "Importar", seleccionar `database/schema.sql`, ejecutar.
   - Opcion linea de comandos:
     ```
     "C:\xampp\mysql\bin\mysql.exe" -u root < database\schema.sql
     ```
6. Revisar `src/Config/db.config.php`. Por defecto ya esta configurado para
   un XAMPP recien instalado (`host=127.0.0.1`, `port=3306`, `user=root`,
   `pass=""`), asi que **normalmente no hay que tocar nada**. Solo cambien
   el puerto si en su maquina el 3306 ya esta ocupado por otro MySQL/otro
   XAMPP (el error de conexion en el navegador lo va a indicar).
7. Abrir en el navegador:
   ```
   http://localhost/ecommerce_pf/
   ```

## Ejecutar el proyecto los siguientes dias (ya instalado)

Solo hace falta tener **Apache** y **MySQL** iniciados desde el panel de
XAMPP y entrar a `http://localhost/ecommerce_pf/`. No hay que repetir la
instalacion ni volver a importar la base de datos (a menos que quieran
resetear los datos de prueba, en cuyo paso vuelvan a correr `database/schema.sql`,
lo cual borra y recrea todas las tablas).

## Sincronizar cambios del equipo

Cada vez que alguien suba cambios al repo:

```
git pull
composer install
```

Y si `database/schema.sql` cambio (nuevas tablas/columnas), volver a
importarlo para tener la base de datos al dia.

## Usuarios de prueba

| Rol      | Correo                          | Contrasena   |
|----------|----------------------------------|--------------|
| Admin    | admin@variedadeslopsi.com        | Admin1234    |
| Cliente  | cliente@variedadeslopsi.com      | Cliente1234  |

## Esquema de seguridad

Basado exactamente en las entidades vistas en clase:

- `usuario`, `roles`, `funciones`, `roles_usuario`, `funciones_roles`
- `PublicController` (Login, Register) vs `PrivateController` (todo lo demas,
  valida `isLogged()` y `isAuthorized()` antes de ejecutar el controlador)
- `isFeatureAuthorized($functionCode)` dentro de los controladores privados
  para permisos granulares (ej: `product_DSP`, `product_UPD`, `product_DEL`,
  `product_INS`)
- El rol **ADMIN** tiene control total del catalogo (ver, crear, editar, eliminar).
- El rol **CLIENTE** solo puede ver el catalogo, comprar y ver su propio historial.
- El menu (`nav.config.json`) se arma dinamicamente segun las funciones
  autorizadas para el usuario logueado.

## Pasarela de pago (simulada)

No se conecta a ningun procesador real. Se implementan los 8 pasos vistos en
clase (seleccion, configuracion, entrada de datos, procesamiento,
aprobacion/rechazo) de forma local:

- Se valida formato de tarjeta (13-19 digitos), vencimiento y CVV.
- Regla de simulacion para la demo: si el **ultimo digito del numero de
  tarjeta es par, el pago se aprueba**; si es impar, se rechaza.
- Toda transaccion (aprobada o rechazada) se guarda en la tabla `transaccion`
  junto con su `orden` asociada.
- Solo si el pago es aprobado se descuenta el inventario (`producto.prodstock`)
  y se marca el carrito como convertido.

## Carrito de compras

- Un carrito activo por usuario (`carrito` / `carrito_item`).
- Cantidad disponible = stock en inventario - cantidad reservada en otros
  carritos activos (formula vista en clase), para evitar sobreventa.
- Permite actualizar cantidades y quitar productos antes de pagar.

## Historico de transacciones

`Orders_Orders` muestra unicamente las compras del usuario logueado
(filtrado por `usercod`), con acceso al detalle de cada orden
(`Orders_Order`).

## Estructura

```
index.php                     Front controller / router
src/Config/                   db.config.php, nav.config.json
src/Controllers/               Sec, Products, Checkout, Orders
src/Dao/                       Acceso a datos (PDO)
src/Utilities/                 Security, View, TemplateEngine, Nav
src/Views/templates/           Vistas .view.tpl
database/schema.sql            Estructura + datos semilla (uso local, incluye CREATE DATABASE)
database/schema_hosting.sql    Mismo contenido, sin CREATE DATABASE (para hosting compartido)
integrantes.txt                Equipo de desarrollo
```

**¿Cual script de base de datos usar?** `schema.sql` es para instalar el proyecto en tu propia maquina (XAMPP local) — incluye `CREATE DATABASE`. `schema_hosting.sql` es el mismo esquema pero sin `CREATE DATABASE`, pensado para hosting compartido (como InfinityFree) donde la base de datos ya se crea desde el panel y solo se importan las tablas dentro de ella.

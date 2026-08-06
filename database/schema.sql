CREATE DATABASE IF NOT EXISTS db_ecommerce_pf CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE db_ecommerce_pf;

DROP TABLE IF EXISTS transaccion;
DROP TABLE IF EXISTS orden_item;
DROP TABLE IF EXISTS orden;
DROP TABLE IF EXISTS carrito_item;
DROP TABLE IF EXISTS carrito;
DROP TABLE IF EXISTS producto;
DROP TABLE IF EXISTS categoria;
DROP TABLE IF EXISTS funciones_roles;
DROP TABLE IF EXISTS funciones;
DROP TABLE IF EXISTS roles_usuario;
DROP TABLE IF EXISTS roles;
DROP TABLE IF EXISTS usuario;

CREATE TABLE usuario (
    usercod        INT AUTO_INCREMENT PRIMARY KEY,
    useremail      VARCHAR(150) NOT NULL UNIQUE,
    username       VARCHAR(100) NOT NULL,
    userpswd       VARCHAR(255) NOT NULL,
    userfching     DATETIME NOT NULL,
    userpswdest    VARCHAR(10) NOT NULL DEFAULT 'ACT',
    userpswdexp    DATETIME NULL,
    userest        VARCHAR(10) NOT NULL DEFAULT 'ACT',
    useractcod     VARCHAR(50) NULL,
    userpswdchg    TINYINT(1) NOT NULL DEFAULT 0,
    usertipo       VARCHAR(10) NOT NULL DEFAULT 'CLI'
);

CREATE TABLE roles (
    rolescod   VARCHAR(20) PRIMARY KEY,
    rolesdsc   VARCHAR(100) NOT NULL,
    rolesest   VARCHAR(10) NOT NULL DEFAULT 'ACT'
);

CREATE TABLE roles_usuario (
    usercod       INT NOT NULL,
    rolescod      VARCHAR(20) NOT NULL,
    roleuserest   VARCHAR(10) NOT NULL DEFAULT 'ACT',
    roleuserfch   DATETIME NOT NULL,
    roleuserexp   DATETIME NOT NULL,
    PRIMARY KEY (usercod, rolescod),
    FOREIGN KEY (usercod) REFERENCES usuario(usercod),
    FOREIGN KEY (rolescod) REFERENCES roles(rolescod)
);

CREATE TABLE funciones (
    fncod   VARCHAR(80) PRIMARY KEY,
    fndsc   VARCHAR(150) NOT NULL,
    fnest   VARCHAR(10) NOT NULL DEFAULT 'ACT',
    fntyp   VARCHAR(10) NOT NULL
);

CREATE TABLE funciones_roles (
    rolescod   VARCHAR(20) NOT NULL,
    fncod      VARCHAR(80) NOT NULL,
    fnrolest   VARCHAR(10) NOT NULL DEFAULT 'ACT',
    fnexp      DATETIME NOT NULL,
    PRIMARY KEY (rolescod, fncod),
    FOREIGN KEY (rolescod) REFERENCES roles(rolescod),
    FOREIGN KEY (fncod) REFERENCES funciones(fncod)
);

CREATE TABLE categoria (
    catcod   INT AUTO_INCREMENT PRIMARY KEY,
    catdsc   VARCHAR(80) NOT NULL,
    catest   VARCHAR(10) NOT NULL DEFAULT 'ACT'
);

CREATE TABLE producto (
    prodcod        INT AUTO_INCREMENT PRIMARY KEY,
    proddsc        VARCHAR(150) NOT NULL,
    proddet        VARCHAR(500) NULL,
    catcod         INT NOT NULL,
    prodprecio     DECIMAL(10,2) NOT NULL,
    prodstock      INT NOT NULL DEFAULT 0,
    prodimg        VARCHAR(255) NULL,
    prodest        VARCHAR(10) NOT NULL DEFAULT 'ACT',
    prodfching     DATETIME NOT NULL,
    FOREIGN KEY (catcod) REFERENCES categoria(catcod)
);

CREATE TABLE carrito (
    carritocod     INT AUTO_INCREMENT PRIMARY KEY,
    usercod        INT NOT NULL,
    carritoest     VARCHAR(10) NOT NULL DEFAULT 'ACT',
    fchcreacion    DATETIME NOT NULL,
    FOREIGN KEY (usercod) REFERENCES usuario(usercod)
);

CREATE TABLE carrito_item (
    carritocod       INT NOT NULL,
    prodcod          INT NOT NULL,
    cantidad         INT NOT NULL,
    precio_unitario  DECIMAL(10,2) NOT NULL,
    PRIMARY KEY (carritocod, prodcod),
    FOREIGN KEY (carritocod) REFERENCES carrito(carritocod),
    FOREIGN KEY (prodcod) REFERENCES producto(prodcod)
);

CREATE TABLE orden (
    ordencod    INT AUTO_INCREMENT PRIMARY KEY,
    usercod     INT NOT NULL,
    ordenfecha  DATETIME NOT NULL,
    ordentotal  DECIMAL(10,2) NOT NULL,
    ordenest    VARCHAR(15) NOT NULL DEFAULT 'PENDIENTE',
    FOREIGN KEY (usercod) REFERENCES usuario(usercod)
);

CREATE TABLE orden_item (
    ordencod         INT NOT NULL,
    prodcod          INT NOT NULL,
    proddsc          VARCHAR(150) NOT NULL,
    cantidad         INT NOT NULL,
    precio_unitario  DECIMAL(10,2) NOT NULL,
    subtotal         DECIMAL(10,2) NOT NULL,
    PRIMARY KEY (ordencod, prodcod),
    FOREIGN KEY (ordencod) REFERENCES orden(ordencod),
    FOREIGN KEY (prodcod) REFERENCES producto(prodcod)
);

CREATE TABLE transaccion (
    transcod     INT AUTO_INCREMENT PRIMARY KEY,
    ordencod     INT NOT NULL,
    transfecha   DATETIME NOT NULL,
    transmonto   DECIMAL(10,2) NOT NULL,
    transmetodo  VARCHAR(20) NOT NULL DEFAULT 'TARJETA',
    transest     VARCHAR(15) NOT NULL,
    transref     VARCHAR(30) NOT NULL,
    FOREIGN KEY (ordencod) REFERENCES orden(ordencod)
);

INSERT INTO roles (rolescod, rolesdsc, rolesest) VALUES
('ADMIN',   'Administrador', 'ACT'),
('CLIENTE', 'Cliente',       'ACT');

INSERT INTO funciones (fncod, fndsc, fnest, fntyp) VALUES
('Controllers\\Products\\Products', 'Lista de Productos',    'ACT', 'CTR'),
('Controllers\\Products\\Product',  'Formulario de Producto', 'ACT', 'CTR'),
('Controllers\\Checkout\\Checkout', 'Carretilla de Compra',   'ACT', 'CTR'),
('Controllers\\Orders\\Orders',     'Historial de Compras',   'ACT', 'CTR'),
('Controllers\\Orders\\Order',      'Detalle de Compra',      'ACT', 'CTR'),
('product_DSP', 'Ver Producto',      'ACT', 'FNC'),
('product_UPD', 'Editar Producto',   'ACT', 'FNC'),
('product_DEL', 'Eliminar Producto', 'ACT', 'FNC'),
('product_INS', 'Agregar Producto',  'ACT', 'FNC'),
('Menu_Products',        'Menu Catalogo',           'ACT', 'MNU'),
('Menu_PaymentCheckout', 'Menu Carretilla de Compra','ACT', 'MNU'),
('Menu_Orders',          'Menu Historial de Compras','ACT', 'MNU'),
('Menu_Logout',          'Menu Cerrar Sesion',      'ACT', 'MNU');

INSERT INTO funciones_roles (rolescod, fncod, fnrolest, fnexp) VALUES
('ADMIN', 'Controllers\\Products\\Products', 'ACT', DATE_ADD(NOW(), INTERVAL 1 YEAR)),
('ADMIN', 'Controllers\\Products\\Product',  'ACT', DATE_ADD(NOW(), INTERVAL 1 YEAR)),
('ADMIN', 'Controllers\\Checkout\\Checkout', 'ACT', DATE_ADD(NOW(), INTERVAL 1 YEAR)),
('ADMIN', 'Controllers\\Orders\\Orders',     'ACT', DATE_ADD(NOW(), INTERVAL 1 YEAR)),
('ADMIN', 'Controllers\\Orders\\Order',      'ACT', DATE_ADD(NOW(), INTERVAL 1 YEAR)),
('ADMIN', 'product_DSP', 'ACT', DATE_ADD(NOW(), INTERVAL 1 YEAR)),
('ADMIN', 'product_UPD', 'ACT', DATE_ADD(NOW(), INTERVAL 1 YEAR)),
('ADMIN', 'product_DEL', 'ACT', DATE_ADD(NOW(), INTERVAL 1 YEAR)),
('ADMIN', 'product_INS', 'ACT', DATE_ADD(NOW(), INTERVAL 1 YEAR)),
('ADMIN', 'Menu_Products',         'ACT', DATE_ADD(NOW(), INTERVAL 1 YEAR)),
('ADMIN', 'Menu_PaymentCheckout',  'ACT', DATE_ADD(NOW(), INTERVAL 1 YEAR)),
('ADMIN', 'Menu_Orders',           'ACT', DATE_ADD(NOW(), INTERVAL 1 YEAR)),
('ADMIN', 'Menu_Logout',           'ACT', DATE_ADD(NOW(), INTERVAL 1 YEAR));

INSERT INTO funciones_roles (rolescod, fncod, fnrolest, fnexp) VALUES
('CLIENTE', 'Controllers\\Products\\Products', 'ACT', DATE_ADD(NOW(), INTERVAL 1 YEAR)),
('CLIENTE', 'Controllers\\Products\\Product',  'ACT', DATE_ADD(NOW(), INTERVAL 1 YEAR)),
('CLIENTE', 'Controllers\\Checkout\\Checkout', 'ACT', DATE_ADD(NOW(), INTERVAL 1 YEAR)),
('CLIENTE', 'Controllers\\Orders\\Orders',     'ACT', DATE_ADD(NOW(), INTERVAL 1 YEAR)),
('CLIENTE', 'Controllers\\Orders\\Order',      'ACT', DATE_ADD(NOW(), INTERVAL 1 YEAR)),
('CLIENTE', 'product_DSP', 'ACT', DATE_ADD(NOW(), INTERVAL 1 YEAR)),
('CLIENTE', 'Menu_Products',        'ACT', DATE_ADD(NOW(), INTERVAL 1 YEAR)),
('CLIENTE', 'Menu_PaymentCheckout', 'ACT', DATE_ADD(NOW(), INTERVAL 1 YEAR)),
('CLIENTE', 'Menu_Orders',          'ACT', DATE_ADD(NOW(), INTERVAL 1 YEAR)),
('CLIENTE', 'Menu_Logout',          'ACT', DATE_ADD(NOW(), INTERVAL 1 YEAR));

INSERT INTO usuario (useremail, username, userpswd, userfching, userpswdest, userpswdexp, userest, useractcod, userpswdchg, usertipo) VALUES
('admin@variedadeslopsi.com', 'Administrador', '$2y$10$YSboPovdHi3hgU/RYneFoepd5NB3pYBs1/lp9Ne1pz7SDGx1l/WX6', NOW(), 'ACT', DATE_ADD(NOW(), INTERVAL 1 YEAR), 'ACT', NULL, 0, 'ADM'),
('cliente@variedadeslopsi.com', 'Cliente Demo', '$2y$10$uRP942WqSapu2OZuZvmm9eE0hXGL6l89TIVTuL5480eFLbnF/2KUS', NOW(), 'ACT', DATE_ADD(NOW(), INTERVAL 1 YEAR), 'ACT', NULL, 0, 'CLI');

INSERT INTO roles_usuario (usercod, rolescod, roleuserest, roleuserfch, roleuserexp) VALUES
(1, 'ADMIN',   'ACT', NOW(), DATE_ADD(NOW(), INTERVAL 1 YEAR)),
(2, 'CLIENTE', 'ACT', NOW(), DATE_ADD(NOW(), INTERVAL 1 YEAR));

INSERT INTO categoria (catdsc, catest) VALUES
('Telefonia', 'ACT'),
('Accesorios', 'ACT'),
('Audio', 'ACT'),
('Computacion', 'ACT');

INSERT INTO producto (proddsc, proddet, catcod, prodprecio, prodstock, prodest, prodfching) VALUES
('Samsung Galaxy A15',       'Telefono inteligente 128GB, 4GB RAM',              1, 6499.00, 10, 'ACT', NOW()),
('Xiaomi Redmi 13C',         'Telefono inteligente 256GB, 6GB RAM',              1, 5299.00, 15, 'ACT', NOW()),
('iPhone 12 (reacondicionado)', 'Telefono inteligente 64GB',                     1, 9800.00, 5,  'ACT', NOW()),
('Cargador tipo C 20W',      'Cargador rapido de pared con cable',               2, 289.00,  40, 'ACT', NOW()),
('Cable USB-C 1m',           'Cable de datos y carga',                          2, 129.00,  60, 'ACT', NOW()),
('Forro protector universal','Forro de silicona para telefono',                  2, 149.00,  50, 'ACT', NOW()),
('Vidrio templado',          'Protector de pantalla 9H',                         2, 99.00,   80, 'ACT', NOW()),
('Audifonos Bluetooth TWS',  'Audifonos inalambricos con estuche de carga',      3, 649.00,  20, 'ACT', NOW()),
('Bocina portatil Bluetooth','Bocina inalambrica resistente a salpicaduras',     3, 899.00,  12, 'ACT', NOW()),
('Power bank 10000mAh',      'Bateria portatil de respaldo',                     2, 599.00,  18, 'ACT', NOW()),
('Mouse inalambrico',        'Mouse optico inalambrico USB',                     4, 259.00,  25, 'ACT', NOW()),
('Memoria USB 32GB',         'Memoria flash USB 3.0',                            4, 189.00,  30, 'ACT', NOW());

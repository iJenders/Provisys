DROP DATABASE IF EXISTS provisys;

CREATE DATABASE provisys;

USE provisys;

-- CREAR ESTRUCTURAS DE TABLAS

CREATE TABLE credenciales (
	id_usuario VARCHAR(255) NOT NULL,
	`password` VARCHAR(255) NOT NULL,
	verificado TINYINT NOT NULL,
	PRIMARY KEY (id_usuario),
	UNIQUE (id_usuario)
);

CREATE TABLE usuarios (
	id_usuario VARCHAR(255) NOT NULL,
	fecha_registro DATETIME NOT NULL,
	nombres VARCHAR(46) NOT NULL,
	apellidos VARCHAR(46) NOT NULL,
	correo_electronico VARCHAR(255) NOT NULL,
	numero_celular VARCHAR(16) NOT NULL,
	numero_celular_secundario VARCHAR(16),
	direccion VARCHAR(255),
	id_rol INT NOT NULL,
    PRIMARY KEY (id_usuario),
    UNIQUE (id_usuario)
);

CREATE TABLE roles (
    id_rol INT NOT NULL AUTO_INCREMENT,
    nombre varchar(24) NOT NULL,
    descripcion varchar(255) NOT NULL,
    PRIMARY KEY (id_rol),
    UNIQUE (id_rol),
    UNIQUE (nombre)
);

CREATE TABLE permisos_del_rol ( 
    id_rol INT NOT NULL,
    id_permiso INT NOT NULL,
    PRIMARY KEY (id_rol, id_permiso)
);

CREATE TABLE permisos (
    id_permiso INT NOT NULL AUTO_INCREMENT,
    nombre varchar(24) NOT NULL,
    descripcion varchar(255) NOT NULL,
    PRIMARY KEY (id_permiso),
    UNIQUE (id_permiso)
);

CREATE TABLE pedidos(
    id_pedido INT NOT NULL AUTO_INCREMENT,
    fecha_pedido DATETIME NOT NULL,
    estado INT NOT NULL,
    id_usuario VARCHAR(255) NOT NULL,
    PRIMARY KEY (id_pedido)
);

CREATE TABLE ventas(
    id_venta INT NOT NULL AUTO_INCREMENT,
    monto DOUBLE NOT NULL,
    verificado TINYINT not null default 0,
    id_pedido INT NOT NULL,
    id_metodo INT NOT NULL,
    PRIMARY KEY (id_venta)
);

CREATE TABLE metodos_de_pago(
    id_metodo INT NOT NULL AUTO_INCREMENT,
    nombre varchar(24) NOT NULL,
    referencia varchar(255) NOT NULL,
    PRIMARY KEY (id_metodo)
);

CREATE TABLE productos(
    id_producto VARCHAR(255) NOT NULL,
    nombre VARCHAR(45) NOT NULL,
    descripcion VARCHAR(255),
    precio DOUBLE NOT NULL,
    id_afiliado VARCHAR(255) NOT NULL,
    id_categoria INT NOT NULL,
    PRIMARY KEY (id_producto),
    UNIQUE (id_producto)
);

CREATE TABLE detalles_pedido(
    id_pedido INT NOT NULL,
    id_producto VARCHAR(255) NOT NULL,
    cantidad INT NOT NULL,
    precio_de_venta DOUBLE NOT NULL,
    PRIMARY KEY (id_pedido, id_producto)
);

CREATE TABLE categorias_producto(
    id_categoria INT NOT NULL AUTO_INCREMENT,
    nombre varchar(45) NOT NULL,
    descripcion varchar(255) NOT NULL,
    PRIMARY KEY (id_categoria),
    UNIQUE (id_categoria)
);

CREATE TABLE afiliados(
    id_afiliado VARCHAR(255) NOT NULL,
    nombre VARCHAR(128) NOT NULL,
    telefono VARCHAR(16) NOT NULL,
    correo_electronico VARCHAR(255) NOT NULL,
    direccion VARCHAR(255) NOT NULL,
    es_proveeedor TINYINT NOT NULL,
    PRIMARY KEY (id_afiliado),
    UNIQUE (id_afiliado)
);

CREATE TABLE notas_de_venta(
    id_nota INT NOT NULL AUTO_INCREMENT,
    fecha DATETIME NOT NULL,
    descripcion VARCHAR(255) NOT NULL,
    id_afiliado VARCHAR(255) NOT NULL,
    PRIMARY KEY (id_nota),
    UNIQUE (id_nota)
);

CREATE TABLE productos_por_nota(
    id_nota INT NOT NULL,
    id_producto VARCHAR(255) NOT NULL,
    cantidad INT NOT NULL,
    precio_de_venta DOUBLE NOT NULL,
    PRIMARY KEY (id_nota, id_producto)
);

CREATE TABLE almacenes(
    id_almacen INT NOT NULL AUTO_INCREMENT,
    nombre VARCHAR(45) NOT NULL,
    descripcion VARCHAR(255) NOT NULL,
    PRIMARY KEY (id_almacen),
    UNIQUE (id_almacen)
);

CREATE TABLE productos_en_almacen(
    id_almacen INT NOT NULL,
    id_producto VARCHAR(255) NOT NULL,
    cantidad INT NOT NULL,
    PRIMARY KEY (id_almacen, id_producto)
);

CREATE TABLE es_almacenable(
    id_producto VARCHAR(255) NOT NULL,
    id_almacen INT NOT NULL,
    PRIMARY KEY (id_producto, id_almacen)
);

CREATE TABLE abastecimientos(
    id_abastecimiento INT NOT NULL AUTO_INCREMENT,
    fecha DATETIME NOT NULL,
    cantidad INT NOT NULL,
    id_producto VARCHAR(255) NOT NULL,
    id_almacen INT NOT NULL,
    precio_de_compra DOUBLE NOT NULL,
    PRIMARY KEY (id_abastecimiento)
);

CREATE TABLE perdidas(
    id_perdida INT NOT NULL AUTO_INCREMENT,
    fecha DATETIME NOT NULL,
    cantidad INT NOT NULL,
    descripcion VARCHAR(255) NOT NULL,
    id_producto VARCHAR(255) NOT NULL,
    id_almacen INT NOT NULL,
    PRIMARY KEY (id_perdida)
);



-- CREAR LAS RELACIONES ENTRE TABLAS

ALTER TABLE credenciales ADD FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario);

ALTER TABLE usuarios ADD FOREIGN KEY (id_rol) REFERENCES roles(id_rol);

ALTER TABLE permisos_del_rol
    ADD FOREIGN KEY (id_rol) REFERENCES roles(id_rol),
    ADD FOREIGN KEY (id_permiso) REFERENCES permisos(id_permiso);

ALTER TABLE pedidos ADD FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario);

ALTER TABLE ventas
    ADD FOREIGN KEY (id_pedido) REFERENCES pedidos(id_pedido),
    ADD FOREIGN KEY (id_metodo) REFERENCES metodos_de_pago(id_metodo);

ALTER TABLE detalles_pedido
    ADD FOREIGN KEY (id_pedido) REFERENCES pedidos(id_pedido),
    ADD FOREIGN KEY (id_producto) REFERENCES productos(id_producto);

ALTER TABLE productos
    ADD FOREIGN KEY (id_categoria) REFERENCES categorias_producto(id_categoria),
    ADD FOREIGN KEY (id_afiliado) REFERENCES afiliados(id_afiliado);

ALTER TABLE notas_de_venta ADD FOREIGN KEY (id_afiliado) REFERENCES afiliados(id_afiliado);

ALTER TABLE productos_por_nota
    ADD FOREIGN KEY (id_nota) REFERENCES notas_de_venta(id_nota),
    ADD FOREIGN KEY (id_producto) REFERENCES productos(id_producto);

ALTER TABLE productos_en_almacen
    ADD FOREIGN KEY (id_almacen) REFERENCES almacenes(id_almacen),
    ADD FOREIGN KEY (id_producto) REFERENCES productos(id_producto);

ALTER TABLE es_almacenable
    ADD FOREIGN KEY (id_producto) REFERENCES productos(id_producto),
    ADD FOREIGN KEY (id_almacen) REFERENCES almacenes(id_almacen);

ALTER TABLE abastecimientos
    ADD FOREIGN KEY (id_producto) REFERENCES productos(id_producto),
    ADD FOREIGN KEY (id_almacen) REFERENCES almacenes(id_almacen);

ALTER TABLE perdidas
    ADD FOREIGN KEY (id_producto) REFERENCES productos(id_producto),
    ADD FOREIGN KEY (id_almacen) REFERENCES almacenes(id_almacen);



-- INSERTAR ROLES Y PERMISOS
INSERT INTO roles (nombre, descripcion) VALUES
('administrador', 'Rol de administrador');

INSERT INTO permisos (nombre, descripcion) VALUES
('ver_usuarios', 'Ver usuarios'),
('crear_usuarios', 'Crear usuarios'),
('editar_usuarios', 'Editar usuarios'),
('eliminar_usuarios', 'Eliminar usuarios'),
('ver_productos', 'Ver productos'),
('crear_productos', 'Crear productos'),
('editar_productos', 'Editar productos'),
('eliminar_productos', 'Eliminar productos'),
('ver_pedidos', 'Ver pedidos'),
('crear_pedidos', 'Crear pedidos'),
('editar_pedidos', 'Editar pedidos'),
('eliminar_pedidos', 'Eliminar pedidos'),
('ver_ventas', 'Ver ventas'),
('crear_ventas', 'Crear ventas'),
('editar_ventas', 'Editar ventas'),
('eliminar_ventas', 'Eliminar ventas');

INSERT INTO permisos_del_rol (id_rol, id_permiso) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 4),
(1, 5),
(1, 6),
(1, 7),
(1, 8),
(1, 9),
(1, 10),
(1, 11),
(1, 12),
(1, 13),
(1, 14),
(1, 15),
(1, 16);

-- INSERTAR METODOS DE PAGO
INSERT INTO metodos_de_pago (nombre, referencia) VALUES
('Efectivo', 'Efectivo'),
('Tarjeta de credito', 'Tarjeta de credito'),
('Transferencia bancaria', 'Transferencia bancaria'),
('Cheque', 'Cheque'),
('Pago movil', 'Pago movil');

-- INSERTAR USUARIO ADMIN
INSERT INTO usuarios (id_usuario, fecha_registro, nombres, apellidos, correo_electronico, numero_celular, numero_celular_secundario, direccion, id_rol) VALUES
('admin', '2023-01-01 00:00:00', 'Administrador', 'Sistema', 'admin@gmail.com', '1234567890', NULL, 'Calle 123, Ciudad', 1);

INSERT INTO credenciales (id_usuario, password, verificado) VALUES
('admin', 'fcfb2eab6d0467833fa37e2db9bce36ee7a566d97db118d407798210bc8adaf5', 1);
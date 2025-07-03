-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.0.30 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for provisys
CREATE DATABASE IF NOT EXISTS `provisys` /*!40100 DEFAULT CHARACTER SET utf8mb3 */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `provisys`;

-- Dumping structure for table provisys.almacen
CREATE TABLE IF NOT EXISTS `almacen` (
  `id_almacen` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(45) NOT NULL,
  `descripcion_almacen` varchar(255) NOT NULL,
  `eliminado` tinyint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_almacen`),
  UNIQUE KEY `id_almacen_UNIQUE` (`id_almacen`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb3;

-- Dumping data for table provisys.almacen: ~2 rows (approximately)
INSERT INTO `almacen` (`id_almacen`, `nombre`, `descripcion_almacen`, `eliminado`) VALUES
	(5, 'Almacén 1', '...', 0),
	(6, 'Almacén 2', '...', 0),
	(7, 'Almacén 3', 'aaaa', 0);

-- Dumping structure for table provisys.categoria_producto
CREATE TABLE IF NOT EXISTS `categoria_producto` (
  `id_categoria` int unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(45) NOT NULL,
  `descripcion` varchar(255) NOT NULL,
  `eliminado` tinyint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_categoria`),
  UNIQUE KEY `id_categoria_UNIQUE` (`id_categoria`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb3;

-- Dumping data for table provisys.categoria_producto: ~0 rows (approximately)
INSERT INTO `categoria_producto` (`id_categoria`, `nombre`, `descripcion`, `eliminado`) VALUES
	(19, 'Snacks', '...', 0),
	(20, 'Waifu', 'Waifus', 0);

-- Dumping structure for table provisys.compra
CREATE TABLE IF NOT EXISTS `compra` (
  `id_compra` int NOT NULL AUTO_INCREMENT,
  `fecha_compra` datetime NOT NULL,
  `id_proveedor` varchar(24) NOT NULL,
  PRIMARY KEY (`id_compra`),
  KEY `fk_compra_proveedor1_idx` (`id_proveedor`),
  CONSTRAINT `fk_compra_proveedor1` FOREIGN KEY (`id_proveedor`) REFERENCES `proveedor` (`id_proveedor`)
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8mb3;

-- Dumping data for table provisys.compra: ~2 rows (approximately)
INSERT INTO `compra` (`id_compra`, `fecha_compra`, `id_proveedor`) VALUES
	(43, '2025-06-18 00:00:00', 'V-07000075456'),
	(44, '2025-06-18 00:00:00', 'V-07000075456'),
	(45, '2025-06-19 00:00:00', 'V-07000075456');

-- Dumping structure for table provisys.credencial
CREATE TABLE IF NOT EXISTS `credencial` (
  `nombre_usuario` varchar(24) NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`nombre_usuario`),
  UNIQUE KEY `nombre_usuario_UNIQUE` (`nombre_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Dumping data for table provisys.credencial: ~4 rows (approximately)
INSERT INTO `credencial` (`nombre_usuario`, `password`) VALUES
	('admin', '$2y$12$AHvjtxJbZcggWPAIzE9s7eapwQPWd12bXgrOIAe4Srae6h0l1GVUO'),
	('facturador', '$2y$12$yw1v8ue5PbKeW2jmTOA9rehwgCTli2gzju9Z0OfD31xJKQ2D4ErEW'),
	('Facturador2', '$2y$12$QBz57V9saNANLtYy4rWONefGChPZ6jFskGw4AGCoo1oMM00WR1GqS'),
	('vanvan', '$2y$12$P4zYrLcNXCXPCthyH.4JweVfDU8pj40lUvkgDMuhkalg.55jJSusu');

-- Dumping structure for table provisys.cuota
CREATE TABLE IF NOT EXISTS `cuota` (
  `id_cuota` int unsigned NOT NULL AUTO_INCREMENT,
  `fecha_cuota` datetime NOT NULL,
  `monto` decimal(12,2) NOT NULL,
  `nro_referencia` varchar(255) NOT NULL,
  `verificado` tinyint NOT NULL DEFAULT '0',
  `eliminado` tinyint NOT NULL DEFAULT '0',
  `id_metodo` int unsigned NOT NULL,
  `id_pago` int unsigned NOT NULL,
  PRIMARY KEY (`id_cuota`),
  UNIQUE KEY `id_cuota_UNIQUE` (`id_cuota`),
  KEY `fk_cuota_pago1_idx` (`id_pago`),
  KEY `fk_cuota_metodo_de_pago1_idx` (`id_metodo`),
  CONSTRAINT `fk_cuota_metodo_de_pago1` FOREIGN KEY (`id_metodo`) REFERENCES `metodo_de_pago` (`id_metodo`),
  CONSTRAINT `fk_cuota_pago1` FOREIGN KEY (`id_pago`) REFERENCES `pago` (`id_pago`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb3;

-- Dumping data for table provisys.cuota: ~6 rows (approximately)
INSERT INTO `cuota` (`id_cuota`, `fecha_cuota`, `monto`, `nro_referencia`, `verificado`, `eliminado`, `id_metodo`, `id_pago`) VALUES
	(26, '2025-06-18 00:00:00', 5.00, '0000000001', 1, 0, 1, 48),
	(27, '2025-06-18 00:00:00', 0.80, '00000001', 1, 0, 3, 48),
	(28, '2025-06-18 00:00:00', 3480.00, 'asdasdasd', 1, 0, 1, 51),
	(32, '2025-06-19 00:00:00', 5000.00, '00000012578654', 1, 0, 1, 54),
	(33, '2025-06-11 00:00:00', 29.00, '454654', 1, 0, 2, 53),
	(34, '2025-06-30 00:00:00', 100.00, 'sdasdasd', 1, 0, 2, 55);

-- Dumping structure for table provisys.desperdicio
CREATE TABLE IF NOT EXISTS `desperdicio` (
  `id_desperdicio` int unsigned NOT NULL AUTO_INCREMENT,
  `fecha` datetime NOT NULL,
  `motivo` varchar(255) NOT NULL DEFAULT '',
  `cantidad_producto` int unsigned NOT NULL,
  `precio_producto` decimal(12,2) NOT NULL,
  `iva_producto` decimal(12,2) NOT NULL,
  `id_producto` varchar(24) NOT NULL,
  `id_almacen` int NOT NULL,
  `eliminado` tinyint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_desperdicio`,`id_producto`,`id_almacen`),
  UNIQUE KEY `id_desperdicio_UNIQUE` (`id_desperdicio`),
  KEY `fk_desperdicio_productos_en_almacen1_idx` (`id_producto`,`id_almacen`),
  CONSTRAINT `fk_desperdicio_productos_en_almacen1` FOREIGN KEY (`id_producto`, `id_almacen`) REFERENCES `productos_en_almacen` (`id_producto`, `id_almacen`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb3;

-- Dumping data for table provisys.desperdicio: ~2 rows (approximately)
INSERT INTO `desperdicio` (`id_desperdicio`, `fecha`, `motivo`, `cantidad_producto`, `precio_producto`, `iva_producto`, `id_producto`, `id_almacen`, `eliminado`) VALUES
	(1, '2025-06-21 00:00:00', 'xd', 5, 5.00, 16.00, '0000777', 5, 1),
	(3, '2025-06-20 00:00:00', 'asdasd', 20, 7.50, 0.00, '000000001', 6, 1);

-- Dumping structure for table provisys.detalles_compra
CREATE TABLE IF NOT EXISTS `detalles_compra` (
  `id_producto` varchar(24) NOT NULL,
  `id_compra` int NOT NULL,
  `cantidad_producto` int unsigned NOT NULL,
  `precio_de_compra` decimal(12,2) NOT NULL,
  `iva_de_compra` decimal(12,2) NOT NULL,
  `id_almacen` int NOT NULL,
  PRIMARY KEY (`id_producto`,`id_compra`),
  KEY `fk_detalles_compra_compra1_idx` (`id_compra`),
  KEY `FK_detalles_compra_productos_en_almacen_2` (`id_almacen`),
  CONSTRAINT `fk_detalles_compra_compra1` FOREIGN KEY (`id_compra`) REFERENCES `compra` (`id_compra`) ON DELETE CASCADE,
  CONSTRAINT `FK_detalles_compra_productos_en_almacen` FOREIGN KEY (`id_producto`) REFERENCES `productos_en_almacen` (`id_producto`),
  CONSTRAINT `FK_detalles_compra_productos_en_almacen_2` FOREIGN KEY (`id_almacen`) REFERENCES `productos_en_almacen` (`id_almacen`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Dumping data for table provisys.detalles_compra: ~2 rows (approximately)
INSERT INTO `detalles_compra` (`id_producto`, `id_compra`, `cantidad_producto`, `precio_de_compra`, `iva_de_compra`, `id_almacen`) VALUES
	('000000001', 45, 1000, 5.00, 0.00, 6),
	('0000777', 43, 5, 1.00, 16.00, 5),
	('0000777', 44, 1000, 3.00, 16.00, 5);

-- Dumping structure for table provisys.detalles_pedido
CREATE TABLE IF NOT EXISTS `detalles_pedido` (
  `id_pedido` int unsigned NOT NULL,
  `id_producto` varchar(255) NOT NULL,
  `cantidad_producto` int unsigned NOT NULL,
  `precio_de_venta` decimal(12,2) NOT NULL,
  `iva_de_venta` decimal(12,2) NOT NULL,
  PRIMARY KEY (`id_pedido`,`id_producto`),
  KEY `fk_pedidos_has_productos_productos1_idx` (`id_producto`),
  KEY `fk_pedidos_has_productos_pedidos1_idx` (`id_pedido`),
  CONSTRAINT `fk_pedidos_has_productos_pedidos1` FOREIGN KEY (`id_pedido`) REFERENCES `pedido` (`id_pedido`),
  CONSTRAINT `fk_pedidos_has_productos_productos1` FOREIGN KEY (`id_producto`) REFERENCES `producto` (`id_producto`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Dumping data for table provisys.detalles_pedido: ~5 rows (approximately)
INSERT INTO `detalles_pedido` (`id_pedido`, `id_producto`, `cantidad_producto`, `precio_de_venta`, `iva_de_venta`) VALUES
	(5, '0000777', 5, 5.00, 16.00),
	(6, '0000777', 5, 5.00, 16.00),
	(7, '0000777', 5, 5.00, 16.00),
	(8, '000000001', 5, 7.50, 0.00),
	(8, '0000777', 20, 5.00, 16.00);

-- Dumping structure for table provisys.fabricante
CREATE TABLE IF NOT EXISTS `fabricante` (
  `id_fabricante` varchar(24) NOT NULL,
  `nombre` varchar(128) NOT NULL,
  `telefono` varchar(16) NOT NULL,
  `telefono_secundario` varchar(16) DEFAULT NULL,
  `correo` varchar(256) NOT NULL,
  `direccion` varchar(255) NOT NULL,
  `eliminado` tinyint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_fabricante`),
  UNIQUE KEY `id_proveedor_UNIQUE` (`id_fabricante`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Dumping data for table provisys.fabricante: ~0 rows (approximately)
INSERT INTO `fabricante` (`id_fabricante`, `nombre`, `telefono`, `telefono_secundario`, `correo`, `direccion`, `eliminado`) VALUES
	('V-0000000001', 'Coca-cola', '+584125555555', '+584125555555', 'coca@cola.com', '...', 0),
	('V-31271802', 'Monitas Chinas', '+584125372035', '', 'jende@jende.jende', '...', 0);

-- Dumping structure for table provisys.iva
CREATE TABLE IF NOT EXISTS `iva` (
  `id_iva` int unsigned NOT NULL AUTO_INCREMENT,
  `nombre_iva` varchar(45) NOT NULL,
  `iva` decimal(12,2) NOT NULL,
  `eliminado` tinyint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_iva`),
  UNIQUE KEY `id_iva_UNIQUE` (`id_iva`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb3;

-- Dumping data for table provisys.iva: ~5 rows (approximately)
INSERT INTO `iva` (`id_iva`, `nombre_iva`, `iva`, `eliminado`) VALUES
	(1, 'Alícuota General', 16.00, 0),
	(2, 'Alícuota Reducida', 8.00, 0),
	(3, 'Excento', 0.00, 0),
	(4, 'Alícuota Aumentada', 16.50, 0),
	(5, 'Impuesto a carlos', 100.00, 0);

-- Dumping structure for table provisys.metodo_de_pago
CREATE TABLE IF NOT EXISTS `metodo_de_pago` (
  `id_metodo` int unsigned NOT NULL AUTO_INCREMENT,
  `nombre_metodo` varchar(45) NOT NULL,
  `descripcion` varchar(255) NOT NULL DEFAULT '',
  `eliminado` tinyint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_metodo`),
  UNIQUE KEY `id_metodo_UNIQUE` (`id_metodo`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb3;

-- Dumping data for table provisys.metodo_de_pago: ~5 rows (approximately)
INSERT INTO `metodo_de_pago` (`id_metodo`, `nombre_metodo`, `descripcion`, `eliminado`) VALUES
	(1, 'Pago Móvil', 'Transacciones de transferencia por Pago Móvil', 0),
	(2, 'Transferencia Bancaria', 'Transferencia Bancaria', 0),
	(3, 'Efectivo', 'Pago en Efectivo. Previo contacto con los encargados.', 0),
	(4, 'Punto de Venta', 'Pago por tarjeta. Previo contacto con los encargados.', 0),
	(5, 'Paypal', 'Transferencia cuenta Paypal', 0),
	(6, 'Pago irregular', '...', 1);

-- Dumping structure for table provisys.pago
CREATE TABLE IF NOT EXISTS `pago` (
  `id_pago` int unsigned NOT NULL AUTO_INCREMENT,
  `fecha_pago` datetime NOT NULL,
  `monto_total` decimal(12,2) NOT NULL,
  `id_pedido` int unsigned DEFAULT NULL,
  `id_compra` int DEFAULT NULL,
  PRIMARY KEY (`id_pago`),
  UNIQUE KEY `id_venta_UNIQUE` (`id_pago`),
  KEY `fk_pago_pedido1_idx` (`id_pedido`),
  KEY `fk_pago_compra1_idx` (`id_compra`),
  CONSTRAINT `fk_pago_compra1` FOREIGN KEY (`id_compra`) REFERENCES `compra` (`id_compra`) ON DELETE CASCADE,
  CONSTRAINT `fk_pago_pedido1` FOREIGN KEY (`id_pedido`) REFERENCES `pedido` (`id_pedido`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=56 DEFAULT CHARSET=utf8mb3;

-- Dumping data for table provisys.pago: ~7 rows (approximately)
INSERT INTO `pago` (`id_pago`, `fecha_pago`, `monto_total`, `id_pedido`, `id_compra`) VALUES
	(48, '2025-06-18 00:00:00', 5.80, NULL, 43),
	(50, '2025-06-18 20:34:11', 29.00, 5, NULL),
	(51, '2025-06-18 00:00:00', 3480.00, NULL, 44),
	(52, '2025-06-18 20:38:56', 29.00, 6, NULL),
	(53, '2025-06-18 20:39:19', 29.00, 7, NULL),
	(54, '2025-06-19 00:00:00', 5000.00, NULL, 45),
	(55, '2025-06-19 02:39:34', 153.50, 8, NULL);

-- Dumping structure for table provisys.pedido
CREATE TABLE IF NOT EXISTS `pedido` (
  `id_pedido` int unsigned NOT NULL AUTO_INCREMENT,
  `fecha_pedido` datetime NOT NULL,
  `estado` int NOT NULL,
  `nombre_usuario` varchar(255) NOT NULL,
  PRIMARY KEY (`id_pedido`),
  UNIQUE KEY `id_pedido_UNIQUE` (`id_pedido`),
  KEY `fk_pedidos_usuario1_idx` (`nombre_usuario`),
  CONSTRAINT `fk_pedidos_usuario1` FOREIGN KEY (`nombre_usuario`) REFERENCES `usuario` (`nombre_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb3;

-- Dumping data for table provisys.pedido: ~3 rows (approximately)
INSERT INTO `pedido` (`id_pedido`, `fecha_pedido`, `estado`, `nombre_usuario`) VALUES
	(5, '2025-06-18 20:27:22', 3, 'vanvan'),
	(6, '2025-06-18 20:38:56', 3, 'vanvan'),
	(7, '2025-06-18 20:39:19', 3, 'vanvan'),
	(8, '2025-06-19 02:39:34', 0, 'vanvan');

-- Dumping structure for table provisys.permiso
CREATE TABLE IF NOT EXISTS `permiso` (
  `id_permiso` int unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(32) NOT NULL,
  `descripcion` varchar(255) NOT NULL,
  `id_permiso_padre` int unsigned DEFAULT NULL,
  PRIMARY KEY (`id_permiso`),
  UNIQUE KEY `id_permiso_UNIQUE` (`id_permiso`),
  UNIQUE KEY `nombre_UNIQUE` (`nombre`),
  KEY `FK_permiso_permiso` (`id_permiso_padre`),
  CONSTRAINT `FK_permiso_permiso` FOREIGN KEY (`id_permiso_padre`) REFERENCES `permiso` (`id_permiso`)
) ENGINE=InnoDB AUTO_INCREMENT=80 DEFAULT CHARSET=utf8mb3;

-- Dumping data for table provisys.permiso: ~79 rows (approximately)
INSERT INTO `permiso` (`id_permiso`, `nombre`, `descripcion`, `id_permiso_padre`) VALUES
	(1, 'categories', 'Gestión de categorías', NULL),
	(2, 'get_category', 'Ver categorías', 1),
	(3, 'create_category', 'Crear categoría', 1),
	(4, 'edit_category', 'Editar categoría', 1),
	(5, 'delete_category', 'Eliminar categoría', 1),
	(6, 'manufacturers', 'Gestión de fabricantes', NULL),
	(7, 'get_manufacturer', 'Ver fabricantes', 6),
	(8, 'create_manufacturer', 'Crear fabricante', 6),
	(9, 'update_manufacturer', 'Actualizar fabricante', 6),
	(10, 'delete_manufacturer', 'Eliminar fabricante', 6),
	(11, 'ivas', 'Gestión de tipos de IVA', NULL),
	(12, 'get_iva', 'Ver tipos de IVA', 11),
	(13, 'create_iva', 'Crear tipo de IVA', 11),
	(14, 'update_iva', 'Actualizar tipo de IVA', 11),
	(15, 'delete_iva', 'Eliminar tipo de IVA', 11),
	(16, 'storages', 'Gestión de almacenes', NULL),
	(17, 'get_storage', 'Ver almacenes', 16),
	(18, 'create_storage', 'Crear almacén', 16),
	(19, 'update_storage', 'Actualizar almacén', 16),
	(20, 'delete_storage', 'Eliminar almacén', 16),
	(21, 'products', 'Gestión de productos', NULL),
	(22, 'get_product', 'Ver productos', 21),
	(23, 'create_product', 'Crear producto', 21),
	(24, 'update_product', 'Actualizar producto', 21),
	(25, 'delete_product', 'Eliminar producto', 21),
	(26, 'payment_methods', 'Gestión de métodos de pago', NULL),
	(27, 'get_payment_method', 'Ver métodos de pago', 26),
	(28, 'create_payment_method', 'Crear método de pago', 26),
	(29, 'update_payment_method', 'Actualizar método de pago', 26),
	(30, 'delete_payment_method', 'Eliminar método de pago', 26),
	(31, 'payments', 'Gestión de pagos', NULL),
	(32, 'get_payment', 'Ver pagos', 31),
	(33, 'roles', 'Gestión de roles', NULL),
	(34, 'get_role', 'Ver roles', 33),
	(35, 'create_role', 'Crear rol', 33),
	(36, 'edit_role', 'Editar rol', 33),
	(37, 'delete_role', 'Eliminar rol', 33),
	(38, 'permissions', 'Gestión de permisos', NULL),
	(39, 'get_permission', 'Ver permisos', 38),
	(40, 'users', 'Gestión de usuarios', NULL),
	(41, 'get_user', 'Ver usuarios', 40),
	(42, 'create_user', 'Crear usuario', 40),
	(43, 'update_user', 'Actualizar usuario', 40),
	(44, 'delete_user', 'Eliminar usuario', 40),
	(45, 'providers', 'Gestión de proveedores', NULL),
	(46, 'get_provider', 'Ver proveedores', 45),
	(47, 'create_provider', 'Crear proveedor', 45),
	(48, 'update_provider', 'Actualizar proveedor', 45),
	(49, 'delete_provider', 'Eliminar proveedor', 45),
	(50, 'clients', 'Gestión de clientes', NULL),
	(51, 'get_client', 'Ver clientes', 50),
	(52, 'create_client', 'Crear cliente', 50),
	(53, 'update_client', 'Actualizar cliente', 50),
	(54, 'delete_client', 'Eliminar cliente', 50),
	(55, 'employees', 'Gestión de empleados', NULL),
	(56, 'get_employee', 'Ver empleados', 55),
	(57, 'create_employee', 'Crear empleado', 55),
	(58, 'update_employee', 'Actualizar empleado', 55),
	(59, 'delete_employee', 'Eliminar empleado', 55),
	(60, 'orders', 'Gestión de órdenes', NULL),
	(61, 'get_order', 'Ver órdenes', 60),
	(62, 'create_order', 'Crear orden', 60),
	(63, 'update_order', 'Actualizar orden', 60),
	(64, 'delete_order', 'Eliminar orden', 60),
	(65, 'ingresses', 'Gestión de ingresos', NULL),
	(66, 'get_ingress', 'Ver ingresos', 65),
	(67, 'create_ingress', 'Crear ingreso', 65),
	(68, 'delete_ingress', 'Eliminar ingreso', 65),
	(69, 'reports', 'Gestión de reportes', NULL),
	(70, 'get_report', 'Ver reportes', 69),
	(71, 'purchases', 'Gestión de Compras', NULL),
	(72, 'get_purchase', 'Ver Compras', 71),
	(73, 'create_purchase', 'Crear Compras', 71),
	(74, 'delete_purchase', 'Eliminar Compras', 71),
	(75, 'update_purchase', 'Editar Compras', 71),
	(76, 'wastes', 'Gestión de pérdidas', NULL),
	(77, 'get_waste', 'Ver pérdidas', 76),
	(78, 'create_waste', 'Crear Pérdidas', 76),
	(79, 'delete_waste', 'Eliminar Pérdidas', 76);

-- Dumping structure for table provisys.permisos_de_rol
CREATE TABLE IF NOT EXISTS `permisos_de_rol` (
  `id_rol` int unsigned NOT NULL,
  `id_permiso` int unsigned NOT NULL,
  PRIMARY KEY (`id_rol`,`id_permiso`),
  KEY `fk_roles_has_permisos_permisos1_idx` (`id_permiso`),
  KEY `fk_roles_has_permisos_roles1_idx` (`id_rol`),
  CONSTRAINT `fk_roles_has_permisos_permisos1` FOREIGN KEY (`id_permiso`) REFERENCES `permiso` (`id_permiso`),
  CONSTRAINT `fk_roles_has_permisos_roles1` FOREIGN KEY (`id_rol`) REFERENCES `rol` (`id_rol`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Dumping data for table provisys.permisos_de_rol: ~85 rows (approximately)
INSERT INTO `permisos_de_rol` (`id_rol`, `id_permiso`) VALUES
	(1, 1),
	(1, 2),
	(3, 2),
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
	(1, 16),
	(1, 17),
	(1, 18),
	(1, 19),
	(1, 20),
	(1, 21),
	(1, 22),
	(1, 23),
	(1, 24),
	(1, 25),
	(1, 26),
	(1, 27),
	(1, 28),
	(1, 29),
	(1, 30),
	(1, 31),
	(1, 32),
	(1, 33),
	(1, 34),
	(1, 35),
	(1, 36),
	(1, 37),
	(1, 38),
	(1, 39),
	(1, 40),
	(1, 41),
	(1, 42),
	(1, 43),
	(1, 44),
	(1, 45),
	(1, 46),
	(1, 47),
	(1, 48),
	(1, 49),
	(1, 50),
	(1, 51),
	(1, 52),
	(1, 53),
	(1, 54),
	(1, 55),
	(1, 56),
	(1, 57),
	(1, 58),
	(1, 59),
	(1, 60),
	(1, 61),
	(1, 62),
	(1, 63),
	(1, 64),
	(1, 65),
	(1, 66),
	(1, 67),
	(1, 68),
	(1, 69),
	(1, 70),
	(1, 71),
	(1, 72),
	(1, 73),
	(1, 74),
	(1, 75),
	(1, 76),
	(1, 77),
	(1, 78),
	(1, 79);

-- Dumping structure for table provisys.producto
CREATE TABLE IF NOT EXISTS `producto` (
  `id_producto` varchar(24) NOT NULL,
  `nombre` varchar(45) NOT NULL,
  `descripcion_producto` varchar(255) NOT NULL,
  `precio` decimal(12,2) NOT NULL,
  `id_categoria` int unsigned NOT NULL,
  `id_iva` int unsigned NOT NULL,
  `id_fabricante` varchar(24) NOT NULL,
  `eliminado` tinyint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_producto`),
  UNIQUE KEY `id_producto_UNIQUE` (`id_producto`),
  KEY `fk_productos_categorias_producto1_idx` (`id_categoria`),
  KEY `fk_productos_iva1_idx` (`id_iva`),
  KEY `fk_producto_proveedor1_idx` (`id_fabricante`),
  CONSTRAINT `fk_producto_proveedor1` FOREIGN KEY (`id_fabricante`) REFERENCES `fabricante` (`id_fabricante`),
  CONSTRAINT `fk_productos_categorias_producto1` FOREIGN KEY (`id_categoria`) REFERENCES `categoria_producto` (`id_categoria`),
  CONSTRAINT `fk_productos_iva1` FOREIGN KEY (`id_iva`) REFERENCES `iva` (`id_iva`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Dumping data for table provisys.producto: ~0 rows (approximately)
INSERT INTO `producto` (`id_producto`, `nombre`, `descripcion_producto`, `precio`, `id_categoria`, `id_iva`, `id_fabricante`, `eliminado`) VALUES
	('000000001', 'Juguito de Navia', 'Juguito sabor a Navia', 7.50, 20, 3, 'V-31271802', 0),
	('0000777', 'Galleta', 'galleta oreo', 5.00, 19, 1, 'V-0000000001', 0);

-- Dumping structure for table provisys.productos_en_almacen
CREATE TABLE IF NOT EXISTS `productos_en_almacen` (
  `id_producto` varchar(24) NOT NULL,
  `id_almacen` int NOT NULL,
  `stock_disponible` int(10) unsigned zerofill NOT NULL DEFAULT '0000000000',
  `stock_reservado` int(10) unsigned zerofill NOT NULL DEFAULT '0000000000',
  `eliminado` tinyint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_producto`,`id_almacen`),
  UNIQUE KEY `id_producto` (`id_producto`),
  KEY `fk_productos_has_almacenes_almacenes1_idx` (`id_almacen`),
  KEY `fk_productos_has_almacenes_productos1_idx` (`id_producto`),
  CONSTRAINT `fk_productos_has_almacenes_almacenes1` FOREIGN KEY (`id_almacen`) REFERENCES `almacen` (`id_almacen`),
  CONSTRAINT `fk_productos_has_almacenes_productos1` FOREIGN KEY (`id_producto`) REFERENCES `producto` (`id_producto`),
  CONSTRAINT `stocks` CHECK ((`stock_reservado` <= `stock_disponible`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Dumping data for table provisys.productos_en_almacen: ~0 rows (approximately)
INSERT INTO `productos_en_almacen` (`id_producto`, `id_almacen`, `stock_disponible`, `stock_reservado`, `eliminado`) VALUES
	('000000001', 6, 0000001000, 0000000005, 0),
	('0000777', 5, 0000001005, 0000000020, 0);

-- Dumping structure for table provisys.proveedor
CREATE TABLE IF NOT EXISTS `proveedor` (
  `id_proveedor` varchar(24) NOT NULL,
  `nombre` varchar(128) NOT NULL,
  `telefono` varchar(16) NOT NULL,
  `telefono_secundario` varchar(16) DEFAULT NULL,
  `correo` varchar(256) NOT NULL,
  `direccion` varchar(255) NOT NULL,
  `eliminado` tinyint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_proveedor`),
  UNIQUE KEY `id_proveedor_UNIQUE` (`id_proveedor`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Dumping data for table provisys.proveedor: ~0 rows (approximately)
INSERT INTO `proveedor` (`id_proveedor`, `nombre`, `telefono`, `telefono_secundario`, `correo`, `direccion`, `eliminado`) VALUES
	('V-07000075456', 'Proveedor 1', '+5844444444', NULL, 'contacto@empresa.com', '...', 0);

-- Dumping structure for table provisys.rol
CREATE TABLE IF NOT EXISTS `rol` (
  `id_rol` int unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(32) NOT NULL,
  `descripcion` varchar(255) NOT NULL,
  `eliminado` tinyint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_rol`),
  UNIQUE KEY `id_rol_UNIQUE` (`id_rol`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb3;

-- Dumping data for table provisys.rol: ~3 rows (approximately)
INSERT INTO `rol` (`id_rol`, `nombre`, `descripcion`, `eliminado`) VALUES
	(1, 'Administrador', 'Rol con acceso total al sistema', 0),
	(2, 'Cliente', 'Rol para clientes que pueden realizar pedidos y pagos', 0),
	(3, 'Facturador', 'Facturador para pruebas', 0);

-- Dumping structure for procedure provisys.rutina_agregar_pago_compra
DELIMITER //
CREATE PROCEDURE `rutina_agregar_pago_compra`(
	IN `id_compra` INT,
	IN `monto` DECIMAL(12,2),
	IN `fecha_cuota` DATETIME,
	IN `nro_referencia` VARCHAR(255),
	IN `id_metodo` INT
)
BEGIN
	DECLARE id_pago_arg INT;
	
	SELECT p.id_pago INTO id_pago_arg
		FROM pago p INNER JOIN compra c ON p.id_compra=c.id_compra
		WHERE c.id_compra = id_compra
		GROUP BY p.id_pago
		LIMIT 1;
	
	
	INSERT INTO cuota (fecha_cuota, monto, nro_referencia, verificado, id_pago, id_metodo)
		VALUES (fecha_cuota, monto, nro_referencia, 1, id_pago_arg, id_metodo);
END//
DELIMITER ;

-- Dumping structure for table provisys.usuario
CREATE TABLE IF NOT EXISTS `usuario` (
  `nombre_usuario` varchar(24) NOT NULL,
  `fecha_registro` datetime NOT NULL,
  `nombres` varchar(46) NOT NULL,
  `apellidos` varchar(46) NOT NULL,
  `correo` varchar(256) NOT NULL,
  `telefono` varchar(16) NOT NULL,
  `telefono_secundario` varchar(16) DEFAULT NULL,
  `direccion` varchar(255) NOT NULL,
  `verificado` tinyint DEFAULT '0',
  `id_rol` int unsigned NOT NULL,
  PRIMARY KEY (`nombre_usuario`),
  UNIQUE KEY `correo_electrónico_UNIQUE` (`correo`),
  KEY `fk_usuarios_roles1_idx` (`id_rol`),
  KEY `fk_usuarios_credencial1_idx` (`nombre_usuario`),
  CONSTRAINT `fk_usuarios_credencial1` FOREIGN KEY (`nombre_usuario`) REFERENCES `credencial` (`nombre_usuario`),
  CONSTRAINT `fk_usuarios_roles1` FOREIGN KEY (`id_rol`) REFERENCES `rol` (`id_rol`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Dumping data for table provisys.usuario: ~4 rows (approximately)
INSERT INTO `usuario` (`nombre_usuario`, `fecha_registro`, `nombres`, `apellidos`, `correo`, `telefono`, `telefono_secundario`, `direccion`, `verificado`, `id_rol`) VALUES
	('admin', '2025-06-11 19:01:47', 'Carlos', 'User', 'admin@example.com', '1234567890', '+584125372035', '123 Main St', 1, 1),
	('facturador', '2025-06-25 02:19:23', 'qwe', 'qwe', 'qwe@qwe.qwe', '+584125555555', '+584125555555', 'qwe', 1, 3),
	('Facturador2', '2025-06-25 02:45:38', 'qweqwewq', 'qwe', 'qwe@qwe.qwee', '+584125555555', '', 'qwe', 1, 3),
	('vanvan', '2025-06-18 17:23:50', 'Van', 'De Abarca', 'van@van.van', '+584123456789', '+584125372035', 'Vía láctea', 1, 2);

-- Dumping structure for view provisys.vista_obtener_compras
-- Creating temporary table to overcome VIEW dependency errors
CREATE TABLE `vista_obtener_compras` (
	`id_compra` INT(10) NOT NULL,
	`fecha_compra` DATETIME NOT NULL,
	`nombre` VARCHAR(128) NOT NULL COLLATE 'utf8mb3_general_ci',
	`cantidadProductos` DECIMAL(32,0) NULL,
	`total` DECIMAL(61,8) NULL,
	`pago_verificado` INT(10) NOT NULL,
	`contacto` VARCHAR(16) NOT NULL COLLATE 'utf8mb3_general_ci'
) ENGINE=MyISAM;

-- Dumping structure for view provisys.vista_obtener_pagos
-- Creating temporary table to overcome VIEW dependency errors
CREATE TABLE `vista_obtener_pagos` (
	`id_pago` INT(10) UNSIGNED NOT NULL,
	`fecha_pago` DATETIME NOT NULL,
	`monto_total` DECIMAL(12,2) NOT NULL,
	`id_pedido` INT(10) UNSIGNED NULL,
	`id_compra` INT(10) NULL,
	`total_pagado` DECIMAL(34,2) NULL,
	`verificados` INT(10) NULL,
	`por_pagar` INT(10) NULL
) ENGINE=MyISAM;

-- Dumping structure for view provisys.vista_obtener_pagos_compra
-- Creating temporary table to overcome VIEW dependency errors
CREATE TABLE `vista_obtener_pagos_compra` (
	`id` INT(10) UNSIGNED NOT NULL,
	`purchaseID` INT(10) NOT NULL,
	`date` DATETIME NOT NULL,
	`amount` DECIMAL(12,2) NOT NULL,
	`reference` VARCHAR(255) NOT NULL COLLATE 'utf8mb3_general_ci',
	`verified` TINYINT(3) NOT NULL,
	`methodId` INT(10) UNSIGNED NOT NULL,
	`methodName` VARCHAR(45) NOT NULL COLLATE 'utf8mb3_general_ci',
	`methodDescription` VARCHAR(255) NOT NULL COLLATE 'utf8mb3_general_ci'
) ENGINE=MyISAM;

-- Dumping structure for view provisys.vista_obtener_pedidos_clientes
-- Creating temporary table to overcome VIEW dependency errors
CREATE TABLE `vista_obtener_pedidos_clientes` (
	`nombre_usuario` VARCHAR(255) NOT NULL COLLATE 'utf8mb3_general_ci',
	`id_pedido` INT(10) UNSIGNED NOT NULL,
	`estado` INT(10) NOT NULL,
	`fecha_pedido` DATETIME NOT NULL,
	`total_productos` DECIMAL(32,0) NULL,
	`valor` DECIMAL(61,8) NULL,
	`id_pago` INT(10) UNSIGNED NOT NULL,
	`total_pagado` DECIMAL(34,2) NULL,
	`verificados` INT(10) NULL,
	`por_pagar` INT(10) NULL
) ENGINE=MyISAM;

-- Dumping structure for view provisys.vista_obtener_productos_compra
-- Creating temporary table to overcome VIEW dependency errors
CREATE TABLE `vista_obtener_productos_compra` (
	`id_compra` INT(10) NOT NULL,
	`id` VARCHAR(24) NOT NULL COLLATE 'utf8mb3_general_ci',
	`name` VARCHAR(45) NOT NULL COLLATE 'utf8mb3_general_ci',
	`description` VARCHAR(255) NOT NULL COLLATE 'utf8mb3_general_ci',
	`price` DECIMAL(12,2) NOT NULL,
	`quantity` INT(10) UNSIGNED NOT NULL,
	`iva` DECIMAL(12,2) NOT NULL
) ENGINE=MyISAM;

-- Dumping structure for view provisys.vista_obtener_productos_pedido
-- Creating temporary table to overcome VIEW dependency errors
CREATE TABLE `vista_obtener_productos_pedido` (
	`id_pedido` INT(10) UNSIGNED NOT NULL,
	`id_producto` VARCHAR(24) NOT NULL COLLATE 'utf8mb3_general_ci',
	`nombre_producto` VARCHAR(45) NOT NULL COLLATE 'utf8mb3_general_ci',
	`descripcion_producto` VARCHAR(255) NOT NULL COLLATE 'utf8mb3_general_ci',
	`id_categoria` INT(10) UNSIGNED NOT NULL,
	`nombre_categoria` VARCHAR(45) NOT NULL COLLATE 'utf8mb3_general_ci',
	`id_fabricante` VARCHAR(24) NOT NULL COLLATE 'utf8mb3_general_ci',
	`nombre_fabricante` VARCHAR(128) NOT NULL COLLATE 'utf8mb3_general_ci',
	`cantidad_producto` INT(10) UNSIGNED NOT NULL,
	`precio_de_venta` DECIMAL(12,2) NOT NULL,
	`iva_de_venta` DECIMAL(12,2) NOT NULL
) ENGINE=MyISAM;

-- Dumping structure for view provisys.vista_obtener_productos_tienda
-- Creating temporary table to overcome VIEW dependency errors
CREATE TABLE `vista_obtener_productos_tienda` (
	`id` VARCHAR(24) NOT NULL COLLATE 'utf8mb3_general_ci',
	`nombre` VARCHAR(45) NOT NULL COLLATE 'utf8mb3_general_ci',
	`descripcion_producto` VARCHAR(255) NOT NULL COLLATE 'utf8mb3_general_ci',
	`precio` DECIMAL(12,2) NOT NULL,
	`categoria` VARCHAR(45) NOT NULL COLLATE 'utf8mb3_general_ci',
	`iva` INT(10) UNSIGNED NOT NULL,
	`fabricante` VARCHAR(128) NOT NULL COLLATE 'utf8mb3_general_ci',
	`stock_disponible` DECIMAL(32,0) NULL,
	`stock_reservado` DECIMAL(32,0) NULL,
	`stock` DECIMAL(33,0) NULL
) ENGINE=MyISAM;

-- Dumping structure for view provisys.vista_obtener_proveedor_compra
-- Creating temporary table to overcome VIEW dependency errors
CREATE TABLE `vista_obtener_proveedor_compra` (
	`id_compra` INT(10) NOT NULL,
	`id_proveedor` VARCHAR(24) NOT NULL COLLATE 'utf8mb3_general_ci',
	`nombre` VARCHAR(128) NOT NULL COLLATE 'utf8mb3_general_ci',
	`telefono` VARCHAR(16) NOT NULL COLLATE 'utf8mb3_general_ci',
	`telefono_secundario` VARCHAR(16) NULL COLLATE 'utf8mb3_general_ci',
	`correo` VARCHAR(256) NOT NULL COLLATE 'utf8mb3_general_ci',
	`direccion` VARCHAR(255) NOT NULL COLLATE 'utf8mb3_general_ci',
	`eliminado` TINYINT(3) NOT NULL
) ENGINE=MyISAM;

-- Dumping structure for view provisys.vista_reporte_estado_inventario
-- Creating temporary table to overcome VIEW dependency errors
CREATE TABLE `vista_reporte_estado_inventario` (
	`id` VARCHAR(24) NOT NULL COLLATE 'utf8mb3_general_ci',
	`nombre` VARCHAR(45) NOT NULL COLLATE 'utf8mb3_general_ci',
	`precio` DECIMAL(12,2) NOT NULL,
	`iva` DECIMAL(12,2) NOT NULL,
	`stock` INT(10) UNSIGNED ZEROFILL NOT NULL,
	`subtotal` DECIMAL(39,8) NULL
) ENGINE=MyISAM;

-- Dumping structure for view provisys.vista_total_pagado
-- Creating temporary table to overcome VIEW dependency errors
CREATE TABLE `vista_total_pagado` (
	`id_pago` INT(10) UNSIGNED NOT NULL,
	`monto_pagado` DECIMAL(34,2) NULL,
	`monto_total` DECIMAL(12,2) NOT NULL
) ENGINE=MyISAM;

-- Dumping structure for view provisys.vista_obtener_compras
-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `vista_obtener_compras`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `vista_obtener_compras` AS with `pagos_verificados` as (select `p`.`id_pago` AS `id_pago`,`p`.`id_compra` AS `id_compra`,(case when ((count(`c`.`id_pago`) > 0) and (sum((case when (`c`.`verificado` = 1) then 1 else 0 end)) = count(`c`.`id_pago`)) and (sum(`c`.`monto`) >= `p`.`monto_total`)) then 1 else 0 end) AS `pago_verificado` from (`pago` `p` left join `cuota` `c` on((`p`.`id_pago` = `c`.`id_pago`))) group by `p`.`id_pago`,`p`.`monto_total`,`p`.`id_compra`) select `c`.`id_compra` AS `id_compra`,`c`.`fecha_compra` AS `fecha_compra`,`pro`.`nombre` AS `nombre`,sum(`dc`.`cantidad_producto`) AS `cantidadProductos`,sum(((`dc`.`cantidad_producto` * `dc`.`precio_de_compra`) + (((`dc`.`cantidad_producto` * `dc`.`precio_de_compra`) * `dc`.`iva_de_compra`) / 100))) AS `total`,`pv`.`pago_verificado` AS `pago_verificado`,`pro`.`telefono` AS `contacto` from ((((`compra` `c` join `detalles_compra` `dc` on((`c`.`id_compra` = `dc`.`id_compra`))) join `proveedor` `pro` on((`pro`.`id_proveedor` = `c`.`id_proveedor`))) join `producto` `p` on((`p`.`id_producto` = `dc`.`id_producto`))) join `pagos_verificados` `pv` on((`pv`.`id_compra` = `c`.`id_compra`))) group by `c`.`id_compra`,`c`.`fecha_compra`,`pro`.`nombre`,`pv`.`pago_verificado`;

-- Dumping structure for view provisys.vista_obtener_pagos
-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `vista_obtener_pagos`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `vista_obtener_pagos` AS select `p`.`id_pago` AS `id_pago`,`p`.`fecha_pago` AS `fecha_pago`,`p`.`monto_total` AS `monto_total`,`p`.`id_pedido` AS `id_pedido`,`p`.`id_compra` AS `id_compra`,sum(`c`.`monto`) AS `total_pagado`,(sum(`c`.`verificado`) = count(`c`.`id_cuota`)) AS `verificados`,(sum(`c`.`monto`) < `p`.`monto_total`) AS `por_pagar` from (`pago` `p` left join `cuota` `c` on((`p`.`id_pago` = `c`.`id_pago`))) group by `p`.`id_pago` order by `verificados`,`por_pagar` desc,`p`.`fecha_pago` desc;

-- Dumping structure for view provisys.vista_obtener_pagos_compra
-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `vista_obtener_pagos_compra`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `vista_obtener_pagos_compra` AS select `c`.`id_cuota` AS `id`,`co`.`id_compra` AS `purchaseID`,`c`.`fecha_cuota` AS `date`,`c`.`monto` AS `amount`,`c`.`nro_referencia` AS `reference`,`c`.`verificado` AS `verified`,`m`.`id_metodo` AS `methodId`,`m`.`nombre_metodo` AS `methodName`,`m`.`descripcion` AS `methodDescription` from (((`cuota` `c` join `pago` `p` on((`c`.`id_pago` = `p`.`id_pago`))) join `compra` `co` on((`p`.`id_compra` = `co`.`id_compra`))) join `metodo_de_pago` `m` on((`c`.`id_metodo` = `m`.`id_metodo`)));

-- Dumping structure for view provisys.vista_obtener_pedidos_clientes
-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `vista_obtener_pedidos_clientes`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `vista_obtener_pedidos_clientes` AS with `pedido_general` as (select `pe`.`nombre_usuario` AS `nombre_usuario`,`pe`.`id_pedido` AS `id_pedido`,`pe`.`estado` AS `estado`,`pe`.`fecha_pedido` AS `fecha_pedido`,sum(`dp`.`cantidad_producto`) AS `total_productos`,sum(((`dp`.`cantidad_producto` * `dp`.`precio_de_venta`) * (1 + (`dp`.`iva_de_venta` / 100)))) AS `valor` from (`pedido` `pe` join `detalles_pedido` `dp` on((`pe`.`id_pedido` = `dp`.`id_pedido`))) group by `pe`.`id_pedido`) select `pg`.`nombre_usuario` AS `nombre_usuario`,`pg`.`id_pedido` AS `id_pedido`,`pg`.`estado` AS `estado`,`pg`.`fecha_pedido` AS `fecha_pedido`,`pg`.`total_productos` AS `total_productos`,`pg`.`valor` AS `valor`,`vop`.`id_pago` AS `id_pago`,`vop`.`total_pagado` AS `total_pagado`,`vop`.`verificados` AS `verificados`,`vop`.`por_pagar` AS `por_pagar` from (`pedido_general` `pg` join `vista_obtener_pagos` `vop` on((`pg`.`id_pedido` = `vop`.`id_pedido`)));

-- Dumping structure for view provisys.vista_obtener_productos_compra
-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `vista_obtener_productos_compra`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `vista_obtener_productos_compra` AS select `dc`.`id_compra` AS `id_compra`,`p`.`id_producto` AS `id`,`p`.`nombre` AS `name`,`p`.`descripcion_producto` AS `description`,`dc`.`precio_de_compra` AS `price`,`dc`.`cantidad_producto` AS `quantity`,`dc`.`iva_de_compra` AS `iva` from (`detalles_compra` `dc` join `producto` `p` on((`p`.`id_producto` = `dc`.`id_producto`)));

-- Dumping structure for view provisys.vista_obtener_productos_pedido
-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `vista_obtener_productos_pedido`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `vista_obtener_productos_pedido` AS with `producto_hidratado` as (select `p`.`id_producto` AS `id_producto`,`p`.`nombre` AS `nombre_producto`,`p`.`descripcion_producto` AS `descripcion_producto`,`p`.`id_categoria` AS `id_categoria`,`c`.`nombre` AS `nombre_categoria`,`p`.`id_fabricante` AS `id_fabricante`,`f`.`nombre` AS `nombre_fabricante` from (((`producto` `p` join `iva` on((`p`.`id_iva` = `iva`.`id_iva`))) join `fabricante` `f` on((`p`.`id_fabricante` = `f`.`id_fabricante`))) join `categoria_producto` `c` on((`p`.`id_categoria` = `c`.`id_categoria`))) group by `p`.`id_producto`) select `pe`.`id_pedido` AS `id_pedido`,`ph`.`id_producto` AS `id_producto`,`ph`.`nombre_producto` AS `nombre_producto`,`ph`.`descripcion_producto` AS `descripcion_producto`,`ph`.`id_categoria` AS `id_categoria`,`ph`.`nombre_categoria` AS `nombre_categoria`,`ph`.`id_fabricante` AS `id_fabricante`,`ph`.`nombre_fabricante` AS `nombre_fabricante`,`dp`.`cantidad_producto` AS `cantidad_producto`,`dp`.`precio_de_venta` AS `precio_de_venta`,`dp`.`iva_de_venta` AS `iva_de_venta` from ((`pedido` `pe` join `detalles_pedido` `dp` on((`pe`.`id_pedido` = `dp`.`id_pedido`))) join `producto_hidratado` `ph` on((`ph`.`id_producto` = `dp`.`id_producto`)));

-- Dumping structure for view provisys.vista_obtener_productos_tienda
-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `vista_obtener_productos_tienda`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `vista_obtener_productos_tienda` AS select `p`.`id_producto` AS `id`,`p`.`nombre` AS `nombre`,`p`.`descripcion_producto` AS `descripcion_producto`,`p`.`precio` AS `precio`,`c`.`nombre` AS `categoria`,`i`.`id_iva` AS `iva`,`f`.`nombre` AS `fabricante`,sum(`pa`.`stock_disponible`) AS `stock_disponible`,sum(`pa`.`stock_reservado`) AS `stock_reservado`,(sum(`pa`.`stock_disponible`) - sum(`pa`.`stock_reservado`)) AS `stock` from ((((`producto` `p` join `categoria_producto` `c` on((`p`.`id_categoria` = `c`.`id_categoria`))) join `iva` `i` on((`p`.`id_iva` = `i`.`id_iva`))) join `fabricante` `f` on((`p`.`id_fabricante` = `f`.`id_fabricante`))) left join `productos_en_almacen` `pa` on((`p`.`id_producto` = `pa`.`id_producto`))) where (`p`.`eliminado` = 0) group by `p`.`id_producto`;

-- Dumping structure for view provisys.vista_obtener_proveedor_compra
-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `vista_obtener_proveedor_compra`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `vista_obtener_proveedor_compra` AS select `c`.`id_compra` AS `id_compra`,`p`.`id_proveedor` AS `id_proveedor`,`p`.`nombre` AS `nombre`,`p`.`telefono` AS `telefono`,`p`.`telefono_secundario` AS `telefono_secundario`,`p`.`correo` AS `correo`,`p`.`direccion` AS `direccion`,`p`.`eliminado` AS `eliminado` from (`proveedor` `p` join `compra` `c` on((`p`.`id_proveedor` = `c`.`id_proveedor`)));

-- Dumping structure for view provisys.vista_reporte_estado_inventario
-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `vista_reporte_estado_inventario`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `vista_reporte_estado_inventario` AS select `p`.`id_producto` AS `id`,`p`.`nombre` AS `nombre`,`p`.`precio` AS `precio`,`iva`.`iva` AS `iva`,`pa`.`stock_disponible` AS `stock`,((`p`.`precio` * `pa`.`stock_disponible`) * (1 + (`iva`.`iva` / 100))) AS `subtotal` from ((`producto` `p` join `productos_en_almacen` `pa` on((`p`.`id_producto` = `pa`.`id_producto`))) join `iva` on((`p`.`id_iva` = `iva`.`id_iva`))) where (`pa`.`stock_disponible` > 0) order by `pa`.`stock_disponible` desc;

-- Dumping structure for view provisys.vista_total_pagado
-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `vista_total_pagado`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `vista_total_pagado` AS select `p`.`id_pago` AS `id_pago`,sum(`c`.`monto`) AS `monto_pagado`,`p`.`monto_total` AS `monto_total` from ((`pago` `p` left join `cuota` `c` on((`p`.`id_pago` = `c`.`id_pago`))) join `compra` on((`compra`.`id_compra` = `p`.`id_compra`))) group by `p`.`monto_total`,`p`.`id_pago`;

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;

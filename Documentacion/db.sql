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
  `es_vehiculo` tinyint NOT NULL DEFAULT '0',
  `eliminado` tinyint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_almacen`),
  UNIQUE KEY `id_almacen_UNIQUE` (`id_almacen`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb3;

-- Data exporting was unselected.

-- Dumping structure for table provisys.categoria_producto
CREATE TABLE IF NOT EXISTS `categoria_producto` (
  `id_categoria` int unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(45) NOT NULL,
  `descripcion` varchar(255) NOT NULL,
  `eliminado` tinyint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_categoria`),
  UNIQUE KEY `id_categoria_UNIQUE` (`id_categoria`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb3;

-- Data exporting was unselected.

-- Dumping structure for table provisys.compra
CREATE TABLE IF NOT EXISTS `compra` (
  `id_compra` int NOT NULL AUTO_INCREMENT,
  `fecha_compra` datetime NOT NULL,
  `id_proveedor` varchar(24) NOT NULL,
  PRIMARY KEY (`id_compra`),
  KEY `fk_compra_proveedor1_idx` (`id_proveedor`),
  CONSTRAINT `fk_compra_proveedor1` FOREIGN KEY (`id_proveedor`) REFERENCES `proveedor` (`id_proveedor`)
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8mb3;

-- Data exporting was unselected.

-- Dumping structure for table provisys.credencial
CREATE TABLE IF NOT EXISTS `credencial` (
  `nombre_usuario` varchar(24) NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`nombre_usuario`),
  UNIQUE KEY `nombre_usuario_UNIQUE` (`nombre_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Data exporting was unselected.

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
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb3;

-- Data exporting was unselected.

-- Dumping structure for table provisys.desperdicio
CREATE TABLE IF NOT EXISTS `desperdicio` (
  `id_desperdicio` int unsigned NOT NULL,
  `fecha` datetime NOT NULL,
  `motivo` varchar(255) NOT NULL DEFAULT '',
  `cantidad_producto` int unsigned NOT NULL,
  `precio_producto` decimal(12,2) NOT NULL,
  `iva_producto` decimal(12,2) NOT NULL,
  `id_producto` varchar(24) NOT NULL,
  `id_almacen` int NOT NULL,
  PRIMARY KEY (`id_desperdicio`,`id_producto`,`id_almacen`),
  UNIQUE KEY `id_desperdicio_UNIQUE` (`id_desperdicio`),
  KEY `fk_desperdicio_productos_en_almacen1_idx` (`id_producto`,`id_almacen`),
  CONSTRAINT `fk_desperdicio_productos_en_almacen1` FOREIGN KEY (`id_producto`, `id_almacen`) REFERENCES `productos_en_almacen` (`id_producto`, `id_almacen`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Data exporting was unselected.

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

-- Data exporting was unselected.

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

-- Data exporting was unselected.

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

-- Data exporting was unselected.

-- Dumping structure for table provisys.iva
CREATE TABLE IF NOT EXISTS `iva` (
  `id_iva` int unsigned NOT NULL AUTO_INCREMENT,
  `nombre_iva` varchar(45) NOT NULL,
  `iva` decimal(12,2) NOT NULL,
  `eliminado` tinyint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_iva`),
  UNIQUE KEY `id_iva_UNIQUE` (`id_iva`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb3;

-- Data exporting was unselected.

-- Dumping structure for table provisys.metodo_de_pago
CREATE TABLE IF NOT EXISTS `metodo_de_pago` (
  `id_metodo` int unsigned NOT NULL AUTO_INCREMENT,
  `nombre_metodo` varchar(45) NOT NULL,
  `descripcion` varchar(255) NOT NULL DEFAULT '',
  `eliminado` tinyint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_metodo`),
  UNIQUE KEY `id_metodo_UNIQUE` (`id_metodo`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb3;

-- Data exporting was unselected.

-- Dumping structure for table provisys.movimiento_de_inventario
CREATE TABLE IF NOT EXISTS `movimiento_de_inventario` (
  `id_movimiento` int unsigned NOT NULL,
  `fecha` datetime NOT NULL,
  `motivo` varchar(255) NOT NULL DEFAULT '',
  `cantidad_producto` int unsigned NOT NULL,
  `id_producto` varchar(24) NOT NULL,
  `id_almacen1` int NOT NULL,
  `id_almacen2` int NOT NULL,
  PRIMARY KEY (`id_movimiento`),
  UNIQUE KEY `id_perdida_UNIQUE` (`id_movimiento`),
  KEY `fk_movimiento_de_inventario_productos_en_almacen1_idx` (`id_producto`,`id_almacen1`),
  KEY `fk_movimiento_de_inventario_productos_en_almacen2_idx` (`id_almacen2`),
  CONSTRAINT `fk_movimiento_de_inventario_productos_en_almacen1` FOREIGN KEY (`id_producto`, `id_almacen1`) REFERENCES `productos_en_almacen` (`id_producto`, `id_almacen`),
  CONSTRAINT `fk_movimiento_de_inventario_productos_en_almacen2` FOREIGN KEY (`id_almacen2`) REFERENCES `productos_en_almacen` (`id_almacen`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Data exporting was unselected.

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
) ENGINE=InnoDB AUTO_INCREMENT=47 DEFAULT CHARSET=utf8mb3;

-- Data exporting was unselected.

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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3;

-- Data exporting was unselected.

-- Dumping structure for table provisys.permiso
CREATE TABLE IF NOT EXISTS `permiso` (
  `id_permiso` int unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(32) NOT NULL,
  `descripcion` varchar(255) NOT NULL,
  PRIMARY KEY (`id_permiso`),
  UNIQUE KEY `id_permiso_UNIQUE` (`id_permiso`),
  UNIQUE KEY `nombre_UNIQUE` (`nombre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Data exporting was unselected.

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

-- Data exporting was unselected.

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

-- Data exporting was unselected.

-- Dumping structure for table provisys.productos_en_almacen
CREATE TABLE IF NOT EXISTS `productos_en_almacen` (
  `id_producto` varchar(24) NOT NULL,
  `id_almacen` int NOT NULL,
  `stock_disponible` int(10) unsigned zerofill NOT NULL DEFAULT '0000000000',
  `stock_reservado` int(10) unsigned zerofill NOT NULL DEFAULT '0000000000',
  `eliminado` tinyint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_producto`,`id_almacen`),
  KEY `fk_productos_has_almacenes_almacenes1_idx` (`id_almacen`),
  KEY `fk_productos_has_almacenes_productos1_idx` (`id_producto`),
  CONSTRAINT `fk_productos_has_almacenes_almacenes1` FOREIGN KEY (`id_almacen`) REFERENCES `almacen` (`id_almacen`),
  CONSTRAINT `fk_productos_has_almacenes_productos1` FOREIGN KEY (`id_producto`) REFERENCES `producto` (`id_producto`),
  CONSTRAINT `stocks` CHECK ((`stock_reservado` <= `stock_disponible`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Data exporting was unselected.

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

-- Data exporting was unselected.

-- Dumping structure for table provisys.rol
CREATE TABLE IF NOT EXISTS `rol` (
  `id_rol` int unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(32) NOT NULL,
  `descripcion` varchar(255) NOT NULL,
  `eliminado` tinyint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_rol`),
  UNIQUE KEY `id_rol_UNIQUE` (`id_rol`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3;

-- Data exporting was unselected.

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
  `id_rol` int unsigned NOT NULL,
  PRIMARY KEY (`nombre_usuario`),
  UNIQUE KEY `correo_electrónico_UNIQUE` (`correo`),
  KEY `fk_usuarios_roles1_idx` (`id_rol`),
  KEY `fk_usuarios_credencial1_idx` (`nombre_usuario`),
  CONSTRAINT `fk_usuarios_credencial1` FOREIGN KEY (`nombre_usuario`) REFERENCES `credencial` (`nombre_usuario`),
  CONSTRAINT `fk_usuarios_roles1` FOREIGN KEY (`id_rol`) REFERENCES `rol` (`id_rol`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Data exporting was unselected.

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
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `vista_obtener_pagos` AS select `p`.`id_pago` AS `id_pago`,`p`.`fecha_pago` AS `fecha_pago`,`p`.`monto_total` AS `monto_total`,`p`.`id_pedido` AS `id_pedido`,`p`.`id_compra` AS `id_compra`,sum(`c`.`monto`) AS `total_pagado`,(sum(`c`.`verificado`) = count(`c`.`id_cuota`)) AS `verificados`,(sum(`c`.`monto`) < `p`.`monto_total`) AS `por_pagar` from (`pago` `p` left join `cuota` `c` on((`p`.`id_pago` = `c`.`id_pago`))) group by `p`.`id_pago` order by `por_pagar` desc,`verificados`,`p`.`fecha_pago` desc;

-- Dumping structure for view provisys.vista_obtener_pagos_compra
-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `vista_obtener_pagos_compra`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `vista_obtener_pagos_compra` AS select `c`.`id_cuota` AS `id`,`co`.`id_compra` AS `purchaseID`,`c`.`fecha_cuota` AS `date`,`c`.`monto` AS `amount`,`c`.`nro_referencia` AS `reference`,`c`.`verificado` AS `verified`,`m`.`id_metodo` AS `methodId`,`m`.`nombre_metodo` AS `methodName`,`m`.`descripcion` AS `methodDescription` from (((`cuota` `c` join `pago` `p` on((`c`.`id_pago` = `p`.`id_pago`))) join `compra` `co` on((`p`.`id_compra` = `co`.`id_compra`))) join `metodo_de_pago` `m` on((`c`.`id_metodo` = `m`.`id_metodo`)));

-- Dumping structure for view provisys.vista_obtener_productos_compra
-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `vista_obtener_productos_compra`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `vista_obtener_productos_compra` AS select `dc`.`id_compra` AS `id_compra`,`p`.`id_producto` AS `id`,`p`.`nombre` AS `name`,`p`.`descripcion_producto` AS `description`,`dc`.`precio_de_compra` AS `price`,`dc`.`cantidad_producto` AS `quantity`,`dc`.`iva_de_compra` AS `iva` from (`detalles_compra` `dc` join `producto` `p` on((`p`.`id_producto` = `dc`.`id_producto`)));

-- Dumping structure for view provisys.vista_obtener_productos_tienda
-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `vista_obtener_productos_tienda`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `vista_obtener_productos_tienda` AS select `p`.`id_producto` AS `id`,`p`.`nombre` AS `nombre`,`p`.`descripcion_producto` AS `descripcion_producto`,`p`.`precio` AS `precio`,`c`.`nombre` AS `categoria`,`i`.`id_iva` AS `iva`,`f`.`nombre` AS `fabricante`,sum(`pa`.`stock_disponible`) AS `stock_disponible`,sum(`pa`.`stock_reservado`) AS `stock_reservado`,(sum(`pa`.`stock_disponible`) - sum(`pa`.`stock_reservado`)) AS `stock` from ((((`producto` `p` join `categoria_producto` `c` on((`p`.`id_categoria` = `c`.`id_categoria`))) join `iva` `i` on((`p`.`id_iva` = `i`.`id_iva`))) join `fabricante` `f` on((`p`.`id_fabricante` = `f`.`id_fabricante`))) left join `productos_en_almacen` `pa` on((`p`.`id_producto` = `pa`.`id_producto`))) where (`p`.`eliminado` = 0) group by `p`.`id_producto`;

-- Dumping structure for view provisys.vista_obtener_proveedor_compra
-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `vista_obtener_proveedor_compra`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `vista_obtener_proveedor_compra` AS select `c`.`id_compra` AS `id_compra`,`p`.`id_proveedor` AS `id_proveedor`,`p`.`nombre` AS `nombre`,`p`.`telefono` AS `telefono`,`p`.`telefono_secundario` AS `telefono_secundario`,`p`.`correo` AS `correo`,`p`.`direccion` AS `direccion`,`p`.`eliminado` AS `eliminado` from (`proveedor` `p` join `compra` `c` on((`p`.`id_proveedor` = `c`.`id_proveedor`)));

-- Dumping structure for view provisys.vista_total_pagado
-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `vista_total_pagado`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `vista_total_pagado` AS select `p`.`id_pago` AS `id_pago`,sum(`c`.`monto`) AS `monto_pagado`,`p`.`monto_total` AS `monto_total` from ((`pago` `p` left join `cuota` `c` on((`p`.`id_pago` = `c`.`id_pago`))) join `compra` on((`compra`.`id_compra` = `p`.`id_compra`))) group by `p`.`monto_total`,`p`.`id_pago`;

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;

-- -----------------------------------------------------
-- Data IMPORTANT
-- -----------------------------------------------------
START TRANSACTION;
USE `provisys`;
INSERT INTO
`provisys`.`rol` (`id_rol`, `nombre`, `descripcion`) VALUES
(1, 'Administrador', 'Rol con acceso total al sistema'),
(2, 'Cliente', 'Rol para clientes que pueden realizar pedidos y pagos');

INSERT INTO
`provisys`.`credencial` (`nombre_usuario`, `password`) VALUES
('admin', '$2y$12$DjO.8dBP0kIQsDfbzCa7luY8y./DKoDXRaNsn0pWKEKK4xn7sVf3G');

INSERT INTO
`provisys`.`usuario` (`nombre_usuario`, `fecha_registro`, `nombres`, `apellidos`, `correo`, `telefono`, `telefono_secundario`, `direccion`, `id_rol`) VALUES
('admin', NOW(), 'Admin', 'User', 'admin@example.com', '1234567890', '9876543210', '123 Main St', 1);

COMMIT;
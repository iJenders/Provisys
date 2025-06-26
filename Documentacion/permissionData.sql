-- Script para insertar permisos en la base de datos
-- Los permisos se organizan en una estructura de árbol, donde cada permiso puede tener un permiso padre.

-- Limpiar la tabla de permisos antes de insertar nuevos datos
DELETE FROM permiso;
ALTER TABLE permiso AUTO_INCREMENT = 1;

-- Permisos de Categorías
INSERT INTO permiso (nombre, descripcion, id_permiso_padre) VALUES ('categories', 'Gestión de categorías', NULL);
SET @last_id_permiso = LAST_INSERT_ID();
INSERT INTO permiso (nombre, descripcion, id_permiso_padre) VALUES 
('get_category', 'Ver categorías', @last_id_permiso),
('create_category', 'Crear categoría', @last_id_permiso),
('edit_category', 'Editar categoría', @last_id_permiso),
('delete_category', 'Eliminar categoría', @last_id_permiso);

-- Permisos de Fabricantes
INSERT INTO permiso (nombre, descripcion, id_permiso_padre) VALUES ('manufacturers', 'Gestión de fabricantes', NULL);
SET @last_id_permiso = LAST_INSERT_ID();
INSERT INTO permiso (nombre, descripcion, id_permiso_padre) VALUES 
('get_manufacturer', 'Ver fabricantes', @last_id_permiso),
('create_manufacturer', 'Crear fabricante', @last_id_permiso),
('update_manufacturer', 'Actualizar fabricante', @last_id_permiso),
('delete_manufacturer', 'Eliminar fabricante', @last_id_permiso);

-- Permisos de IVA
INSERT INTO permiso (nombre, descripcion, id_permiso_padre) VALUES ('ivas', 'Gestión de tipos de IVA', NULL);
SET @last_id_permiso = LAST_INSERT_ID();
INSERT INTO permiso (nombre, descripcion, id_permiso_padre) VALUES 
('get_iva', 'Ver tipos de IVA', @last_id_permiso),
('create_iva', 'Crear tipo de IVA', @last_id_permiso),
('update_iva', 'Actualizar tipo de IVA', @last_id_permiso),
('delete_iva', 'Eliminar tipo de IVA', @last_id_permiso);

-- Permisos de Almacenes
INSERT INTO permiso (nombre, descripcion, id_permiso_padre) VALUES ('storages', 'Gestión de almacenes', NULL);
SET @last_id_permiso = LAST_INSERT_ID();
INSERT INTO permiso (nombre, descripcion, id_permiso_padre) VALUES 
('get_storage', 'Ver almacenes', @last_id_permiso),
('create_storage', 'Crear almacén', @last_id_permiso),
('update_storage', 'Actualizar almacén', @last_id_permiso),
('delete_storage', 'Eliminar almacén', @last_id_permiso);

-- Permisos de Productos
INSERT INTO permiso (nombre, descripcion, id_permiso_padre) VALUES ('products', 'Gestión de productos', NULL);
SET @last_id_permiso = LAST_INSERT_ID();
INSERT INTO permiso (nombre, descripcion, id_permiso_padre) VALUES 
('get_product', 'Ver productos', @last_id_permiso),
('create_product', 'Crear producto', @last_id_permiso),
('update_product', 'Actualizar producto', @last_id_permiso),
('delete_product', 'Eliminar producto', @last_id_permiso);

-- Permisos de Métodos de Pago
INSERT INTO permiso (nombre, descripcion, id_permiso_padre) VALUES ('payment_methods', 'Gestión de métodos de pago', NULL);
SET @last_id_permiso = LAST_INSERT_ID();
INSERT INTO permiso (nombre, descripcion, id_permiso_padre) VALUES 
('get_payment_method', 'Ver métodos de pago', @last_id_permiso),
('create_payment_method', 'Crear método de pago', @last_id_permiso),
('update_payment_method', 'Actualizar método de pago', @last_id_permiso),
('delete_payment_method', 'Eliminar método de pago', @last_id_permiso);

-- Permisos de Pagos
INSERT INTO permiso (nombre, descripcion, id_permiso_padre) VALUES ('payments', 'Gestión de pagos', NULL);
SET @last_id_permiso = LAST_INSERT_ID();
INSERT INTO permiso (nombre, descripcion, id_permiso_padre) VALUES 
('get_payment', 'Ver pagos', @last_id_permiso);

-- Permisos de Roles
INSERT INTO permiso (nombre, descripcion, id_permiso_padre) VALUES ('roles', 'Gestión de roles', NULL);
SET @last_id_permiso = LAST_INSERT_ID();
INSERT INTO permiso (nombre, descripcion, id_permiso_padre) VALUES 
('get_role', 'Ver roles', @last_id_permiso),
('create_role', 'Crear rol', @last_id_permiso),
('edit_role', 'Editar rol', @last_id_permiso),
('delete_role', 'Eliminar rol', @last_id_permiso);

-- Permisos de Permisos
INSERT INTO permiso (nombre, descripcion, id_permiso_padre) VALUES ('permissions', 'Gestión de permisos', NULL);
SET @last_id_permiso = LAST_INSERT_ID();
INSERT INTO permiso (nombre, descripcion, id_permiso_padre) VALUES 
('get_permission', 'Ver permisos', @last_id_permiso);

-- Permisos de Usuarios
INSERT INTO permiso (nombre, descripcion, id_permiso_padre) VALUES ('users', 'Gestión de usuarios', NULL);
SET @last_id_permiso = LAST_INSERT_ID();
INSERT INTO permiso (nombre, descripcion, id_permiso_padre) VALUES 
('get_user', 'Ver usuarios', @last_id_permiso),
('create_user', 'Crear usuario', @last_id_permiso),
('update_user', 'Actualizar usuario', @last_id_permiso),
('delete_user', 'Eliminar usuario', @last_id_permiso);

-- Permisos de Proveedores
INSERT INTO permiso (nombre, descripcion, id_permiso_padre) VALUES ('providers', 'Gestión de proveedores', NULL);
SET @last_id_permiso = LAST_INSERT_ID();
INSERT INTO permiso (nombre, descripcion, id_permiso_padre) VALUES 
('get_provider', 'Ver proveedores', @last_id_permiso),
('create_provider', 'Crear proveedor', @last_id_permiso),
('update_provider', 'Actualizar proveedor', @last_id_permiso),
('delete_provider', 'Eliminar proveedor', @last_id_permiso);

-- Permisos de Clientes
INSERT INTO permiso (nombre, descripcion, id_permiso_padre) VALUES ('clients', 'Gestión de clientes', NULL);
SET @last_id_permiso = LAST_INSERT_ID();
INSERT INTO permiso (nombre, descripcion, id_permiso_padre) VALUES 
('get_client', 'Ver clientes', @last_id_permiso),
('create_client', 'Crear cliente', @last_id_permiso),
('update_client', 'Actualizar cliente', @last_id_permiso),
('delete_client', 'Eliminar cliente', @last_id_permiso);

-- Permisos de Empleados
INSERT INTO permiso (nombre, descripcion, id_permiso_padre) VALUES ('employees', 'Gestión de empleados', NULL);
SET @last_id_permiso = LAST_INSERT_ID();
INSERT INTO permiso (nombre, descripcion, id_permiso_padre) VALUES 
('get_employee', 'Ver empleados', @last_id_permiso),
('create_employee', 'Crear empleado', @last_id_permiso),
('update_employee', 'Actualizar empleado', @last_id_permiso),
('delete_employee', 'Eliminar empleado', @last_id_permiso);

-- Permisos de Órdenes
INSERT INTO permiso (nombre, descripcion, id_permiso_padre) VALUES ('orders', 'Gestión de órdenes', NULL);
SET @last_id_permiso = LAST_INSERT_ID();
INSERT INTO permiso (nombre, descripcion, id_permiso_padre) VALUES 
('get_order', 'Ver órdenes', @last_id_permiso),
('create_order', 'Crear orden', @last_id_permiso),
('update_order', 'Actualizar orden', @last_id_permiso),
('delete_order', 'Eliminar orden', @last_id_permiso);

-- Permisos de Ingresos
INSERT INTO permiso (nombre, descripcion, id_permiso_padre) VALUES ('ingresses', 'Gestión de ingresos', NULL);
SET @last_id_permiso = LAST_INSERT_ID();
INSERT INTO permiso (nombre, descripcion, id_permiso_padre) VALUES 
('get_ingress', 'Ver ingresos', @last_id_permiso),
('create_ingress', 'Crear ingreso', @last_id_permiso),
('delete_ingress', 'Eliminar ingreso', @last_id_permiso);

-- Permisos de Reportes
INSERT INTO permiso (nombre, descripcion, id_permiso_padre) VALUES ('reports', 'Gestión de reportes', NULL);
SET @last_id_permiso = LAST_INSERT_ID();
INSERT INTO permiso (nombre, descripcion, id_permiso_padre) VALUES 
('get_report', 'Ver reportes', @last_id_permiso);

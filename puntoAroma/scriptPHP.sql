CREATE TABLE banner(
    id_banner INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100),
    ruta VARCHAR(200),
    descripcion VARCHAR(200),
    id_pagina INT
);


CREATE TABLE categorias (
    id_categoria INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100),
    descripcion TEXT
);



-- Tabla de estado de productos
CREATE TABLE estados_productos (
    id_estado_producto INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100),
    descripcion TEXT
);

-- Tabla de estados de pedidos
CREATE TABLE estados_pedidos (
    id_estado_pedido INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100),
    descripcion TEXT
);

-- Tabla de permisos
CREATE TABLE permisos (
    id_permiso INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100),
    descripcion TEXT
);

-- Tabla de estado de usuario
CREATE TABLE estados_usuarios (
    id_estado_usuario INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100),
    descripcion TEXT
);

-- Tabla de sexos
CREATE TABLE sexos (
    id_sexo INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100),
    descripcion TEXT
);

-- Tabla de domicilios
CREATE TABLE domicilios (
    id_domicilio INT PRIMARY KEY AUTO_INCREMENT,
    codigo_postal VARCHAR(10),
    provincia VARCHAR(100),
    localidad VARCHAR(100),
    barrio VARCHAR(100),
    calle VARCHAR(100),
    numero VARCHAR(10)
);

-- Tabla de tipos de precios
CREATE TABLE tipos_precios (
    id_tipo_precio INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100),
    descripcion TEXT
);

-- Tabla de productos
CREATE TABLE productos (
    id_producto INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100),
    descripcion TEXT,
    id_categoria INT,
    destacado TINYINT DEFAULT 0,
    estado TINYINT DEFAULT 1,
    decuento DECIMAL(10, 2) DEFAULT 0,
    CONSTRAINT FK_productos_id_categoria_END FOREIGN KEY (id_categoria) REFERENCES categorias(id_categoria)
);

-- Tabla de usuarios
CREATE TABLE usuarios (
    id_usuario INT PRIMARY KEY AUTO_INCREMENT,
    id_permiso INT DEFAULT 2,
    id_estado_usuario INT DEFAULT 1,
    email VARCHAR(100),
    clave VARCHAR(100),
    CONSTRAINT FK_usuario_id_permiso_END FOREIGN KEY (id_permiso) REFERENCES permisos(id_permiso),
    CONSTRAINT FK_usuario_id_estado_usuario_END FOREIGN KEY (id_estado_usuario) REFERENCES estados_usuarios(id_estado_usuario)
);

-- Tabla de imágenes de productos
CREATE TABLE imagenes (
    id_imagen INT PRIMARY KEY AUTO_INCREMENT,
    id_producto INT,
    ruta TEXT,
    principal TINYINT DEFAULT 0,
    CONSTRAINT FK_imagenes_id_producto_END FOREIGN KEY (id_producto) REFERENCES productos(id_producto)
);

-- Tabla de información del usuario
CREATE TABLE info_usuarios (
    id_info_usuario INT PRIMARY KEY AUTO_INCREMENT,
    id_usuario INT,
    id_sexo INT,
    nombre VARCHAR(100),
    apellido VARCHAR(100),
    dni VARCHAR(20),
    fecha_nacimiento DATE,
    telefono VARCHAR(20),
    CONSTRAINT FK_info_usuario_id_usuario_END FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario),
    CONSTRAINT FK_info_usuario_id_sexo_END FOREIGN KEY (id_sexo) REFERENCES sexos(id_sexo)
);


-- metodos de pago
CREATE TABLE metodos_pago (
    id_metodo_pago INT PRIMARY KEY AUTO_INCREMENT,
    tipo VARCHAR(100)
);

-- Tabla de pedidos
CREATE TABLE pedidos (
    id_pedido INT PRIMARY KEY AUTO_INCREMENT,
    id_usuario INT,
    id_estado_pedido INT,
    total DECIMAL(10, 2),
    fecha DATE,
    id_domicilio INT,
    id_metodo_pago INT,
    CONSTRAINT FK_pedidos_id_usuario_END FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario),
    CONSTRAINT FK_pedidos_id_estado_pedido_END FOREIGN KEY (id_estado_pedido) REFERENCES estados_pedidos(id_estado_pedido),
    CONSTRAINT FK_pedidos_id_domicilio_END FOREIGN KEY (id_domicilio) REFERENCES domicilios(id_domicilio),
    CONSTRAINT FK_pedidos_id_metodo_pago_END FOREIGN KEY (id_metodo_pago) REFERENCES metodos_pago(id_metodo_pago)
);

-- Tabla de pagos
CREATE TABLE pagos (
    id_pago INT PRIMARY KEY AUTO_INCREMENT,
    id_pedido INT,
    fecha DATE,
    comprobante VARCHAR(100),
    numero_transaccion VARCHAR(100),
    CONSTRAINT FK_pagos_id_pedido_END FOREIGN KEY (id_pedido) REFERENCES pedidos(id_pedido)
);

-- Tabla de variantes (variante del producto)
CREATE TABLE variantes (
    sku VARCHAR(100),
    nombre_variante VARCHAR(100),
    id_producto INT,
    id_estado_producto INT DEFAULT 1,
    aroma VARCHAR(100),
    color INT,
    stock INT,
    CONSTRAINT PK_variante_sku_END PRIMARY KEY (sku),
    CONSTRAINT FK_variante_id_producto_END          FOREIGN KEY (id_producto)           REFERENCES productos(id_producto),
    CONSTRAINT FK_variante_id_estado_producto_END   FOREIGN KEY (id_estado_producto)    REFERENCES estados_productos(id_estado_producto)
);

-- Tabla de productos pedidos (relación entre pedidos y productos)
CREATE TABLE productos_pedido (
    id_pedido INT,
    id_producto INT,
    sku VARCHAR(100),
    cantidad INT,
    precio DECIMAL(10, 2),
    estado TINYINT DEFAULT 1,
    CONSTRAINT FK_productos_pedido_id_pedido_END FOREIGN KEY (id_pedido) REFERENCES pedidos(id_pedido),
    CONSTRAINT FK_productos_pedido_sku_END FOREIGN KEY (sku) REFERENCES variantes(sku)
);

-- Relación entre info_usuario y domicilio
CREATE TABLE usuario_domicilios (
    id_usuario INT,
    id_domicilio INT,
    tipo_domicilio VARCHAR(100),
    principal TINYINT DEFAULT 0,
    estado TINYINT DEFAULT 1,
    PRIMARY KEY (id_usuario, id_domicilio),
    CONSTRAINT FK_usuario_domicilio_id_usuario_END FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario),
    CONSTRAINT FK_usuario_domicilio_id_domicilio_END FOREIGN KEY (id_domicilio) REFERENCES domicilios(id_domicilio)
);

-- Tabla de variantes_tipo_precio (relación entre variantes y tipos de precios)
CREATE TABLE variantes_tipo_precio (
    id_producto INT,
    id_tipo_precio INT,
    precio DECIMAL(10, 2),
    cantidad_minima INT,
    PRIMARY KEY (id_tipo_precio, id_producto),
    CONSTRAINT FK_variante_tipo_precio_id_tipo_precio    FOREIGN KEY (id_tipo_precio)    REFERENCES tipos_precios(id_tipo_precio),
    CONSTRAINT FK_variante_tipo_precio_sku               FOREIGN KEY (id_producto)       REFERENCES productos(id_producto)
);

-- Insertar datos en permisos
INSERT INTO permisos (nombre, descripcion) VALUES 
('Admin', 'Permisos de administrador'),
('User', 'Permisos de usuario regular');

-- Insertar datos en estado_usuario
INSERT INTO estados_usuarios (nombre, descripcion) VALUES 
('Activo', 'Usuario activo'),
('Inactivo', 'Usuario inactivo');

-- Insertar datos en sexo
INSERT INTO sexos (nombre, descripcion) VALUES 
('Masculino', 'Hombre'),
('Femenino', 'Mujer');

-- Insertar datos en usuarios
INSERT INTO usuarios (id_permiso, id_estado_usuario, email, clave) VALUES 
(1, 1, 'admin@empresa.com', 'admin123'),
(2, 1, 'usuario1@empresa.com', 'user123'),
(2, 1, 'usuario2@empresa.com', 'user456');

-- Insertar datos en info_usuario
INSERT INTO info_usuarios (id_usuario, id_sexo, nombre, apellido, dni, fecha_nacimiento, telefono) VALUES 
(1, 1, 'Juan', 'Pérez', '12345678', '1985-05-15', '1234567890'),
(2, 2, 'María', 'González', '23456789', '1990-07-10', '0987654321'),
(3, 1, 'Carlos', 'López', '34567890', '1992-09-20', '1122334455');

-- Insertar datos en domicilio
INSERT INTO domicilios (codigo_postal, provincia, localidad, barrio, calle, numero) VALUES 
('1000', 'Buenos Aires', 'CABA', 'Palermo', 'Calle Falsa', '123'),
('2000', 'Buenos Aires', 'CABA', 'Belgrano', 'Avenida Siempreviva', '742');

-- Insertar relación entre info_usuario y domicilio
INSERT INTO usuario_domicilios (id_usuario, id_domicilio, tipo_domicilio) VALUES 
(1, 1, 'Residencial'),
(2, 2, 'Residencial');

-- Insertar datos en categorías
INSERT INTO categorias (nombre, descripcion) VALUES 
('Perfumes', 'Fragrancias y perfumes'),
('Aromas para el hogar', 'Difusores y velas aromáticas');

-- Insertar datos en estado_producto
INSERT INTO estados_productos (nombre, descripcion) VALUES 
('Disponible', 'Producto en stock'),
('Agotado', 'Producto fuera de stock');

-- Insertar datos en productos
INSERT INTO productos (nombre, descripcion, id_categoria, destacado) VALUES 
('Perfume Floral', 'Un perfume con fragancias florales', 1, 1),
('Perfume Amaderado', 'Un perfume con fragancia amaderada', 1, 0),
('Difusor de Vainilla', 'Un difusor con aroma a vainilla', 2, 1),
('Vela Aromática', 'Una vela con aroma a lavanda', 2, 0),
('Perfume Cítrico', 'Un perfume con fragancias cítricas', 1, 1),
('Perfume Dulce', 'Un perfume con un toque dulce', 1, 0),
('Difusor de Eucalipto', 'Un difusor con aroma a eucalipto', 2, 1),
('Vela Aromática Coco', 'Una vela con aroma a coco', 2, 0),
('Perfume Deportivo', 'Un perfume ideal para actividades deportivas', 1, 1),
('Perfume de Noche', 'Un perfume ideal para la noche', 1, 0),
('Difusor de Frutos Rojos', 'Un difusor con aroma a frutos rojos', 2, 1),
('Vela Aromática Canela', 'Una vela con aroma a canela', 2, 0),
('Perfume de Viaje', 'Un perfume ideal para llevar en viajes', 1, 0),
('Perfume de Verano', 'Un perfume fresco para el verano', 1, 0),
('Difusor de Lavanda', 'Un difusor con aroma a lavanda', 2, 0),
('Vela Aromática Limón', 'Una vela con aroma a limón', 2, 0),
('Perfume de Otoño', 'Un perfume cálido para el otoño', 1, 0),
('Perfume de Invierno', 'Un perfume suave para el invierno', 1, 0),
('Difusor de Té Verde', 'Un difusor con aroma a té verde', 2, 0),
('Vela Aromática Jazmín', 'Una vela con aroma a jazmín', 2, 0),
('Perfume Exótico', 'Un perfume con fragancias exóticas', 1, 0),
('Perfume Clásico', 'Un perfume con un toque clásico', 1, 0),
('Difusor de Menta', 'Un difusor con aroma a menta', 2, 0),
('Vela Aromática Naranja', 'Una vela con aroma a naranja', 2, 0);



-- Insertar datos en variantes
INSERT INTO variantes (sku, nombre_variante, id_producto, id_estado_producto, aroma, color, stock) VALUES 
('SKU001', 'Variante Lavanda', 1, 1, 'Lavanda', 1, 50),
('SKU002', 'Variante Vainilla', 1, 1, 'Vainilla', 2, 30),
('SKU003', 'Variante Rosa', 2, 1, 'Rosa', 3, 20),
('SKU004', 'Variante Jazmín', 2, 2, 'Jazmín', 4, 0),
('SKU005', 'Variante Coco', 3, 1, 'Coco', 1, 15),
('SKU006', 'Variante Canela', 3, 1, 'Canela', 2, 10),
('SKU007', 'Variante Eucalipto', 4, 1, 'Eucalipto', 3, 40),
('SKU008', 'Variante Menta', 4, 1, 'Menta', 4, 25),
('SKU009', 'Variante Limón', 5, 1, 'Limón', 1, 45),
('SKU010', 'Variante Naranja', 5, 1, 'Naranja', 2, 30),
('SKU011', 'Variante Lavanda', 6, 1, 'Lavanda', 3, 50),
('SKU012', 'Variante Vainilla', 6, 1, 'Vainilla', 4, 20),
('SKU013', 'Variante Rosa', 7, 1, 'Rosa', 1, 10),
('SKU014', 'Variante Jazmín', 8, 1, 'Jazmín', 2, 15),
('SKU015', 'Variante Coco', 9, 1, 'Coco', 3, 25),
('SKU016', 'Variante Canela', 9, 1, 'Canela', 4, 20),
('SKU017', 'Variante Eucalipto', 10, 1, 'Eucalipto', 1, 30),
('SKU018', 'Variante Menta', 11, 1, 'Menta', 2, 15),
('SKU019', 'Variante Limón', 12, 1, 'Limón', 3, 18),
('SKU020', 'Variante Naranja', 13, 1, 'Naranja', 4, 12),
('SKU021', 'Variante Lavanda', 14, 1, 'Lavanda', 1, 40),
('SKU022', 'Variante Vainilla', 15, 1, 'Vainilla', 2, 30),
('SKU023', 'Variante Rosa', 16, 1, 'Rosa', 3, 25),
('SKU024', 'Variante Jazmín', 17, 1, 'Jazmín', 4, 35),
('SKU025', 'Variante Coco', 18, 1, 'Coco', 1, 28),
('SKU026', 'Variante Canela', 19, 1, 'Canela', 2, 22),
('SKU027', 'Variante Eucalipto', 20, 1, 'Eucalipto', 3, 18),
('SKU028', 'Variante Menta', 21, 1, 'Menta', 4, 50),
('SKU029', 'Variante Limón', 22, 1, 'Limón', 1, 40),
('SKU030', 'Variante Naranja', 23, 1, 'Naranja', 2, 10),
('SKU031', 'Variante Lavanda', 24, 1, 'Lavanda', 3, 15);

-- Insertar datos en imágenes
INSERT INTO imagenes (id_producto, ruta, principal) VALUES 
(1, '/imagen/1.webp', 1),
(2, '/imagen/2.webp', 1),
(3, '/imagen/3.webp', 1),
(4, '/imagen/4.webp', 1),
(5, '/imagen/5.webp', 1),
(6, '/imagen/6.webp', 1),
(7, '/imagen/7.webp', 1),
(8, '/imagen/8.webp', 1),
(9, '/imagen/9.webp', 1),
(10, '/imagen/10.webp', 1),
(11, '/imagen/11.webp', 1),
(12, '/imagen/12.webp', 1),
(13, '/imagen/13.webp', 1),
(14, '/imagen/14.webp', 1),
(15, '/imagen/15.webp', 1),
(16, '/imagen/16.webp', 1),
(17, '/imagen/17.webp', 1),
(18, '/imagen/18.webp', 1),
(19, '/imagen/19.jpg', 1),
(20, '/imagen/20.jpg', 1),
(21, '/imagen/21.jpg', 1),
(22, '/imagen/22.jpg', 1),
(23, '/imagen/23.jpg', 1),
(24, '/imagen/24.jpg', 1),
(1, '/imagen/25.jpg', 0),
(1, '/imagen/26.jpg', 0),
(2, '/imagen/27.jpg', 0),
(2, '/imagen/28.jpg', 0),
(3, '/imagen/29.jpg', 0),
(3, '/imagen/30.jpg', 0),
(4, '/imagen/31.jpg', 0),
(4, '/imagen/32.jpg', 0);

-- Insertar datos en estados_pedidos
INSERT INTO estados_pedidos (nombre, descripcion) VALUES 
('pendiente', 'Pedido pendiente , para ser procesado y aprobado por la empresa'),
('procesado', 'Pedido procesado, se aprobo el envio del pedido'),
('cambiado', 'Pedido cambiado por falta de stock'),
('en-camino', 'Pedido enviado al cliente'),
('entregado', 'Pedido entregado al cliente'),
('cancelado', 'Pedido cancelado por el cliente');

-- Insertar datos en metodos_pago
INSERT INTO metodos_pago (tipo) VALUES 
('Transferencia bancaria'),
('Mercado Pago');

INSERT INTO pedidos (id_usuario, id_estado_pedido, total, fecha, id_domicilio, id_metodo_pago) VALUES 
(2, 1, 1200.50, '2024-01-01', 1, 1),
(3, 2, 750.00, '2024-01-05', 1, 2),
(2, 3, 350.75, '2024-01-10', 1, 1),
(1, 4, 1500.00, '2024-01-11', 1, 2),
(2, 5, 300.00, '2024-01-12', 1, 1),
(3, 6, 850.00, '2024-01-13', 1, 2),
(1, 1, 500.00, '2024-01-14', 1, 1),
(2, 2, 1200.00, '2024-01-15', 1, 2),
(3, 3, 600.00, '2024-01-16', 1, 1),
(1, 4, 750.00, '2024-01-17', 1, 2),
(2, 5, 400.00, '2024-01-18', 1, 1),
(3, 6, 950.00, '2024-01-19', 1, 2),
(1, 1, 1250.00, '2024-01-20', 1, 1),
(2, 2, 800.00, '2024-01-21', 1, 2),
(3, 3, 900.00, '2024-01-22', 1, 1),
(1, 4, 700.00, '2024-01-23', 1, 2),
(2, 5, 300.00, '2024-01-24', 1, 1),
(3, 6, 450.00, '2024-01-25', 1, 2),
(1, 1, 600.00, '2024-01-26', 1, 1),
(2, 2, 350.00, '2024-01-27', 1, 2),
(3, 3, 500.00, '2024-01-28', 1, 1),
(1, 4, 1000.00, '2024-01-29', 1, 2),
(2, 5, 200.00, '2024-01-30', 1, 1);

-- Insertar datos en pagos
INSERT INTO pagos (id_pedido, fecha, comprobante, numero_transaccion) VALUES 
(1, '2024-01-02', 'comprobante_001', NULL),
(2, '2024-01-06', NULL, 'transaccion_001'),
(3, '2024-01-11', 'comprobante_002', NULL),
(4, '2024-01-12', NULL, 'transaccion_002'),
(5, '2024-01-13', 'comprobante_003', NULL),
(6, '2024-01-14', NULL, 'transaccion_003'),
(7, '2024-01-15', 'comprobante_004', NULL),
(8, '2024-01-16', NULL, 'transaccion_004'),
(9, '2024-01-17', 'comprobante_005', NULL),
(10, '2024-01-18', NULL, 'transaccion_005'),
(11, '2024-01-19', 'comprobante_006', NULL),
(12, '2024-01-20', NULL, 'transaccion_006'),
(13, '2024-01-21', 'comprobante_007', NULL),
(14, '2024-01-22', NULL, 'transaccion_007'),
(15, '2024-01-23', 'comprobante_008', NULL),
(16, '2024-01-24', NULL, 'transaccion_008'),
(17, '2024-01-25', 'comprobante_009', NULL),
(18, '2024-01-26', NULL, 'transaccion_009'),
(19, '2024-01-27', 'comprobante_010', NULL),
(20, '2024-01-28', NULL, 'transaccion_010'),
(21, '2024-01-29', 'comprobante_011', NULL),
(22, '2024-01-30', NULL, 'transaccion_011'),
(23, '2024-01-31', 'comprobante_012', NULL);

-- Insertar datos en productos_pedido
INSERT INTO productos_pedido (id_pedido, id_producto, sku, cantidad, precio) VALUES 
(1, 1, 'SKU001', 2, 600.25), 
(1, 1, 'SKU002', 1, 300.00), 
(1, 2, 'SKU003', 1, 750.00), 
(1, 2, 'SKU004', 3, 116.92),
(2, 3, 'SKU005', 2, 900.00), 
(2, 3, 'SKU006', 1, 600.00), 
(3, 4, 'SKU007', 1, 300.00), 
(3, 4, 'SKU008', 2, 400.00), 
(4, 5, 'SKU009', 1, 500.00), 
(4, 5, 'SKU010', 3, 1200.00), 
(5, 6, 'SKU011', 1, 600.00), 
(5, 6, 'SKU012', 2, 150.00), 
(6, 7, 'SKU013', 1, 700.00), 
(6, 8, 'SKU014', 1, 900.00), 
(7, 9, 'SKU015', 2, 140.00), 
(7, 9, 'SKU016', 3, 600.00), 
(8, 10, 'SKU017', 1, 800.00), 
(8, 11, 'SKU018', 2, 500.00), 
(9, 12, 'SKU019', 1, 200.00), 
(9, 13, 'SKU020', 3, 900.00), 
(10, 14, 'SKU021', 1, 400.00), 
(10, 15, 'SKU022', 2, 500.00), 
(11, 16, 'SKU023', 1, 600.00), 
(11, 17, 'SKU024', 3, 500.00), 
(12, 18, 'SKU025', 2, 300.00),
(12, 19, 'SKU026', 1, 150.00),
(13, 20, 'SKU027', 2, 450.00),
(13, 21, 'SKU028', 1, 500.00),
(14, 22, 'SKU029', 3, 600.00),
(14, 23, 'SKU030', 2, 700.00),
(15, 24, 'SKU031', 1, 800.00),
(15, 1, 'SKU001', 2, 900.00),
(16, 2, 'SKU002', 1, 1000.00),
(16, 3, 'SKU003', 3, 1100.00),
(17, 4, 'SKU004', 2, 1200.00),
(17, 5, 'SKU005', 1, 1300.00),
(18, 6, 'SKU006', 3, 1400.00),
(18, 7, 'SKU007', 2, 1500.00),
(19, 8, 'SKU008', 1, 1600.00),
(19, 9, 'SKU009', 3, 1700.00),
(20, 10, 'SKU010', 2, 1800.00),
(20, 11, 'SKU011', 1, 1900.00);

-- Insertar datos en tipos_precios
INSERT INTO tipos_precios (nombre, descripcion) VALUES 
('Precio Minorista', 'Precio para ventas al por menor'),
('Precio Mayorista', 'Precio para ventas al por mayor');

-- Insertar datos en variante_tipo_precio
INSERT INTO variantes_tipo_precio (id_producto, id_tipo_precio, precio, cantidad_minima) VALUES 
(1, 1, 600.25, 1), 
(1, 2, 300.00, 1), 
(2, 1, 750.00, 1), 
(2, 2, 116.92, 1),
(3, 1, 900.00, 1), 
(3, 2, 600.00, 1), 
(4, 1, 300.00, 1), 
(4, 2, 400.00, 1), 
(5, 1, 500.00, 1), 
(5, 2, 1200.00, 1), 
(6, 1, 600.00, 1), 
(6, 2, 150.00, 1), 
(7, 1, 700.00, 1), 
(8, 1, 900.00, 1), 
(9, 1, 140.00, 1), 
(9, 2, 600.00, 1), 
(10, 1, 800.00, 1), 
(11, 1, 500.00, 1), 
(12, 1, 200.00, 1), 
(13, 1, 900.00, 1), 
(14, 1, 400.00, 1), 
(15, 1, 500.00, 1), 
(16, 1, 600.00, 1), 
(17, 1, 500.00, 1), 
(18, 1, 300.00, 1),
(19, 1, 150.00, 1),
(20, 1, 450.00, 1),
(21, 1, 500.00, 1),
(22, 1, 600.00, 1),
(23, 1, 700.00, 1),
(24, 1, 800.00, 1);

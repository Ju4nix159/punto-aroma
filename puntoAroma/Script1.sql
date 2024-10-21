


-- Drop tables if they exist
DROP TABLE IF EXISTS productos_pedido;
DROP TABLE IF EXISTS pedidos;
DROP TABLE IF EXISTS variante_tipo_precio;
DROP TABLE IF EXISTS tipo_precio;
DROP TABLE IF EXISTS variantes;
DROP TABLE IF EXISTS imagenes;
DROP TABLE IF EXISTS productos;
DROP TABLE IF EXISTS tamaño;
DROP TABLE IF EXISTS color;
DROP TABLE IF EXISTS aromas;
DROP TABLE IF EXISTS categorias;
DROP TABLE IF EXISTS info_usuario;
DROP TABLE IF EXISTS usuario_domicilio;
DROP TABLE IF EXISTS domicilio;
DROP TABLE IF EXISTS usuario;
DROP TABLE IF EXISTS estado_usuario;
DROP TABLE IF EXISTS permisos;
DROP TABLE IF EXISTS sexo;
DROP TABLE IF EXISTS estado_producto;


-- Tabla de categorías
CREATE TABLE categorias (
    id_categoria INT PRIMARY KEY,
    nombre VARCHAR(100),
    descripcion TEXT
);

-- Tabla de aromas
CREATE TABLE aromas (
    id_aroma INT PRIMARY KEY,
    nombre VARCHAR(100)
);

-- Tabla de color
CREATE TABLE colores (
    id_color INT PRIMARY KEY,
    nombre VARCHAR(100)
);




-- Tabla de productos
CREATE TABLE productos (
    id_producto INT PRIMARY KEY,
    nombre VARCHAR(100),
    descripcion TEXT,
    id_categoria INT,
    destacado TINYINT DEFAULT 0,
    CONSTRAINT FK_productos_id_categoria_END FOREIGN KEY (id_categoria) REFERENCES categorias(id_categoria)
);

-- Tabla de imágenes de los productos
CREATE TABLE imagenes (
    id_imagen INT PRIMARY KEY,
    id_producto INT,
    ruta TEXT,
    principal TINYINT DEFAULT 0,
    CONSTRAINT FK_imagenes_id_producto_END FOREIGN KEY (id_producto) REFERENCES productos(id_producto)

);

-- Tabla de estado de productos
CREATE TABLE estados_producto (
    id_estado_producto INT PRIMARY KEY,
    nombre VARCHAR(100),
    descripcion TEXT
);

-- Tabla de variante (variante del producto)
CREATE TABLE variantes (
    sku VARCHAR(100) ,

    id_producto INT ,
    id_estado_producto INT,
    id_aroma INT,
    id_color INT,
    stock INT,
    CONSTRAINT PK_variante_sku_END PRIMARY KEY (sku),
    CONSTRAINT FK_variante_id_producto_END          FOREIGN KEY (id_producto)           REFERENCES productos(id_producto),
    CONSTRAINT FK_variante_id_estado_producto_END   FOREIGN KEY (id_estado_producto)    REFERENCES estado_producto(id_estado_producto),
    CONSTRAINT FK_variante_id_aroma                 FOREIGN KEY (id_aroma)              REFERENCES aromas(id_aroma),
    CONSTRAINT FK_variante_id_color                 FOREIGN KEY (id_color)              REFERENCES color(id_color),
);

-- Tabla de pedidos
CREATE TABLE pedidos (
    id_pedido INT PRIMARY KEY,
    id_usuario INT,
    total DECIMAL(10, 2),
    fecha DATE
);

-- Tabla de productos pedidos (relación entre pedidos y productos)
CREATE TABLE productos_pedido (
    id_compra INT PRIMARY KEY,
    id_pedido INT,
    id_producto INT,
    sku VARCHAR(100),
    cantidad INT,
    precio DECIMAL(10, 2),
    CONSTRAINT FK_productos_pedido_id_pedido_END FOREIGN KEY (id_pedido) REFERENCES pedidos(id_pedido),
    CONSTRAINT FK_productos_pedido_sku_END FOREIGN KEY (sku) REFERENCES variantes(sku),
);

-- Tabla de permisos
CREATE TABLE permisos (
    id_permiso INT PRIMARY KEY,
    nombre VARCHAR(100),
    descripcion TEXT
);

-- Tabla de estado de usuario
CREATE TABLE estados_usuario (
    id_estado_usuario INT PRIMARY KEY,
    nombre VARCHAR(100),
    descripcion TEXT
);

-- Tabla de usuario
CREATE TABLE usuarios (
    id_usuario INT PRIMARY KEY,
    id_permiso INT,
    id_estado_usuario INT,
    email VARCHAR(100),
    contraseña VARCHAR(100),
    CONSTRAINT FK_usuario_id_permiso_END FOREIGN KEY (id_permiso) REFERENCES permisos(id_permiso),
    CONSTRAINT FK_usuario_id_estado_usuario_END FOREIGN KEY (id_estado_usuario) REFERENCES estado_usuario(id_estado_usuario)
);

-- Tabla de sexo
CREATE TABLE sexos (
    id_sexo INT PRIMARY KEY,
    nombre VARCHAR(100),
    descripcion TEXT
);

-- Tabla de información del usuario
CREATE TABLE info_usuarios (
    id_info_usuario INT PRIMARY KEY,
    id_usuario INT,
    id_sexo INT,
    nombre VARCHAR(100),
    apellido VARCHAR(100),
    dni VARCHAR(20),
    fecha_nacimiento DATE,
    telefono VARCHAR(20),
    CONSTRAINT FK_info_usuario_id_usuario_END FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario),
    CONSTRAINT FK_info_usuario_id_sexo_END FOREIGN KEY (id_sexo) REFERENCES sexo(id_sexo)
);

-- Tabla de domicilio
CREATE TABLE domicilios (
    id_domicilio INT PRIMARY KEY,
    codigo_postal VARCHAR(10),
    provincia VARCHAR(100),
    localidad VARCHAR(100),
    barrio VARCHAR(100),
    calle VARCHAR(100),
    numero VARCHAR(10)
);

-- Relación entre info_usuario y domicilio
CREATE TABLE usuario_domicilios (
    id_info_usuario INT,
    id_domicilio INT,
    tipo_domicilio VARCHAR(100),
    PRIMARY KEY (id_info_usuario, id_domicilio),
    CONSTRAINT FK_usuario_domicilio_id_info_usuario_END FOREIGN KEY (id_info_usuario) REFERENCES info_usuario(id_info_usuario),
    CONSTRAINT FK_usuario_domicilio_id_domicilio_END FOREIGN KEY (id_domicilio) REFERENCES domicilio(id_domicilio)
);

-- Crear tabla tipo_precio
CREATE TABLE tipo_precios (
    id_tipo_precio INT PRIMARY KEY,
    nombre VARCHAR(100),
    descripcion TEXT
);

-- Crear tabla variante_tipo_precio
CREATE TABLE variantes_tipo_precio (
    id_producto INT,
    id_tipo_precio INT,
    precio DECIMAL(10, 2),
    cantidad_minima INT,
    PRIMARY KEY (id_tipo_precio, id_producto),
    CONSTRAINT FK_variante_tipo_precio_id_tipo_precio    FOREIGN KEY (id_tipo_precio)    REFERENCES tipo_precio(id_tipo_precio),
    CONSTRAINT FK_variante_tipo_precio_sku               FOREIGN KEY (id_producto)       REFERENCES productos(id_producto),

);



-- Insertar datos en permisos (solo algunos registros)
-- Insertar datos en permisos (solo algunos registros)
INSERT INTO permisos (id_permiso, nombre, descripcion) VALUES 
(1, 'Admin', 'Permisos de administrador'),
(2, 'User', 'Permisos de usuario regular');

-- Insertar datos en estado_usuario
INSERT INTO estados_usuario (id_estado_usuario, nombre, descripcion) VALUES 
(1, 'Activo', 'Usuario activo'),
(2, 'Inactivo', 'Usuario inactivo');

-- Insertar datos en sexo
INSERT INTO sexos (id_sexo, nombre, descripcion) VALUES 
(1, 'Masculino', 'Hombre'),
(2, 'Femenino', 'Mujer');

-- Insertar datos en usuarios (pocos registros)
INSERT INTO usuarios (id_usuario, id_permiso, id_estado_usuario, email, contraseña) VALUES 
(1, 1, 1, 'admin@empresa.com', 'admin123'),
(2, 2, 1, 'usuario1@empresa.com', 'user123'),
(3, 2, 1, 'usuario2@empresa.com', 'user456'),
(4, 1, 1, 'juanimelillo@gmail.com', '1234');


-- Insertar datos en info_usuario
INSERT INTO info_usuarios (id_info_usuario, id_usuario, id_sexo, nombre, apellido, dni, fecha_nacimiento, telefono) VALUES 
(1, 1, 1, 'Juan', 'Pérez', '12345678', '1985-05-15', '1234567890'),
(2, 2, 2, 'María', 'González', '23456789', '1990-07-10', '0987654321'),
(3, 3, 1, 'Carlos', 'López', '34567890', '1992-09-20', '1122334455');

-- Insertar datos en domicilio
INSERT INTO domicilios (id_domicilio, codigo_postal, provincia, localidad, barrio, calle, numero) VALUES 
(1, '1000', 'Buenos Aires', 'CABA', 'Palermo', 'Calle Falsa', '123'),
(2, '2000', 'Buenos Aires', 'CABA', 'Belgrano', 'Avenida Siempreviva', '742');

-- Insertar relación entre info_usuario y domicilio
INSERT INTO usuario_domicilios (id_info_usuario, id_domicilio, tipo_domicilio) VALUES 
(1, 1, 'Residencial'),
(2, 2, 'Residencial');

-- Insertar datos en categorías
INSERT INTO categorias (id_categoria, nombre, descripcion) VALUES 
(1, 'Perfumes', 'Fragrancias y perfumes'),
(2, 'Aromas para el hogar', 'Difusores y velas aromáticas');

-- Insertar datos en estado_producto
INSERT INTO estados_producto (id_estado_producto, nombre, descripcion) VALUES 
(1, 'Disponible', 'Producto en stock'),
(2, 'Agotado', 'Producto fuera de stock');



-- Insertar datos en productos (muchos productos)
INSERT INTO productos (id_producto, nombre, descripcion, id_categoria, destacado) VALUES 
(1, 'Perfume Floral', 'Un perfume con fragancias florales', 1, 1),
(2, 'Perfume Amaderado', 'Un perfume con fragancia amaderada', 1, 0),
(3, 'Difusor de Vainilla', 'Un difusor con aroma a vainilla', 2, 1),
(4, 'Vela Aromática', 'Una vela con aroma a lavanda', 2, 0),
(5, 'Perfume Cítrico', 'Un perfume con fragancias cítricas', 1, 1),
(6, 'Perfume Dulce', 'Un perfume con un toque dulce', 1, 0),
(7, 'Difusor de Eucalipto', 'Un difusor con aroma a eucalipto', 2, 1),
(8, 'Vela Aromática Coco', 'Una vela con aroma a coco', 2, 0),
(9, 'Perfume Deportivo', 'Un perfume ideal para actividades deportivas', 1, 1),
(10, 'Perfume de Noche', 'Un perfume ideal para la noche', 1, 0),
(11, 'Difusor de Frutos Rojos', 'Un difusor con aroma a frutos rojos', 2, 1),
(12, 'Vela Aromática Canela', 'Una vela con aroma a canela', 2, 0),
(13, 'Perfume de Viaje', 'Un perfume ideal para llevar en viajes', 1, 0),
(14, 'Perfume de Verano', 'Un perfume fresco para el verano', 1, 0),
(15, 'Difusor de Lavanda', 'Un difusor con aroma a lavanda', 2, 0),
(16, 'Vela Aromática Limón', 'Una vela con aroma a limón', 2, 0),
(17, 'Perfume de Otoño', 'Un perfume cálido para el otoño', 1, 0),
(18, 'Perfume de Invierno', 'Un perfume suave para el invierno', 1, 0),
(19, 'Difusor de Té Verde', 'Un difusor con aroma a té verde', 2, 0),
(20, 'Vela Aromática Jazmín', 'Una vela con aroma a jazmín', 2, 0),
(21, 'Perfume Exótico', 'Un perfume con fragancias exóticas', 1, 0),
(22, 'Perfume Clásico', 'Un perfume con un toque clásico', 1, 0),
(23, 'Difusor de Menta', 'Un difusor con aroma a menta', 2, 0),
(24, 'Vela Aromática Naranja', 'Una vela con aroma a naranja', 2, 0);

-- Insertar datos en aromas
INSERT INTO aromas (id_aroma, nombre) VALUES 
(1, 'Lavanda'),
(2, 'Vainilla'),
(3, 'Cítrico'),
(4, 'Amaderado'),
(5, 'Floral'),
(6, 'Frutos Rojos'),
(7, 'Menta'),
(8, 'Canela'),
(9, 'Coco'),
(10, 'Jazmín');

-- Insertar datos en color
INSERT INTO colores (id_color, nombre) VALUES 
(1, 'Rojo'),
(2, 'Azul'),
(3, 'Verde'),
(4, 'Amarillo'),
(5, 'Negro'),
(6, 'Blanco'),
(7, 'Rosa'),
(8, 'Morado'),
(9, 'Naranja'),
(10, 'Gris');



INSERT INTO variantes (sku, id_producto, id_estado_producto, id_aroma, id_color, stock) VALUES 
('SKU001', 1, 1, 1, 1, 50),
('SKU002', 1, 1, 2, 2, 30),
('SKU003', 2, 1, 3, 3, 20),
('SKU004', 2, 2, 4, 4, 0),
('SKU005', 3, 1, 5, 1, 15),
('SKU006', 3, 1, 6, 2, 10),
('SKU007', 4, 1, 7, 3, 40),
('SKU008', 4, 1, 8, 4, 25),
('SKU009', 5, 1, 9, 1, 45),
('SKU010', 5, 1, 10, 2, 30),
('SKU011', 6, 1, 1, 3, 50),
('SKU012', 6, 1, 2, 4, 20),
('SKU013', 7, 1, 3, 1, 10),
('SKU014', 8, 1, 4, 2, 15),
('SKU015', 9, 1, 5, 3, 25),
('SKU016', 9, 1, 6, 4, 20),
('SKU017', 10, 1, 7, 1, 30),
('SKU018', 11, 1, 8, 2, 15),
('SKU019', 12, 1, 9, 3, 18),
('SKU020', 13, 1, 10, 4, 12),
('SKU021', 14, 1, 1, 1, 40),
('SKU022', 15, 1, 2, 2, 30),
('SKU023', 16, 1, 3, 3, 25),
('SKU024', 17, 1, 4, 4, 35),
('SKU025', 18, 1, 5, 1, 28),
('SKU026', 19, 1, 6, 2, 22),
('SKU027', 20, 1, 7, 3, 18),
('SKU028', 21, 1, 8, 4, 50),
('SKU029', 22, 1, 9, 1, 40),
('SKU030', 23, 1, 10, 2, 10),
('SKU031', 24, 1, 1, 3, 15);


-- Insertar datos en imágenes (1 imagen por producto)
INSERT INTO imagenes (id_imagen, id_producto, ruta, principal) VALUES 
(1, 1, '/imagenes/perfume_floral.jpg', 1),
(2, 2, '/imagenes/perfume_amaderado.jpg', 1),
(3, 3, '/imagenes/difusor_vainilla.jpg', 1),
(4, 4, '/imagenes/vela_aromatica.jpg', 1),
(5, 5, '/imagenes/perfume_citrico.jpg', 1),
(6, 6, '/imagenes/perfume_dulce.jpg', 1),
(7, 7, '/imagenes/difusor_eucalipto.jpg', 1),
(8, 8, '/imagenes/vela_aromatica_coco.jpg', 1),
(9, 9, '/imagenes/perfume_deportivo.jpg', 1),
(10, 10, '/imagenes/perfume_de_noche.jpg', 1),
(11, 11, '/imagenes/difusor_frutos_rojos.jpg', 1),
(12, 12, '/imagenes/vela_aromatica_canela.jpg', 1),
(13, 13, '/imagenes/perfume_de_viaje.jpg', 1),
(14, 14, '/imagenes/perfume_de_verano.jpg', 1),
(15, 15, '/imagenes/difusor_lavanda.jpg', 1),
(16, 16, '/imagenes/vela_aromatica_limon.jpg', 1),
(17, 17, '/imagenes/perfume_de_otoño.jpg', 1),
(18, 18, '/imagenes/perfume_de_invierno.jpg', 1),
(19, 19, '/imagenes/difusor_te_verde.jpg', 1),
(20, 20, '/imagenes/vela_aromatica_jazmin.jpg', 1),
(21, 21, '/imagenes/perfume_exotico.jpg', 1),
(22, 22, '/imagenes/perfume_clasico.jpg', 1),
(23, 23, '/imagenes/difusor_menta.jpg', 1),
(24, 24, '/imagenes/vela_aromatica_naranja.jpg', 1),
(25, 1, '/imagenes/perfume_floral_2.jpg', 0),
(26, 1, '/imagenes/perfume_floral_3.jpg', 0),
(27, 2, '/imagenes/perfume_amaderado_2.jpg', 0),
(28, 2, '/imagenes/perfume_amaderado_3.jpg', 0),
(29, 3, '/imagenes/difusor_vainilla_2.jpg', 0),
(30, 3, '/imagenes/difusor_vainilla_3.jpg', 0),
(31, 4, '/imagenes/vela_aromatica_2.jpg', 0),
(32, 4, '/imagenes/vela_aromatica_3.jpg', 0);

-- Insertar datos en pedidos (muchos pedidos)
INSERT INTO pedidos (id_pedido, id_usuario, total, fecha) VALUES 
(1, 2, 1200.50, '2024-01-01'),
(2, 3, 750.00, '2024-01-05'),
(3, 2, 350.75, '2024-01-10'),
(4, 1, 1500.00, '2024-01-11'),
(5, 2, 300.00, '2024-01-12'),
(6, 3, 850.00, '2024-01-13'),
(7, 1, 500.00, '2024-01-14'),
(8, 2, 1200.00, '2024-01-15'),
(9, 3, 600.00, '2024-01-16'),
(10, 1, 750.00, '2024-01-17'),
(11, 2, 400.00, '2024-01-18'),
(12, 3, 950.00, '2024-01-19'),
(13, 1, 1250.00, '2024-01-20'),
(14, 2, 800.00, '2024-01-21'),
(15, 3, 900.00, '2024-01-22'),
(16, 1, 700.00, '2024-01-23'),
(17, 2, 300.00, '2024-01-24'),
(18, 3, 450.00, '2024-01-25'),
(19, 1, 600.00, '2024-01-26'),
(20, 2, 350.00, '2024-01-27'),
(21, 3, 500.00, '2024-01-28'),
(22, 1, 1000.00, '2024-01-29'),
(23, 2, 200.00, '2024-01-30');



-- Insertar datos en productos_pedido (muchos productos por pedido)
INSERT INTO productos_pedido (id_compra, id_pedido, id_producto, sku, cantidad, precio) VALUES 
(1, 1, 1, 'SKU001', 2, 600.25), 
(2, 1, 1, 'SKU002', 1, 300.00), 
(3, 1, 2, 'SKU003', 1, 750.00), 
(4, 1, 2, 'SKU004', 3, 116.92),
(5, 2, 3, 'SKU005', 2, 900.00), 
(6, 2, 3, 'SKU006', 1, 600.00), 
(7, 3, 4, 'SKU007', 1, 300.00), 
(8, 3, 4, 'SKU008', 2, 400.00), 
(9, 4, 5, 'SKU009', 1, 500.00), 
(10, 4, 5, 'SKU010', 3, 1200.00), 
(11, 5, 6, 'SKU011', 1, 600.00), 
(12, 5, 6, 'SKU012', 2, 150.00), 
(13, 6, 7, 'SKU013', 1, 700.00), 
(14, 6, 8, 'SKU014', 1, 900.00), 
(15, 7, 9, 'SKU015', 2, 140.00), 
(16, 7, 9, 'SKU016', 3, 600.00), 
(17, 8, 10, 'SKU017', 1, 800.00), 
(18, 8, 11, 'SKU018', 2, 500.00), 
(19, 9, 12, 'SKU019', 1, 200.00), 
(20, 9, 13, 'SKU020', 3, 900.00), 
(21, 10, 14, 'SKU021', 1, 400.00), 
(22, 10, 15, 'SKU022', 2, 500.00), 
(23, 11, 16, 'SKU023', 1, 600.00), 
(24, 11, 17, 'SKU024', 3, 500.00), 
(25, 12, 18, 'SKU025', 2, 300.00),
(26, 12, 19, 'SKU026', 1, 150.00),
(27, 13, 20, 'SKU027', 2, 450.00),
(28, 13, 21, 'SKU028', 1, 500.00),
(29, 14, 22, 'SKU029', 3, 600.00),
(30, 14, 23, 'SKU030', 2, 700.00),
(31, 15, 24, 'SKU031', 1, 800.00),
(32, 15, 1, 'SKU001', 2, 900.00),
(33, 16, 2, 'SKU002', 1, 1000.00),
(34, 16, 3, 'SKU003', 3, 1100.00),
(35, 17, 4, 'SKU004', 2, 1200.00),
(36, 17, 5, 'SKU005', 1, 1300.00),
(37, 18, 6, 'SKU006', 3, 1400.00),
(38, 18, 7, 'SKU007', 2, 1500.00),
(39, 19, 8, 'SKU008', 1, 1600.00),
(40, 19, 9, 'SKU009', 3, 1700.00),
(41, 20, 10, 'SKU010', 2, 1800.00),
(42, 20, 11, 'SKU011', 1, 1900.00);


INSERT INTO tipos_precio (id_tipo_precio, nombre, descripcion) VALUES 
(1, 'Precio Minorista', 'Precio para ventas al por menor'),
(2, 'Precio Mayorista', 'Precio para ventas al por mayor');

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



--todos los productos con su precio minorista
SELECT p.id_producto, p.nombre, p.descripcion, c.nombre AS categoria, vtp.precio AS precio_minorista, i.ruta AS imagen_principal
FROM productos p
JOIN variante_tipo_precio vtp ON p.id_producto = vtp.id_producto
JOIN categorias c ON p.id_categoria = c.id_categoria
LEFT JOIN imagenes i ON p.id_producto = i.id_producto AND i.principal = 1
WHERE vtp.id_tipo_precio = 1;

-- todos los productos con su precio mayorista
SELECT p.id_producto, p.nombre, p.descripcion, c.nombre AS categoria, vtp.precio AS precio_minorista, i.ruta AS imagen_principal
FROM productos p
JOIN variante_tipo_precio vtp ON p.id_producto = vtp.id_producto
JOIN categorias c ON p.id_categoria = c.id_categoria
LEFT JOIN imagenes i ON p.id_producto = i.id_producto AND i.principal = 1
WHERE vtp.id_tipo_precio = 2;



-- todas las variantes de un producto
SELECT v.sku, v.stock, p.nombre AS producto_nombre, ep.nombre AS estado_producto_nombre, a.nombre AS aroma_nombre, c.nombre AS color_nombre
FROM variantes v
JOIN productos p ON v.id_producto = p.id_producto
JOIN estado_producto ep ON v.id_estado_producto = ep.id_estado_producto
JOIN aromas a ON v.id_aroma = a.id_aroma
JOIN color c ON v.id_color = c.id_color
WHERE v.id_producto = 1;

-- todas las images de un producto 
SELECT i.*
FROM imagenes i
JOIN productos p ON i.id_producto = p.id_producto
WHERE p.id_producto = 1;

-- Select all data from categorias
SELECT * FROM categorias;
GO

-- Select all data from aromas
SELECT * FROM aromas;
GO

-- Select all data from color
SELECT * FROM color;
GO


-- Select all data from productos
SELECT * FROM productos;
GO

-- Select all data from imagenes
SELECT * FROM imagenes;
GO

-- Select all data from estado_producto
SELECT * FROM estado_producto;
GO

-- Select all data from variantes
SELECT * FROM variantes;
GO

-- Select all data from pedidos
SELECT * FROM pedidos;
GO

-- Select all data from productos_pedido
SELECT * FROM productos_pedido;
GO

-- Select all data from permisos
SELECT * FROM permisos;
GO

-- Select all data from estado_usuario
SELECT * FROM estado_usuario;
GO

-- Select all data from usuario
SELECT * FROM usuario;
GO

-- Select all data from sexo
SELECT * FROM sexo;
GO

-- Select all data from info_usuario
SELECT * FROM info_usuario;
GO

-- Select all data from domicilio
SELECT * FROM domicilio;
GO

-- Select all data from usuario_domicilio
SELECT * FROM usuario_domicilio;
GO

-- Select all data from tipo_precio
SELECT * FROM tipo_precio;
GO

-- Select all data from variante_tipo_precio
SELECT * FROM variante_tipo_precio;
GO

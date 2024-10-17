DELETE FROM productos;
DELETE FROM productos_pedido;
DELETE FROM pedidos;
DELETE FROM imagenes;
DELETE FROM usuario_domicilio;
DELETE FROM domicilio;
DELETE FROM info_usuario;
DELETE FROM usuario;
DELETE FROM estado_producto;
DELETE FROM categorias;
DELETE FROM permisos;
DELETE FROM estado_usuario;
DELETE FROM sexo;
DELETE FROM atributo;
DELETE FROM productos;
DELETE FROM aromas;
DELETE FROM color;
DELETE FROM tamaño;


DROP TABLE IF EXISTS productos_pedido;
DROP TABLE IF EXISTS pedidos;
DROP TABLE IF EXISTS imagenes;
DROP TABLE IF EXISTS usuario_domicilio;
DROP TABLE IF EXISTS domicilio;
DROP TABLE IF EXISTS info_usuario;
DROP TABLE IF EXISTS usuario;
DROP TABLE IF EXISTS estado_producto;
DROP TABLE IF EXISTS categorias;
DROP TABLE IF EXISTS permisos;
DROP TABLE IF EXISTS estado_usuario;
DROP TABLE IF EXISTS sexo;
DROP TABLE IF EXISTS atributo;
DROP TABLE IF EXISTS productos;
DROP TABLE IF EXISTS aromas;
DROP TABLE IF EXISTS color;
DROP TABLE IF EXISTS tamaño;


-- Tabla de categorías
CREATE TABLE categorias (
    id_categoria INT PRIMARY KEY,
    nombre VARCHAR(100),
    descripcion TEXT
);

-- Tabla de aromas
CREATE TABLE aromas (
    id_aroma INT PRIMARY KEY,
    nombre VARCHAR(100),
);

-- Tabla de color
CREATE TABLE color (
    id_color INT PRIMARY KEY,
    nombre VARCHAR(100),
);

-- Tabla de tamaño
CREATE TABLE tamaño (
    id_tamaño INT PRIMARY KEY,
    nombre VARCHAR(100),
);



-- Tabla de productos
CREATE TABLE productos (
    id_producto INT PRIMARY KEY,
    nombre VARCHAR(100),
    descripcion TEXT,
    id_categoria INT,
    CONSTRAINT FK_productos_id_categoria_END FOREIGN KEY (id_categoria) REFERENCES categorias(id_categoria)
);

-- Tabla de imágenes de los productos
CREATE TABLE imagenes (
    id_imagen INT PRIMARY KEY,
    id_producto INT,
    ruta TEXT,
    CONSTRAINT FK_imagenes_id_producto_END FOREIGN KEY (id_producto) REFERENCES productos(id_producto)
);

-- Tabla de estado de productos
CREATE TABLE estado_producto (
    id_estado_producto INT PRIMARY KEY,
    nombre VARCHAR(100),
    descripcion TEXT
);

-- Tabla de atributo (variante del producto)
CREATE TABLE atributo (
    id_atributo INT PRIMARY KEY,
    id_producto INT,
    id_estado_producto INT,
    id_color INT,
    id_tamaño INT,
    id_aroma INT,
    stock INT,
    sku VARCHAR(100),
    CONSTRAINT FK_atributo_id_producto_END FOREIGN KEY (id_producto) REFERENCES productos(id_producto),
    CONSTRAINT FK_atributo_id_estado_producto_END FOREIGN KEY (id_estado_producto) REFERENCES estado_producto(id_estado_producto),
    CONSTRAINT FK_atributo_id_color FOREIGN KEY (id_color) REFERENCES color(id_color),
    CONSTRAINT FK_atributo_id_tamaño FOREIGN KEY (id_tamaño) REFERENCES tamaño(id_tamaño),
    CONSTRAINT FK_atributo_id_aroma FOREIGN KEY (id_aroma) REFERENCES aromas(id_aroma)
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
    id_atributo INT,
    id_producto INT,
    sku VARCHAR(100),
    cantidad INT,
    precio DECIMAL(10, 2),
    CONSTRAINT FK_productos_pedido_id_pedido_END FOREIGN KEY (id_pedido) REFERENCES pedidos(id_pedido),
    CONSTRAINT FK_productos_pedido_id_atributo_END FOREIGN KEY (id_atributo) REFERENCES atributo(id_atributo),
);

-- Tabla de permisos
CREATE TABLE permisos (
    id_permiso INT PRIMARY KEY,
    nombre VARCHAR(100),
    descripcion TEXT
);

-- Tabla de estado de usuario
CREATE TABLE estado_usuario (
    id_estado_usuario INT PRIMARY KEY,
    nombre VARCHAR(100),
    descripcion TEXT
);

-- Tabla de usuario
CREATE TABLE usuario (
    id_usuario INT PRIMARY KEY,
    id_permiso INT,
    id_estado_usuario INT,
    email VARCHAR(100),
    contraseña VARCHAR(100),
    CONSTRAINT FK_usuario_id_permiso_END FOREIGN KEY (id_permiso) REFERENCES permisos(id_permiso),
    CONSTRAINT FK_usuario_id_estado_usuario_END FOREIGN KEY (id_estado_usuario) REFERENCES estado_usuario(id_estado_usuario)
);

-- Tabla de sexo
CREATE TABLE sexo (
    id_sexo INT PRIMARY KEY,
    nombre VARCHAR(100),
    descripcion TEXT
);

-- Tabla de información del usuario
CREATE TABLE info_usuario (
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
CREATE TABLE domicilio (
    id_domicilio INT PRIMARY KEY,
    codigo_postal VARCHAR(10),
    provincia VARCHAR(100),
    localidad VARCHAR(100),
    barrio VARCHAR(100),
    calle VARCHAR(100),
    numero VARCHAR(10)
);

-- Relación entre info_usuario y domicilio
CREATE TABLE usuario_domicilio (
    id_info_usuario INT,
    id_domicilio INT,
    tipo_domicilio VARCHAR(100),
    PRIMARY KEY (id_info_usuario, id_domicilio),
    CONSTRAINT FK_usuario_domicilio_id_info_usuario_END FOREIGN KEY (id_info_usuario) REFERENCES info_usuario(id_info_usuario),
    CONSTRAINT FK_usuario_domicilio_id_domicilio_END FOREIGN KEY (id_domicilio) REFERENCES domicilio(id_domicilio)
);

-- Insertar datos en permisos (solo algunos registros)
-- Insertar datos en permisos (solo algunos registros)
INSERT INTO permisos (id_permiso, nombre, descripcion) VALUES 
(1, 'Admin', 'Permisos de administrador'),
(2, 'User', 'Permisos de usuario regular');

-- Insertar datos en estado_usuario
INSERT INTO estado_usuario (id_estado_usuario, nombre, descripcion) VALUES 
(1, 'Activo', 'Usuario activo'),
(2, 'Inactivo', 'Usuario inactivo');

-- Insertar datos en sexo
INSERT INTO sexo (id_sexo, nombre, descripcion) VALUES 
(1, 'Masculino', 'Hombre'),
(2, 'Femenino', 'Mujer');

-- Insertar datos en usuarios (pocos registros)
INSERT INTO usuario (id_usuario, id_permiso, id_estado_usuario, email, contraseña) VALUES 
(1, 1, 1, 'admin@empresa.com', 'admin123'),
(2, 2, 1, 'usuario1@empresa.com', 'user123'),
(3, 2, 1, 'usuario2@empresa.com', 'user456'),
(4, 1, 1, 'juanimelillo@gmail.com', '1234');


-- Insertar datos en info_usuario
INSERT INTO info_usuario (id_info_usuario, id_usuario, id_sexo, nombre, apellido, dni, fecha_nacimiento, telefono) VALUES 
(1, 1, 1, 'Juan', 'Pérez', '12345678', '1985-05-15', '1234567890'),
(2, 2, 2, 'María', 'González', '23456789', '1990-07-10', '0987654321'),
(3, 3, 1, 'Carlos', 'López', '34567890', '1992-09-20', '1122334455');

-- Insertar datos en domicilio
INSERT INTO domicilio (id_domicilio, codigo_postal, provincia, localidad, barrio, calle, numero) VALUES 
(1, '1000', 'Buenos Aires', 'CABA', 'Palermo', 'Calle Falsa', '123'),
(2, '2000', 'Buenos Aires', 'CABA', 'Belgrano', 'Avenida Siempreviva', '742');

-- Insertar relación entre info_usuario y domicilio
INSERT INTO usuario_domicilio (id_info_usuario, id_domicilio, tipo_domicilio) VALUES 
(1, 1, 'Residencial'),
(2, 2, 'Residencial');

-- Insertar datos en categorías
INSERT INTO categorias (id_categoria, nombre, descripcion) VALUES 
(1, 'Perfumes', 'Fragrancias y perfumes'),
(2, 'Aromas para el hogar', 'Difusores y velas aromáticas');

-- Insertar datos en estado_producto
INSERT INTO estado_producto (id_estado_producto, nombre, descripcion) VALUES 
(1, 'Disponible', 'Producto en stock'),
(2, 'Agotado', 'Producto fuera de stock');



-- Insertar datos en productos (muchos productos)
INSERT INTO productos (id_producto, nombre, descripcion, id_categoria) VALUES 
(1, 'Perfume Floral', 'Un perfume con fragancias florales', 1),
(2, 'Perfume Amaderado', 'Un perfume con fragancia amaderada', 1),
(3, 'Difusor de Vainilla', 'Un difusor con aroma a vainilla', 2),
(4, 'Vela Aromática', 'Una vela con aroma a lavanda', 2),
(5, 'Perfume Cítrico', 'Un perfume con fragancias cítricas', 1),
(6, 'Perfume Dulce', 'Un perfume con un toque dulce', 1),
(7, 'Difusor de Eucalipto', 'Un difusor con aroma a eucalipto', 2),
(8, 'Vela Aromática Coco', 'Una vela con aroma a coco', 2),
(9, 'Perfume Deportivo', 'Un perfume ideal para actividades deportivas', 1),
(10, 'Perfume de Noche', 'Un perfume ideal para la noche', 1),
(11, 'Difusor de Frutos Rojos', 'Un difusor con aroma a frutos rojos', 2),
(12, 'Vela Aromática Canela', 'Una vela con aroma a canela', 2),
(13, 'Perfume de Viaje', 'Un perfume ideal para llevar en viajes', 1),
(14, 'Perfume de Verano', 'Un perfume fresco para el verano', 1),
(15, 'Difusor de Lavanda', 'Un difusor con aroma a lavanda', 2),
(16, 'Vela Aromática Limón', 'Una vela con aroma a limón', 2),
(17, 'Perfume de Otoño', 'Un perfume cálido para el otoño', 1),
(18, 'Perfume de Invierno', 'Un perfume suave para el invierno', 1),
(19, 'Difusor de Té Verde', 'Un difusor con aroma a té verde', 2),
(20, 'Vela Aromática Jazmín', 'Una vela con aroma a jazmín', 2),
(21, 'Perfume Exótico', 'Un perfume con fragancias exóticas', 1),
(22, 'Perfume Clásico', 'Un perfume con un toque clásico', 1),
(23, 'Difusor de Menta', 'Un difusor con aroma a menta', 2),
(24, 'Vela Aromática Naranja', 'Una vela con aroma a naranja', 2);

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
INSERT INTO color (id_color, nombre) VALUES 
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

-- Insertar datos en tamaño
INSERT INTO tamaño (id_tamaño, nombre) VALUES 
(1, 'Pequeño'),
(2, 'Mediano'),
(3, 'Grande'),
(4, 'Extra Grande');



INSERT INTO atributo (id_atributo, id_producto, id_estado_producto, id_color, id_tamaño, id_aroma, stock, sku) VALUES 
(1, 1, 1, 1, 1, 1, 50, 'SKU001'),
(2, 1, 1, 2, 2, 2, 30, 'SKU002'),
(3, 2, 1, 3, 3, 3, 20, 'SKU003'),
(4, 2, 2, 4, 4, 4, 0, 'SKU004'),
(5, 3, 1, 5, 1, 5, 15, 'SKU005'),
(6, 3, 1, 6, 2, 6, 10, 'SKU006'),
(7, 4, 1, 7, 3, 7, 40, 'SKU007'),
(8, 4, 1, 8, 4, 8, 25, 'SKU008'),
(9, 5, 1, 9, 1, 9, 45, 'SKU009'),
(10, 5, 1, 10, 2, 10, 30, 'SKU010'),
(11, 6, 1, 1, 3, 1, 50, 'SKU011'),
(12, 6, 1, 2, 4, 2, 20, 'SKU012'),
(13, 7, 1, 3, 1, 3, 10, 'SKU013'),
(14, 8, 1, 4, 2, 4, 15, 'SKU014'),
(15, 9, 1, 5, 3, 5, 25, 'SKU015'),
(16, 9, 1, 6, 4, 6, 20, 'SKU016'),
(17, 10, 1, 7, 1, 7, 30, 'SKU017'),
(18, 11, 1, 8, 2, 8, 15, 'SKU018'),
(19, 12, 1, 9, 3, 9, 18, 'SKU019'),
(20, 13, 1, 10, 4, 10, 12, 'SKU020'),
(21, 14, 1, 1, 1, 1, 40, 'SKU021'),
(22, 15, 1, 2, 2, 2, 30, 'SKU022'),
(23, 16, 1, 3, 3, 3, 25, 'SKU023'),
(24, 17, 1, 4, 4, 4, 35, 'SKU024'),
(25, 18, 1, 5, 1, 5, 28, 'SKU025'),
(26, 19, 1, 6, 2, 6, 22, 'SKU026'),
(27, 20, 1, 7, 3, 7, 18, 'SKU027'),
(28, 21, 1, 8, 4, 8, 50, 'SKU028'),
(29, 22, 1, 9, 1, 9, 40, 'SKU029'),
(30, 23, 1, 10, 2, 10, 10, 'SKU030'),
(31, 24, 1, 1, 3, 1, 15, 'SKU031');


-- Insertar datos en imágenes (1 imagen por producto)
INSERT INTO imagenes (id_imagen, id_producto, ruta) VALUES 
(1, 1, '/imagenes/perfume_floral.jpg'),
(2, 2, '/imagenes/perfume_amaderado.jpg'),
(3, 3, '/imagenes/difusor_vainilla.jpg'),
(4, 4, '/imagenes/vela_aromatica.jpg');

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
INSERT INTO productos_pedido (id_compra, id_pedido, id_atributo, id_producto, sku, cantidad, precio) VALUES 
(1, 1, 1, 1, 'SKU001', 2, 600.25), 
(2, 1, 2, 1, 'SKU002', 1, 300.00), 
(3, 1, 3, 2, 'SKU003', 1, 750.00), 
(4, 1, 4, 2, 'SKU004', 3, 116.92),
(5, 2, 5, 3, 'SKU005', 2, 900.00), 
(6, 2, 6, 3, 'SKU006', 1, 600.00), 
(7, 3, 7, 4, 'SKU007', 1, 300.00), 
(8, 3, 8, 4, 'SKU008', 2, 400.00), 
(9, 4, 9, 5, 'SKU009', 1, 500.00), 
(10, 4, 10, 5, 'SKU010', 3, 1200.00), 
(11, 5, 11, 6, 'SKU011', 1, 600.00), 
(12, 5, 12, 6, 'SKU012', 2, 150.00), 
(13, 6, 13, 7, 'SKU013', 1, 700.00), 
(14, 6, 14, 8, 'SKU014', 1, 900.00), 
(15, 7, 15, 9, 'SKU015', 2, 140.00), 
(16, 7, 16, 9, 'SKU016', 3, 600.00), 
(17, 8, 17, 10, 'SKU017', 1, 800.00), 
(18, 8, 18, 11, 'SKU018', 2, 500.00), 
(19, 9, 19, 12, 'SKU019', 1, 200.00), 
(20, 9, 20, 13, 'SKU020', 3, 900.00), 
(21, 10, 21, 14, 'SKU021', 1, 400.00), 
(22, 10, 22, 15, 'SKU022', 2, 500.00), 
(23, 11, 23, 16, 'SKU023', 1, 600.00), 
(24, 11, 24, 17, 'SKU024', 3, 500.00), 
(25, 12, 25, 18, 'SKU025', 2, 300.00),
(26, 12, 26, 19, 'SKU026', 1, 150.00),
(27, 13, 27, 20, 'SKU027', 2, 450.00),
(28, 13, 28, 21, 'SKU028', 1, 500.00),
(29, 14, 29, 22, 'SKU029', 3, 600.00),
(30, 14, 30, 23, 'SKU030', 2, 700.00),
(31, 15, 31, 24, 'SKU031', 1, 800.00),
(32, 15, 1, 1, 'SKU001', 2, 900.00),
(33, 16, 2, 2, 'SKU002', 1, 1000.00),
(34, 16, 3, 3, 'SKU003', 3, 1100.00),
(35, 17, 4, 4, 'SKU004', 2, 1200.00),
(36, 17, 5, 5, 'SKU005', 1, 1300.00),
(37, 18, 6, 6, 'SKU006', 3, 1400.00),
(38, 18, 7, 7, 'SKU007', 2, 1500.00),
(39, 19, 8, 8, 'SKU008', 1, 1600.00),
(40, 19, 9, 9, 'SKU009', 3, 1700.00),
(41, 20, 10, 10, 'SKU010', 2, 1800.00),
(42, 20, 11, 11, 'SKU011', 1, 1900.00);



--consultas:
-- Select all data from productos along with the category name
SELECT p.*, c.nombre AS categoria
FROM productos p
JOIN categorias c ON p.id_categoria = c.id_categoria;

-- Select all attributes of a specific product
SELECT p.nombre AS producto, a.sku, ep.nombre AS estado_producto, c.nombre AS color, t.nombre AS tamaño, ar.nombre AS aroma
FROM atributo a
JOIN productos p ON a.id_producto = p.id_producto
JOIN estado_producto ep ON a.id_estado_producto = ep.id_estado_producto
JOIN color c ON a.id_color = c.id_color
JOIN tamaño t ON a.id_tamaño = t.id_tamaño
JOIN aromas ar ON a.id_aroma = ar.id_aroma
WHERE p.id_producto = 1;



-- Crear tabla tipo_precio
CREATE TABLE tipo_precio (
    id_tipo_precio INT PRIMARY KEY,
    nombre VARCHAR(100),
    descripcion TEXT
);

-- Crear tabla atributo_tipo_precio
CREATE TABLE atributo_tipo_precio (
    id_atributo INT,
    id_tipo_precio INT,
    precio DECIMAL(10, 2),
    cantidad_minima INT,
    PRIMARY KEY (id_atributo, id_tipo_precio),
    CONSTRAINT FK_atributo_tipo_precio_id_atributo FOREIGN KEY (id_atributo) REFERENCES atributo(id_atributo),
    CONSTRAINT FK_atributo_tipo_precio_id_tipo_precio FOREIGN KEY (id_tipo_precio) REFERENCES tipo_precio(id_tipo_precio)
);



INSERT INTO tipo_precio (id_tipo_precio, nombre, descripcion) VALUES 
(1, 'Precio Minorista', 'Precio para ventas al por menor'),
(2, 'Precio Mayorista', 'Precio para ventas al por mayor');

-- Insertar datos en atributo_tipo_precio
INSERT INTO atributo_tipo_precio (id_atributo, id_tipo_precio, precio) VALUES 
(1, 1, 600.25), 
(2, 1, 300.00), 
(3, 1, 750.00), 
(4, 1, 116.92),
(5, 1, 900.00), 
(6, 1, 600.00), 
(7, 1, 300.00), 
(8, 1, 400.00), 
(9, 1, 500.00), 
(10, 1, 1200.00), 
(11, 1, 600.00), 
(12, 1, 150.00), 
(13, 1, 700.00), 
(14, 1, 900.00), 
(15, 1, 140.00), 
(16, 1, 600.00), 
(17, 1, 800.00), 
(18, 1, 500.00), 
(19, 1, 200.00), 
(20, 1, 900.00), 
(21, 1, 400.00), 
(22, 1, 500.00), 
(23, 1, 600.00), 
(24, 1, 500.00), 
(25, 1, 300.00),
(26, 1, 150.00),
(27, 1, 450.00),
(28, 1, 500.00),
(29, 1, 600.00),
(30, 1, 700.00),
(31, 1, 800.00),
(32, 1, 900.00),
(33, 1, 1000.00),
(34, 1, 1100.00),
(35, 1, 1200.00),
(36, 1, 1300.00),
(37, 1, 1400.00),
(38, 1, 1500.00),
(39, 1, 1600.00),
(40, 1, 1700.00),
(41, 1, 1800.00),
(42, 1, 1900.00);



--ver tablas: 
-- Select all data from categorias
SELECT * FROM categorias;

-- Select all data from aromas
SELECT * FROM aromas;

-- Select all data from color
SELECT * FROM color;

-- Select all data from tamaño
SELECT * FROM tamaño;

-- Select all data from productos
SELECT * FROM productos;

-- Select all data from imagenes
SELECT * FROM imagenes;

-- Select all data from estado_producto
SELECT * FROM estado_producto;

-- Select all data from atributo
SELECT * FROM atributo;

-- Select all data from pedidos
SELECT * FROM pedidos;

-- Select all data from productos_pedido
SELECT * FROM productos_pedido;

-- Select all data from permisos
SELECT * FROM permisos;

-- Select all data from estado_usuario
SELECT * FROM estado_usuario;

-- Select all data from usuario
SELECT * FROM usuario;

-- Select all data from sexo
SELECT * FROM sexo;

-- Select all data from info_usuario
SELECT * FROM info_usuario;

-- Select all data from domicilio
SELECT * FROM domicilio;

-- Select all data from usuario_domicilio
SELECT * FROM usuario_domicilio;
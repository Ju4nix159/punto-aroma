DELETE FROM productos;
DELETE FROM productos_pedido;
DELETE FROM pedidos;
DELETE FROM fragancias;
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


DROP TABLE IF EXISTS productos_pedido;
DROP TABLE IF EXISTS pedidos;
DROP TABLE IF EXISTS fragancias;
DROP TABLE IF EXISTS imagenes;
DROP TABLE IF EXISTS usuario_domicilio;
DROP TABLE IF EXISTS domicilio;
DROP TABLE IF EXISTS info_usuario;
DROP TABLE IF EXISTS usuario;
DROP TABLE IF EXISTS estado_producto;
DROP TABLE IF EXISTS productos;
DROP TABLE IF EXISTS categorias;
DROP TABLE IF EXISTS permisos;
DROP TABLE IF EXISTS estado_usuario;
DROP TABLE IF EXISTS sexo;


-- Tabla de categorías
CREATE TABLE categorias (
    id_categoria INT PRIMARY KEY,
    nombre VARCHAR(100),
    descripcion TEXT
);

-- Tabla de productos
CREATE TABLE productos (
    id_producto INT PRIMARY KEY,
    n_producto VARCHAR(100),
    nombre VARCHAR(100),
    descripcion TEXT,
    id_categoria INT,
    precio DECIMAL(10, 2),
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

-- Tabla de fragancias (variante del producto)
CREATE TABLE fragancias (
    id_fragancia INT PRIMARY KEY,
    id_producto INT,
    id_estado_producto INT,
    nombre VARCHAR(100),
    stock INT,
    sku VARCHAR(100),
    CONSTRAINT FK_fragancias_id_producto_END FOREIGN KEY (id_producto) REFERENCES productos(id_producto),
    CONSTRAINT FK_fragancias_id_estado_producto_END FOREIGN KEY (id_estado_producto) REFERENCES estado_producto(id_estado_producto)
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
    id_fragancia INT,
    id_producto INT,
    cantidad INT,
    precio DECIMAL(10, 2),
    CONSTRAINT FK_productos_pedido_id_pedido_END FOREIGN KEY (id_pedido) REFERENCES pedidos(id_pedido),
    CONSTRAINT FK_productos_pedido_id_fragancia_END FOREIGN KEY (id_fragancia) REFERENCES fragancias(id_fragancia),
    CONSTRAINT FK_productos_pedido_id_producto_END FOREIGN KEY (id_producto) REFERENCES productos(id_producto)
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
INSERT INTO productos (id_producto, n_producto, nombre, descripcion, id_categoria, precio) VALUES 
(1, 'PRD001', 'Perfume Floral', 'Un perfume con fragancias florales', 1, 25.50),
(2, 'PRD002', 'Perfume Amaderado', 'Un perfume con fragancia amaderada', 1, 30.00),
(3, 'PRD003', 'Difusor de Vainilla', 'Un difusor con aroma a vainilla', 2, 15.75),
(4, 'PRD004', 'Vela Aromática', 'Una vela con aroma a lavanda', 2, 12.50),
(5, 'PRD005', 'Perfume Cítrico', 'Un perfume con fragancias cítricas', 1, 28.00),
(6, 'PRD006', 'Perfume Dulce', 'Un perfume con un toque dulce', 1, 22.40),
(7, 'PRD007', 'Difusor de Eucalipto', 'Un difusor con aroma a eucalipto', 2, 18.90),
(8, 'PRD008', 'Vela Aromática Coco', 'Una vela con aroma a coco', 2, 14.30),
(9, 'PRD009', 'Perfume Deportivo', 'Un perfume ideal para actividades deportivas', 1, 27.50),
(10, 'PRD010', 'Perfume de Noche', 'Un perfume ideal para la noche', 1, 35.00),
(11, 'PRD011', 'Difusor de Frutos Rojos', 'Un difusor con aroma a frutos rojos', 2, 16.20),
(12, 'PRD012', 'Vela Aromática Canela', 'Una vela con aroma a canela', 2, 13.50),
(13, 'PRD013', 'Perfume de Viaje', 'Un perfume ideal para llevar en viajes', 1, 20.00),
(14, 'PRD014', 'Perfume de Verano', 'Un perfume fresco para el verano', 1, 25.00),
(15, 'PRD015', 'Difusor de Lavanda', 'Un difusor con aroma a lavanda', 2, 17.50),
(16, 'PRD016', 'Vela Aromática Limón', 'Una vela con aroma a limón', 2, 12.00),
(17, 'PRD017', 'Perfume de Otoño', 'Un perfume cálido para el otoño', 1, 29.00),
(18, 'PRD018', 'Perfume de Invierno', 'Un perfume suave para el invierno', 1, 24.50),
(19, 'PRD019', 'Difusor de Té Verde', 'Un difusor con aroma a té verde', 2, 19.00),
(20, 'PRD020', 'Vela Aromática Jazmín', 'Una vela con aroma a jazmín', 2, 15.00),
(21, 'PRD021', 'Perfume Exótico', 'Un perfume con fragancias exóticas', 1, 32.00),
(22, 'PRD022', 'Perfume Clásico', 'Un perfume con un toque clásico', 1, 26.50),
(23, 'PRD023', 'Difusor de Menta', 'Un difusor con aroma a menta', 2, 18.00),
(24, 'PRD024', 'Vela Aromática Naranja', 'Una vela con aroma a naranja', 2, 14.75);

INSERT INTO fragancias (id_fragancia, id_producto, id_estado_producto, nombre, stock, sku) VALUES 
(1, 1, 1, 'Fragancia Floral Suave', 50, 'SKU001'),
(2, 1, 1, 'Fragancia Floral Intensa', 30, 'SKU002'),
(3, 2, 1, 'Fragancia Amaderada Suave', 20, 'SKU003'),
(4, 2, 2, 'Fragancia Amaderada Intensa', 0, 'SKU004'),
(5, 3, 1, 'Difusor Vainilla Pequeño', 15, 'SKU005'),
(6, 3, 1, 'Difusor Vainilla Grande', 10, 'SKU006'),
(7, 4, 1, 'Vela Aromática Lavanda', 40, 'SKU007'),
(8, 4, 1, 'Vela Aromática Lavanda XL', 25, 'SKU008'),
(9, 5, 1, 'Fragancia Cítrica Fresca', 45, 'SKU009'),
(10, 5, 1, 'Fragancia Cítrica Intensa', 30, 'SKU010'),
(11, 6, 1, 'Fragancia Dulce Suave', 50, 'SKU011'),
(12, 6, 1, 'Fragancia Dulce Fuerte', 20, 'SKU012'),
(13, 7, 1, 'Difusor Eucalipto Pequeño', 10, 'SKU013'),
(14, 8, 1, 'Vela Coco Natural', 15, 'SKU014'),
(15, 9, 1, 'Fragancia Deportiva Suave', 25, 'SKU015'),
(16, 9, 1, 'Fragancia Deportiva Intensa', 20, 'SKU016'),
(17, 10, 1, 'Fragancia Noche Suave', 30, 'SKU017'),
(18, 11, 1, 'Difusor Frutos Rojos Pequeño', 15, 'SKU018'),
(19, 12, 1, 'Vela Canela Clásica', 18, 'SKU019'),
(20, 13, 1, 'Fragancia Viaje Compacta', 12, 'SKU020'),
(21, 14, 1, 'Fragancia Verano Fresco', 40, 'SKU021'),
(22, 15, 1, 'Difusor Lavanda Natural', 30, 'SKU022'),
(23, 16, 1, 'Vela Limón Refrescante', 25, 'SKU023'),
(24, 17, 1, 'Fragancia Otoño Cálido', 35, 'SKU024'),
(25, 18, 1, 'Fragancia Invierno Suave', 28, 'SKU025'),
(26, 19, 1, 'Difusor Té Verde Natural', 22, 'SKU026'),
(27, 20, 1, 'Vela Jazmín Floral', 18, 'SKU027'),
(28, 21, 1, 'Fragancia Exótica', 50, 'SKU028'),
(29, 22, 1, 'Fragancia Clásica Suave', 40, 'SKU029'),
(30, 23, 1, 'Difusor Menta Fresca', 10, 'SKU030'),
(31, 24, 1, 'Vela Naranja Dulce', 15, 'SKU031');


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
INSERT INTO productos_pedido (id_compra, id_pedido, id_fragancia, id_producto, cantidad, precio) VALUES 
(1, 1, 1, 1, 2, 600.25), 
(2, 1, 3, 2, 1, 300.00), 
(3, 2, 5, 3, 1, 750.00), 
(4, 3, 7, 4, 3, 116.92),
(5, 4, 1, 5, 2, 900.00), 
(6, 4, 9, 6, 1, 600.00), 
(7, 5, 12, 7, 1, 300.00), 
(8, 6, 15, 8, 2, 400.00), 
(9, 7, 18, 9, 1, 500.00), 
(10, 8, 21, 10, 3, 1200.00), 
(11, 9, 24, 11, 1, 600.00), 
(12, 10, 27, 12, 2, 150.00), 
(13, 11, 30, 13, 1, 700.00), 
(14, 12, 1, 14, 1, 900.00), 
(15, 13, 1, 15, 2, 140.00), 
(16, 14, 1, 16, 3, 600.00), 
(17, 15, 1, 17, 1, 800.00), 
(18, 16, 1, 18, 2, 500.00), 
(19, 17, 1, 19, 1, 200.00), 
(20, 18, 1, 20, 3, 900.00), 
(21, 19, 1, 21, 1, 400.00), 
(22, 20, 1, 22, 2, 500.00), 
(23, 21, 1, 23, 1, 600.00), 
(24, 22, 1, 24, 3, 500.00), 
(25, 23, 1, 5, 2, 300.00);

-- Select all data from productos along with the category name
SELECT p.*, c.nombre AS categoria
FROM productos p
JOIN categorias c ON p.id_categoria = c.id_categoria;
-- Select all data from categorias
SELECT * FROM categorias;

-- Select all data from productos
SELECT * FROM productos;

-- Select all data from imagenes
SELECT * FROM imagenes;

-- Select all data from estado_producto
SELECT * FROM estado_producto;

-- Select all data from fragancias
SELECT * FROM fragancias;

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





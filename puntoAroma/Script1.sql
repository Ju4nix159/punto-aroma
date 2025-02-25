CREATE TABLE banner (
  id_banner int(11) NOT NULL AUTO_INCREMENT,
    nombre varchar(100) DEFAULT NULL,
    ruta varchar(200) DEFAULT NULL,
    descripcion varchar(200) DEFAULT NULL,
    id_pagina int(11) DEFAULT NULL,
    PRIMARY KEY (id_banner)
  );

  CREATE TABLE categorias (
    id_categoria int(11) NOT NULL AUTO_INCREMENT,
    nombre varchar(100) DEFAULT NULL,
    descripcion text DEFAULT NULL,
    estado tinyint(1) NOT NULL DEFAULT 1,
    destacado tinyint(1) NOT NULL DEFAULT 0,
    PRIMARY KEY (id_categoria)
  );

  CREATE TABLE detalle_metodo_pago (
    id_detalle int(11) NOT NULL AUTO_INCREMENT,
    banco varchar(100) DEFAULT NULL,
    cbu varchar(100) DEFAULT NULL,
    alias varchar(100) DEFAULT NULL,
    titular varchar(100) DEFAULT NULL,
    PRIMARY KEY (id_detalle)
  );

  CREATE TABLE domicilios (
    id_domicilio int(11) NOT NULL AUTO_INCREMENT,
    codigo_postal varchar(10) DEFAULT NULL,
    provincia varchar(100) DEFAULT NULL,
    localidad varchar(100) DEFAULT NULL,
    barrio varchar(100) DEFAULT NULL,
    calle varchar(100) DEFAULT NULL,
    numero varchar(10) DEFAULT NULL,
    informacion_adicional text DEFAULT NULL,
    piso int(10) DEFAULT NULL,
    departamento varchar(10) DEFAULT NULL,
    PRIMARY KEY (id_domicilio)
  );

  CREATE TABLE estados_pedidos (
    id_estado_pedido int(11) NOT NULL AUTO_INCREMENT,
    nombre varchar(100) DEFAULT NULL,
    descripcion text DEFAULT NULL,
    PRIMARY KEY (id_estado_pedido)
  );

  CREATE TABLE estados_productos (
    id_estado_producto int(11) NOT NULL AUTO_INCREMENT,
    nombre varchar(100) DEFAULT NULL,
    descripcion text DEFAULT NULL,
    PRIMARY KEY (id_estado_producto)
  );

  CREATE TABLE estados_usuarios (
    id_estado_usuario int(11) NOT NULL AUTO_INCREMENT,
    nombre varchar(100) DEFAULT NULL,
    descripcion text DEFAULT NULL,
    PRIMARY KEY (id_estado_usuario)
  );

  CREATE TABLE sexos (
    id_sexo int(11) NOT NULL AUTO_INCREMENT,
    nombre varchar(100) DEFAULT NULL,
    descripcion text DEFAULT NULL,
    PRIMARY KEY (id_sexo)
  );

  CREATE TABLE permisos (
    id_permiso int(11) NOT NULL AUTO_INCREMENT,
    nombre varchar(100) DEFAULT NULL,
    descripcion text DEFAULT NULL,
    PRIMARY KEY (id_permiso)
  );

  CREATE TABLE tipos_precios (
    id_tipo_precio int(11) NOT NULL AUTO_INCREMENT,
    nombre varchar(100) DEFAULT NULL,
    descripcion text DEFAULT NULL,
    PRIMARY KEY (id_tipo_precio)
  );

  CREATE TABLE locales (
    id_local int(11) NOT NULL AUTO_INCREMENT,
    provincia varchar(200) NOT NULL,
    localidad varchar(200) NOT NULL,
    calle varchar(200) NOT NULL,
    numero int(100) NOT NULL,
    codigo_postal varchar(100) NOT NULL,
    nombre varchar(100) NOT NULL,
    PRIMARY KEY (id_local)
  );

  CREATE TABLE marcas (
    id_marca int(11) NOT NULL AUTO_INCREMENT,
    nombre varchar(100) DEFAULT NULL,
    estado tinyint(1) NOT NULL DEFAULT 1,
    PRIMARY KEY (id_marca)
  );

  CREATE TABLE productos (
    id_producto int(11) NOT NULL AUTO_INCREMENT,
    nombre varchar(200) DEFAULT NULL,
    descripcion text DEFAULT NULL,
    id_categoria int(11) DEFAULT NULL,
    id_marca int(11) DEFAULT NULL,
    id_submarca int(11) DEFAULT NULL,
    destacado tinyint(4) DEFAULT 0,
    estado tinyint(4) DEFAULT 1,
    decuento decimal(10,2) DEFAULT 0.00,
    unico tinyint(1) NOT NULL DEFAULT 0,
    peso float(10,2) DEFAULT NULL,
    alto float(10,2) DEFAULT NULL,
    ancho float(10,2) DEFAULT NULL,
    profundo float(10,2) DEFAULT NULL,
    PRIMARY KEY (id_producto),
    CONSTRAINT FK_productos_id_categoria_END FOREIGN KEY (id_categoria) REFERENCES categorias (id_categoria),
    CONSTRAINT FK_productos_id_marca_END FOREIGN KEY (id_marca) REFERENCES marcas (id_marca),
    CONSTRAINT fk_submarca_marca FOREIGN KEY (id_submarca) REFERENCES marcas (id_marca)
  );

  CREATE TABLE imagenes (
    id_imagen int(11) NOT NULL AUTO_INCREMENT,
    id_producto int(11) DEFAULT NULL,
    nombre varchar(200) DEFAULT NULL,
    principal tinyint(4) DEFAULT 1,
    PRIMARY KEY (id_imagen),
    CONSTRAINT FK_imagenes_id_producto_END FOREIGN KEY (id_producto) REFERENCES productos (id_producto)
  );

  CREATE TABLE variantes (
    sku varchar(100) NOT NULL,
    nombre_variante varchar(100) DEFAULT NULL,
    id_producto int(11) DEFAULT NULL,
    id_estado_producto int(11) DEFAULT 1,
    aroma varchar(100) DEFAULT NULL,
    color varchar(100) DEFAULT NULL,
    titulo varchar(200) DEFAULT NULL,
    stock int(11) DEFAULT NULL,
    PRIMARY KEY (sku),
    CONSTRAINT FK_variante_id_producto_END FOREIGN KEY (id_producto) REFERENCES productos (id_producto),
    CONSTRAINT FK_variante_id_estado_producto_END FOREIGN KEY (id_estado_producto) REFERENCES estados_productos (id_estado_producto)
  );

  CREATE TABLE variantes_tipo_precio (
    id_producto int(11) NOT NULL,
    id_tipo_precio int(11) NOT NULL,
    precio decimal(10,2) DEFAULT NULL,
    cantidad_minima int(11) DEFAULT NULL,
    PRIMARY KEY (id_tipo_precio, id_producto),
    CONSTRAINT FK_variante_tipo_precio_sku FOREIGN KEY (id_producto) REFERENCES productos (id_producto),
    CONSTRAINT FK_variante_tipo_precio_id_tipo_precio FOREIGN KEY (id_tipo_precio) REFERENCES tipos_precios (id_tipo_precio)
  );

  CREATE TABLE usuarios (
    id_usuario int(11) NOT NULL AUTO_INCREMENT,
    id_permiso int(11) DEFAULT 2,
    id_estado_usuario int(11) DEFAULT 1,
    email varchar(100) DEFAULT NULL,
    clave varchar(100) DEFAULT NULL,
    PRIMARY KEY (id_usuario),
    CONSTRAINT FK_usuario_id_permiso_END FOREIGN KEY (id_permiso) REFERENCES permisos (id_permiso),
    CONSTRAINT FK_usuario_id_estado_usuario_END FOREIGN KEY (id_estado_usuario) REFERENCES estados_usuarios (id_estado_usuario)
  );

  CREATE TABLE info_usuarios (
    id_info_usuario int(11) NOT NULL AUTO_INCREMENT,
    id_usuario int(11) DEFAULT NULL,
    id_sexo int(11) DEFAULT NULL,
    nombre varchar(100) DEFAULT NULL,
    apellido varchar(100) DEFAULT NULL,
    dni varchar(20) DEFAULT NULL,
    fecha_nacimiento date DEFAULT NULL,
    telefono varchar(20) DEFAULT NULL,
    PRIMARY KEY (id_info_usuario),
    CONSTRAINT FK_info_usuario_id_usuario_END FOREIGN KEY (id_usuario) REFERENCES usuarios (id_usuario),
    CONSTRAINT FK_info_usuario_id_sexo_END FOREIGN KEY (id_sexo) REFERENCES sexos (id_sexo)
  );

  CREATE TABLE usuario_domicilios (
    id_usuario int(11) NOT NULL,
    id_domicilio int(11) NOT NULL,
    tipo_domicilio varchar(100) DEFAULT NULL,
    principal tinyint(4) DEFAULT 0,
    estado tinyint(4) DEFAULT 1,
    PRIMARY KEY (id_usuario, id_domicilio),
    CONSTRAINT FK_usuario_domicilio_id_usuario_END FOREIGN KEY (id_usuario) REFERENCES usuarios (id_usuario),
    CONSTRAINT FK_usuario_domicilio_id_domicilio_END FOREIGN KEY (id_domicilio) REFERENCES domicilios (id_domicilio)
  );

  CREATE TABLE metodos_pago (
    id_metodo_pago int(11) NOT NULL AUTO_INCREMENT,
    nombre_metodo_pago varchar(100) DEFAULT NULL,
    descripcion_metodo_pago varchar(200) DEFAULT NULL,
    id_detalle int(11) DEFAULT NULL,
    PRIMARY KEY (id_metodo_pago),
    CONSTRAINT FK_metodos_pago_id_detalle_END FOREIGN KEY (id_detalle) REFERENCES detalle_metodo_pago (id_detalle)
  );

  CREATE TABLE pedidos (
    id_pedido int(11) NOT NULL AUTO_INCREMENT,
    id_usuario int(11) DEFAULT NULL,
    id_estado_pedido int(11) DEFAULT 1,
    total decimal(10,2) DEFAULT NULL,
    fecha date DEFAULT NULL,
    id_domicilio int(11) DEFAULT NULL,
    estado_seña tinyint(4) DEFAULT 0,
    envio float(10,2) NOT NULL DEFAULT 0.00,
    id_local int(11) DEFAULT NULL,
    PRIMARY KEY (id_pedido),
    CONSTRAINT FK_pedidos_id_usuario_END FOREIGN KEY (id_usuario) REFERENCES usuarios (id_usuario),
    CONSTRAINT FK_pedidos_id_estado_pedido_END FOREIGN KEY (id_estado_pedido) REFERENCES estados_pedidos (id_estado_pedido),
    CONSTRAINT FK_pedidos_id_domicilio_END FOREIGN KEY (id_domicilio) REFERENCES domicilios (id_domicilio),
    CONSTRAINT fk_id_local FOREIGN KEY (id_local) REFERENCES locales (id_local)
  );

  CREATE TABLE productos_pedido (
    id_pedido int(11) DEFAULT NULL,
    id_producto int(11) DEFAULT NULL,
    sku varchar(100) DEFAULT NULL,
    cantidad int(11) DEFAULT NULL,
    precio decimal(10,2) DEFAULT NULL,
    estado tinyint(4) DEFAULT 1,
    CONSTRAINT FK_productos_pedido_id_pedido_END FOREIGN KEY (id_pedido) REFERENCES pedidos (id_pedido),
    CONSTRAINT FK_productos_pedido_sku_END FOREIGN KEY (sku) REFERENCES variantes (sku)
  );

  CREATE TABLE pagos (
    id_pago int(11) NOT NULL AUTO_INCREMENT,
    id_pedido int(11) DEFAULT NULL,
    id_metodo_pago int(11) DEFAULT NULL,
    comprobante varchar(100) DEFAULT NULL,
    monto decimal(10,2) DEFAULT NULL,
    fecha date DEFAULT NULL,
    descripcion varchar(200) DEFAULT NULL,
    PRIMARY KEY (id_pago),
    CONSTRAINT FK_pagos_id_pedido_END FOREIGN KEY (id_pedido) REFERENCES pedidos (id_pedido),
    CONSTRAINT FK_pagos_id_metodo_pago_END FOREIGN KEY (id_metodo_pago) REFERENCES metodos_pago (id_metodo_pago)
  );

  SELECT sku, 
      COALESCE(titulo, aroma, color) AS atributo
  FROM variantes
  WHERE id_producto = 1;

  SELECT id_producto, 
    COALESCE(titulo, aroma, color) AS atributo_no_null
  FROM variantes
  WHERE id_producto = 1;
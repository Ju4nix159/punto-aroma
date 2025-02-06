<?php
include './footer.php';
include './header.php';
include './aside.php';
include './config/sbd.php';




$sql_categorias = $con->prepare("SELECT * FROM categorias;");
$sql_categorias->execute();
$categorias = $sql_categorias->fetchAll(PDO::FETCH_ASSOC);

$sql_marcas = $con->prepare("SELECT * FROM marcas;");
$sql_marcas->execute();
$marcas = $sql_marcas->fetchAll(PDO::FETCH_ASSOC);




?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        .hover-image:hover {
            opacity: 0.5;
        }

        .hover-image:hover+.hover-text {
            display: block;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: white;
            font-weight: bold;
            background-color: rgba(0, 0, 0, 0.5);
            padding: 10px;
            border-radius: 5px;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <div class="content-wrapper">
            Content Header (Page header)
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h2>Añadir producto</h2>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="#">Inicio</a></li>
                                <li class="breadcrumb-item"><a href="#">Productos</a></li>
                                <li class="breadcrumb-item active">añadir producto</li>
                            </ol>
                        </div>
                    </div>
                </div><!-- /.container-fluid -->
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="card card-primary">
                                <div class="card-header">
                                    <h3 class="card-title">Información del Producto</h3>
                                </div>
                                <!-- /.card-header -->
                                <div class="card-body">
                                    <hr>
                                    <strong><i class="fas fa-box mr-1"></i> Nombre del Producto</strong>
                                    <input type="text" id="nombre-input" class="form-control" required>

                                    <hr>

                                    <strong><i class="fas fa-tag mr-1"></i> Marca</strong>
                                    <select id="marca-input" class="form-control" required>
                                        <?php foreach ($marcas as $marca) {  ?>
                                            <option value="<?php echo $marca['id_marca'] ?>"><?php echo $marca['nombre'] ?></option>
                                        <?php } ?>
                                    </select>

                                    <hr>

                                    <strong><i class="fas fa-tag mr-1"></i> Categoría</strong>
                                    <select id="categoria-input" class="form-control" required>
                                        <?php foreach ($categorias as $categoria) {  ?>
                                            <option value="<?php echo $categoria['id_categoria'] ?>"><?php echo $categoria['nombre'] ?></option>
                                        <?php } ?>
                                    </select>

                                    <hr>



                                    <strong><i class="fas fa-dollar-sign mr-1"></i> Precio Minorista</strong>
                                    <input type="number" id="precio_minorista-input" class="form-control" value="0" min="0" required>

                                    <hr>

                                    <strong><i class="fas fa-warehouse mr-1"></i> Precios Mayoristas</strong>
                                    <div id="precios-mayoristas-container">
                                        <div class="d-flex align-items-center mb-2">
                                            <input type="number" class="form-control precio-mayorista" value="0" min="0" placeholder="Precio" required>
                                            <input type="number" class="form-control ml-2 cantidad-minima" value="1" min="1" placeholder="Cantidad mínima" required>
                                            <button type="button" class="btn btn-danger ml-2 eliminar-precio" onclick="eliminarPrecio(this)" style="display: none;">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="form-check mb-3">
                                        <input type="checkbox" class="form-check-input" id="agregar-mas-precios">
                                        <label class="form-check-label" for="agregar-mas-precios">Agregar más precios mayoristas</label>
                                    </div>

                                    <button type="button" id="btn-agregar-precio" class="btn btn-secondary mb-3" style="display: none;">
                                        <i class="fas fa-plus"></i> Agregar otro precio
                                    </button>

                                    <hr>

                                    <strong><i class="fas fa-cubes mr-1"></i> Destacado</strong>
                                    <select id="destacado-input" class="form-control" required>
                                        <option value="1">Si</option>
                                        <option value="0">No</option>
                                    </select>

                                    <hr>

                                    <strong><i class="fas fa-align-left mr-1"></i> Descripción</strong>
                                    <textarea id="descripcion-input" class="form-control" required></textarea>
                                </div>


                                <!-- Botones -->
                                <div class="card-footer">
                                    <div class="row mt-4">
                                        <div class="col-12">
                                            <a href="./productos.php" id="volver-listado" class="btn btn-secondary">Volver al listado</a>
                                            <button type="button" id="boton-guardar" class="btn btn-success float-right" onclick="añadir_producto()">
                                                <i class="fas fa-save"></i> Guardar
                                            </button>

                                        </div>
                                    </div>
                                </div>

                                <!-- /.card-body -->
                            </div>
                            <!-- /.card -->
                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- /.row -->
                </div><!-- /.container-fluid -->
            </section>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->
    </div>


    <script>
        document.getElementById('agregar-mas-precios').addEventListener('change', function() {
            const btnAgregar = document.getElementById('btn-agregar-precio');
            const primeraFila = document.querySelector('.eliminar-precio');
            btnAgregar.style.display = this.checked ? 'block' : 'none';
            primeraFila.style.display = this.checked ? 'block' : 'none';
        });

        document.getElementById('btn-agregar-precio').addEventListener('click', function() {
            const container = document.getElementById('precios-mayoristas-container');
            const nuevaFila = document.createElement('div');
            nuevaFila.className = 'd-flex align-items-center mb-2';
            nuevaFila.innerHTML = `
        <input type="number" class="form-control precio-mayorista" value="0" min="0" placeholder="Precio" required>
        <input type="number" class="form-control ml-2 cantidad-minima" value="1" min="1" placeholder="Cantidad mínima" required>
        <button type="button" class="btn btn-danger ml-2 eliminar-precio" onclick="eliminarPrecio(this)">
            <i class="fas fa-trash"></i>
        </button>
    `;
            container.appendChild(nuevaFila);
        });

        function eliminarPrecio(button) {
            button.closest('.d-flex').remove();
        }

        function añadir_producto() {
            // Obtener todos los campos para añadir un producto
            const nombre = document.getElementById('nombre-input').value;
            const categoria = document.getElementById('categoria-input').value;
            const marca = document.getElementById('marca-input').value;
            const precio_minorista = parseFloat(document.getElementById('precio_minorista-input').value) || 0;
            const destacado = document.getElementById('destacado-input').value;
            const descripcion = document.getElementById('descripcion-input').value;

            // Validar campos obligatorios
            if (!nombre || !categoria || !descripcion || !marca) {
                alert('Por favor, completa todos los campos obligatorios.');
                return;
            }

            const preciosMayoristas = [];
            const filasPrecio = document.querySelectorAll('#precios-mayoristas-container .d-flex');

            filasPrecio.forEach(fila => {
                const precio = parseFloat(fila.querySelector('.precio-mayorista').value) || 0;
                const cantidad = parseInt(fila.querySelector('.cantidad-minima').value) || 1;

                if (precio > 0 && cantidad > 1) {
                    preciosMayoristas.push({
                        precio: precio,
                        cantidad_minima: cantidad
                    });
                }
            });
            if (preciosMayoristas.length > 0) {
                // Check if quantities are in ascending order
                for (let i = 1; i < preciosMayoristas.length; i++) {
                    if (preciosMayoristas[i].cantidad_minima <= preciosMayoristas[i - 1].cantidad_minima) {
                        alert('Las cantidades mínimas deben ser ascendentes.');
                        return;
                    }
                    if (preciosMayoristas[i].precio >= preciosMayoristas[i - 1].precio) {
                        alert('Los precios mayoristas deben ser descendentes.');
                        return;
                    }
                }

                // Check against retail price
                if (precio_minorista > 0 && preciosMayoristas[0].precio >= precio_minorista) {
                    alert('Los precios mayoristas deben ser menores que el precio minorista.');
                    return;
                }
            }


            // Crear el FormData
            const formData = new FormData();
            formData.append('nombre', nombre);
            formData.append('categoria', categoria);
            formData.append('marca', marca);
            formData.append('precio_minorista', precio_minorista);
            formData.append('precios_mayoristas', JSON.stringify(preciosMayoristas));
            formData.append('destacado', destacado);
            formData.append('descripcion', descripcion);

            // Enviar datos al servidor
            fetch('subir_producto.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Redirigir a la página del producto con el ID obtenido
                        const id_producto = data.id_producto;
                        alert('Producto añadido correctamente');
                        window.location.href = `producto.php?id_producto=${id_producto}`;
                    } else {
                        alert('Ocurrió un error al insertar el producto: ' + (data.message || 'Error desconocido'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Ocurrió un error al procesar la solicitud.');
                });
        }
    </script>
</body>


</html>
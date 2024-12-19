<?php

include './header.php';
include './aside.php';
include './footer.php';
include './config/sbd.php';

$sql_marcas = $con->prepare("SELECT * FROM marcas where estado = 1");
$sql_marcas->execute();
$marcas = $sql_marcas->fetchAll(PDO::FETCH_ASSOC);



?>



<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Administración de marcas - Punto Aroma</title>
</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1>Administración de Marcas</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="#">Inicio</a></li>
                                <li class="breadcrumb-item active">Marcas</li>
                            </ol>
                        </div>
                    </div>
                </div><!-- /.container-fluid -->
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Listado de Marcas</h3>
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-add-marca">
                                            <i class="fas fa-plus"></i> Agregar marca
                                        </button>
                                    </div>
                                </div>
                                <!-- /.card-header -->
                                <div class="card-body">
                                    <table id="categoriesTable" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Nombre</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($marcas as $marca) { ?>
                                                <tr>
                                                    <td><?php echo $marca['id_marca']; ?></td>
                                                    <td><?php echo $marca['nombre']; ?></td>
                                                    <td>
                                                        <button class="btn btn-primary btn-sm edit-marca" data-id="<?php echo $marca['id_marca']; ?>" onclick="editarMarca(<?php echo $marca['id_marca']; ?>)"><i class="fas fa-edit"></i> Editar</button>
                                                        <button class="btn btn-danger btn-sm delete-marca" data-id="<?php echo $marca['id_marca']; ?>" onclick="eliminarMarca(<?php echo $marca['id_marca']; ?>)"><i class="fas fa-trash"></i> Eliminar</button>
                                                    </td>
                                                </tr>
                                            <?php }; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <!-- /.card-body -->
                            </div>
                            <!-- /.card -->
                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- /.row -->
                </div>
                <!-- /.container-fluid -->
            </section>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->
    </div>
    <!-- ./wrapper -->

    <!-- Modal para agregar marca -->
    <div class="modal fade" id="modal-add-marca">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Agregar Nueva marca</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="addmarcaForm">
                        <div class="form-group">
                            <label for="marcaName">Nombre de la marca</label>
                            <input type="text" class="form-control" id="marcaName" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" onclick="agregarMarca()">Guardar marca</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->

    <div class="modal fade" id="modalEditarMarca" tabindex="-1" aria-labelledby="modalEditarMarcaLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditarMarcaLabel">Editar Marca</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formEditarMarca">
                        <div class="mb-3">
                            <label for="nombreMarca" class="form-label">Nombre de la Marca</label>
                            <input type="text" class="form-control" id="nombreMarca" name="nombreMarca" required>
                        </div>
                        <input type="hidden" id="idMarca" name="idMarca">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" onclick="guardarCambiosMarca()">Guardar cambios</button>
                </div>
            </div>
        </div>
    </div>



    <script>
        $(function() {
            // Inicializar DataTable
            const table = $("#categoriesTable").DataTable({
                "responsive": true,
                "lengthChange": true,
                "autoWidth": true,
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json"
                }
            });
        });

        // Agregar marca
        function agregarMarca() {
            const categoriaNombre = document.getElementById('marcaName').value;

            console.log(categoriaNombre);

            const data = new FormData();
            data.append('nombre', categoriaNombre);

            fetch('añadir_marca.php', {
                    method: 'POST',
                    body: data
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        alert('marca agregada correctamente');
                        location.reload();
                    } else {
                        alert('Ocurrió un error al agregar la marca');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }

        function eliminarMarca(id) {
            if (confirm("¿Estás seguro de que deseas eliminar esta marca?")) {
                // Crear una instancia de FormData
                console.log(id);
                const formData = new FormData();
                formData.append('id_marca', id);

                // Enviar una solicitud AJAX al backend
                fetch('eliminar_marca.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert("marca eliminada correctamente");
                            location.reload(); // Recargar la página para actualizar la lista
                        } else {
                            alert("Error al eliminar la marca: " + data.error);
                        }
                    })
                    .catch(error => {
                        console.error("Error:", error);
                        alert("Ocurrió un error al intentar eliminar la marca.");
                    });
            }
        }

        function editarMarca(id) {
            // Enviar solicitud al backend para obtener datos de la marca
            fetch(`obtener_marca.php?id_marca=${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Llenar el modal con los datos de la marca
                        document.getElementById('idMarca').value = data.marca.id_marca;
                        document.getElementById('nombreMarca').value = data.marca.nombre;

                        // Abrir el modal
                        const modal = new bootstrap.Modal(document.getElementById('modalEditarMarca'));
                        modal.show();
                    } else {
                        alert("Error al obtener la información de la marca: " + data.error);
                    }
                })
                .catch(error => {
                    console.error("Error:", error);
                    alert("Ocurrió un error al intentar obtener la información de la marca.");
                });
        }

        function guardarCambiosMarca() {
            const form = document.getElementById('formEditarMarca');
            const formData = new FormData(form);

            fetch('editar_marca.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert("Marca actualizada correctamente.");
                        location.reload(); // Recargar la página para reflejar los cambios
                    } else {
                        alert("Error al actualizar la marca: " + data.error);
                    }
                })
                .catch(error => {
                    console.error("Error:", error);
                    alert("Ocurrió un error al intentar guardar los cambios.");
                });
        }
    </script>
</body>

</html>
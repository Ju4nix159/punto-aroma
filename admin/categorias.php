<?php

include './header.php';
include './aside.php';
include './footer.php';
include './config/sbd.php';

$sql_categorias = $con->prepare("SELECT * FROM categorias where estado = 1");
$sql_categorias->execute();
$categorias = $sql_categorias->fetchAll(PDO::FETCH_ASSOC);



?>



<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Administración de Categorías - Punto Aroma</title>
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
                            <h1>Administración de Categorías</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="#">Inicio</a></li>
                                <li class="breadcrumb-item active">Categorías</li>
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
                                    <h3 class="card-title">Listado de Categorías</h3>
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-add-category">
                                            <i class="fas fa-plus"></i> Agregar Categoría
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
                                                <th>Descripción</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($categorias as $categoria) { ?>
                                                <tr>
                                                    <td><?php echo $categoria['id_categoria']; ?></td>
                                                    <td><?php echo $categoria['nombre']; ?></td>
                                                    <td><?php echo $categoria['descripcion']; ?></td>
                                                    <td>
                                                        <button class="btn btn-primary btn-sm edit-category" data-id="<?php echo $categoria['id_categoria']; ?>"><i class="fas fa-edit"></i> Editar</button>
                                                        <button class="btn btn-danger btn-sm delete-category" data-id="<?php echo $categoria['id_categoria']; ?>" onclick="deleteCategory(<?php echo $categoria['id_categoria']; ?>)"><i class="fas fa-trash"></i> Eliminar</button>
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

    <!-- Modal para agregar categoría -->
    <div class="modal fade" id="modal-add-category">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Agregar Nueva Categoría</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="addCategoryForm">
                        <div class="form-group">
                            <label for="categoryName">Nombre de la Categoría</label>
                            <input type="text" class="form-control" id="categoryName" required>
                        </div>
                        <div class="form-group">
                            <label for="categoryDescription">Descripción</label>
                            <textarea class="form-control" id="categoryDescription" rows="3"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" onclick="addCategory()">Guardar Categoría</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->

    <!-- Modal para editar categoría -->
    <div class="modal fade" id="editCategoryModal" tabindex="-1" aria-labelledby="editCategoryModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editCategoryModalLabel">Editar Categoría</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editCategoryForm">
                        <input type="hidden" id="editCategoryId" name="id_categoria">
                        <div class="mb-3">
                            <label for="editCategoryName" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="editCategoryName" name="nombre" required>
                        </div>
                        <div class="mb-3">
                            <label for="editCategoryDescription" class="form-label">Descripción</label>
                            <textarea class="form-control" id="editCategoryDescription" name="descripcion" rows="3" required></textarea>
                        </div>
                        <button type="button" class="btn btn-primary" id="saveCategoryChanges">Guardar Cambios</button>
                    </form>
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

        // Agregar categoría
        function addCategory() {
            const categoriaNombre = document.getElementById('categoryName').value;
            const categoriaDescripcion = document.getElementById('categoryDescription').value;

            console.log(categoriaNombre, categoriaDescripcion);

            const data = new FormData();
            data.append('nombre', categoriaNombre);
            data.append('descripcion', categoriaDescripcion);

            fetch('añadir_categoria.php', {
                    method: 'POST',
                    body: data
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        alert('Categoría agregada correctamente');
                        location.reload();
                    } else {
                        alert('Ocurrió un error al agregar la categoría');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }

        function deleteCategory(id) {
            if (confirm("¿Estás seguro de que deseas eliminar esta categoría?")) {
                // Crear una instancia de FormData
                const formData = new FormData();
                formData.append('id_categoria', id);

                // Enviar una solicitud AJAX al backend
                fetch('eliminar_categoria.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert("Categoría eliminada correctamente");
                            location.reload(); // Recargar la página para actualizar la lista
                        } else {
                            alert("Error al eliminar la categoría: " + data.error);
                        }
                    })
                    .catch(error => {
                        console.error("Error:", error);
                        alert("Ocurrió un error al intentar eliminar la categoría.");
                    });
            }
        }

        $(document).ready(function() {
            // Abrir modal y cargar datos de la categoría seleccionada
            $('.edit-category').on('click', function() {
                var categoryId = $(this).data('id');

                // Obtener datos de la categoría actual
                $.ajax({
                    url: 'get_categoria.php', // Endpoint para obtener datos de una categoría específica
                    type: 'GET',
                    data: {
                        id: categoryId
                    },
                    success: function(response) {
                        // Rellenar el formulario del modal con los datos recibidos
                        var categoria = JSON.parse(response);
                        $('#editCategoryId').val(categoria.id_categoria);
                        $('#editCategoryName').val(categoria.nombre);
                        $('#editCategoryDescription').val(categoria.descripcion);

                        // Abrir el modal
                        $('#editCategoryModal').modal('show');
                    },
                    error: function() {
                        alert('Error al obtener los datos de la categoría.');
                    }
                });
            });

            // Guardar los cambios de la categoría
            $('#saveCategoryChanges').on('click', function() {
                var formData = $('#editCategoryForm').serialize();

                $.ajax({
                    url: 'editar_categoria.php', // Endpoint para actualizar la categoría
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        // Comprobar si la respuesta indica éxito
                        if (response === 'success') {
                            alert('Categoría actualizada con éxito.');
                            $('#editCategoryModal').modal('hide');

                            // Opcional: recargar la tabla de categorías o actualizar los datos directamente
                            location.reload();
                        } else {
                            alert('Error al actualizar la categoría.');
                        }
                    },
                    error: function() {
                        alert('Error en el servidor al actualizar la categoría.');
                    }
                });
            });
        });
    </script>
</body>

</html>
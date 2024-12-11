<?php
include 'header.php';
include 'aside.php';
include 'footer.php';
include './config/sbd.php';

$sql_imagenes = $con->prepare("SELECT * FROM banner");
$sql_imagenes->execute();
$imagenes = $sql_imagenes->fetchAll(PDO::FETCH_ASSOC);
?>



<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edición de Carrusel - Punto Aroma</title>

    <style>
        .carousel-image {
            max-width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .no-banner {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 200px;
            background-color: #f0f0f0;
            border: 1px dashed #ccc;
            color: #999;
            font-size: 18px;
            font-weight: bold;
        }
    </style>
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
                            <h1>Edición de Carrusel</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="#">Inicio</a></li>
                                <li class="breadcrumb-item active">Edición de Carrusel</li>
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
                                    <h3 class="card-title">Imágenes del Carrusel</h3>
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-add-image">
                                            <i class="fas fa-plus"></i> Agregar Imagen
                                        </button>
                                    </div>
                                </div>
                                <!-- /.card-header -->
                                <div class="card-body">
                                    <div class="row">
                                        <?php if (empty($imagenes)) { ?>
                                            <div class="col-12">
                                                <div class="no-banner">Aún no hay banner disponible</div>
                                            </div>
                                        <?php } else { ?>
                                            <?php foreach ($imagenes as $imagen) { ?>
                                                <div class="col-sm-4">
                                                    <div class="card">
                                                        <img src="../<?php echo $imagen["ruta"] ?>" class="card-img-top carousel-image" alt="Imagen 1">
                                                        <div class="card-body">
                                                            <h5 class="card-title"><?php echo $imagen["nombre"] ?></h5>
                                                            <p class="card-text"><?php echo $imagen["descripcion"] ?></p>

                                                            <button type="button" class="btn btn-danger btn-sm" onclick="deleteImage(<?php echo $imagen['id_banner'] ?>)">
                                                                <i class="fas fa-trash"></i> Eliminar
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        <?php } ?>
                                    </div>
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

    <!-- Modal para agregar imagen -->
    <div class="modal fade" id="modal-add-image">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Agregar Nueva Imagen</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="addImageForm">
                        <div class="form-group">
                            <label for="image-file">Seleccionar Imagen</label>
                            <div class="input-group">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="image-file" accept="image/*" onchange="previewImage(event)">
                                    <label class="custom-file-label" for="image-file">Elegir archivo</label>
                                </div>
                            </div>
                            <!-- Contenedor de previsualización de imagen -->
                            <div id="image-preview" style="margin-top: 10px;">
                                <!-- Aquí se insertará la previsualización de la imagen -->
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="image-title">Título</label>
                            <input type="text" class="form-control" id="image-title" placeholder="Ingrese el título del banner">
                        </div>
                        <div class="form-group">
                            <label for="image-description">Título</label>
                            <input type="text" class="form-control" id="image-description" placeholder="Ingrese la descripcion del banner">
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" onclick="addImage()">Guardar Imagen</button>
                </div>
            </div>
        </div>
    </div>

    <!-- /.modal -->

    <!-- Modal para editar imagen -->
    <div class="modal fade" id="modal-edit-image">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Editar Imagen</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="editImageForm">
                        <div class="form-group">
                            <label for="edit-image-file">Cambiar Imagen</label>
                            <div class="input-group">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="edit-image-file" accept="image/*">
                                    <label class="custom-file-label" for="edit-image-file">Elegir archivo</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="edit-image-title">Título</label>
                            <input type="text" class="form-control" id="edit-image-title" placeholder="Ingrese el título del banner">
                        </div>
                        <div class="form-group">
                            <label for="edit-image-description">Título</label>
                            <input type="text" class="form-control" id="edit-image-description" placeholder="ingrese descripcion del banner ">
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" onclick="updateImage()">Guardar Cambios</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->

    <script>
        function deleteImage(id) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: "No podrás revertir esta acción",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    const formData = new FormData();
                    formData.append('id', id);

                    fetch('eliminar_imagen_banner.php', {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire(
                                    'Eliminada',
                                    'La imagen ha sido eliminada.',
                                    'success'
                                );
                                setTimeout(() => {
                                    location.reload();
                                }, 2000);
                            } else {
                                Swal.fire(
                                    'Error',
                                    data.message,
                                    'error'
                                );
                            }
                        })
                        .catch(error => {
                            Swal.fire(
                                'Error',
                                'Hubo un problema al eliminar la imagen.',
                                'error'
                            );
                            console.error('Error:', error);
                        });
                }
            });
        }


        // Función para previsualizar la imagen
        function previewImage(event) {
            const imagePreview = document.getElementById('image-preview');
            const file = event.target.files[0];

            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    // Creamos un elemento img con la fuente establecida al resultado de la carga del archivo
                    imagePreview.innerHTML = `<img src="${e.target.result}" alt="Previsualización" class="img-fluid" style="max-width: 100%; height: auto;">`;
                };
                reader.readAsDataURL(file);
            } else {
                imagePreview.innerHTML = ''; // Limpiamos la previsualización si no hay archivo seleccionado
            }
        }

        // Función para enviar la imagen y los datos mediante AJAX
        function addImage() {
            const formData = new FormData();
            const imageFile = document.getElementById('image-file').files[0];
            const title = document.getElementById('image-title').value;
            const description = document.getElementById('image-description').value;

            // Validamos que se haya seleccionado un archivo
            if (!imageFile) {
                alert('Por favor, seleccione una imagen.');
                return;
            }

            // Agregar los datos al FormData
            formData.append('image', imageFile);
            formData.append('title', title);
            formData.append('description', description);

            // Enviar los datos mediante AJAX
            fetch('subir_imagen_banner.php', {
                    method: 'POST',
                    body: formData,
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Imagen subida correctamente.');
                        // Limpiar el formulario y la previsualización
                        document.getElementById('addImageForm').reset();
                        document.getElementById('image-preview').innerHTML = '';
                        // Cerrar el modal
                        $('#modal-add-image').modal('hide');
                        location.reload();
                    } else {
                        alert('Error al subir la imagen1: ' + data.message);
                        console.error(data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Ocurrió un error en el servidor.');
                });
        }
    </script>
</body>

</html>
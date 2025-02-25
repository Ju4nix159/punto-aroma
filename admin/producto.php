<?php
include './footer.php';
include './header.php';
include './aside.php';
include './config/sbd.php';

$id_producto = $_GET['id_producto'];


$sql_resume_producto = $con->prepare("SELECT 
    p.id_producto, 
    p.nombre, 
    p.descripcion, 
    p.estado,
    c.nombre AS categoria, 
    p.destacado, 
    i.nombre AS imagen_principal,
    c.id_categoria,
    m.nombre AS marca,
    m.id_marca,
    sm.nombre AS submarca,
    MIN(CASE WHEN vtp.id_tipo_precio = 1 THEN vtp.precio END) AS precio_mayorista,
    MIN(CASE WHEN vtp.id_tipo_precio = 2 THEN vtp.precio END) AS precio_minorista,
    MIN(CASE WHEN vtp.id_tipo_precio = 3 THEN vtp.precio END) AS precio_mayorista_6,
    MIN(CASE WHEN vtp.id_tipo_precio = 4 THEN vtp.precio END) AS precio_mayorista_48,
    MIN(CASE WHEN vtp.id_tipo_precio = 5 THEN vtp.precio END) AS precio_mayorista_120
FROM 
    productos p
JOIN 
    categorias c ON p.id_categoria = c.id_categoria
JOIN 
    marcas m ON p.id_marca = m.id_marca
LEFT JOIN 
    marcas sm ON p.id_submarca = sm.id_marca
LEFT JOIN 
    imagenes i ON p.id_producto = i.id_producto AND i.principal = 1
LEFT JOIN 
    variantes_tipo_precio vtp ON p.id_producto = vtp.id_producto
WHERE 
    p.id_producto = :id_producto
GROUP BY 
    p.id_producto, 
    p.nombre, 
    p.descripcion, 
    c.nombre, 
    p.destacado, 
    i.nombre, 
    sm.nombre;");
$sql_resume_producto->bindParam(':id_producto', $id_producto);
$sql_resume_producto->execute();
$resumen = $sql_resume_producto->fetch(PDO::FETCH_ASSOC);


$sql_variante = $con->prepare("SELECT ep.nombre AS estado, v.aroma, v.sku, v.color, v.titulo
            FROM variantes v
            JOIN estados_productos ep ON v.id_estado_producto = ep.id_estado_producto
            WHERE v.id_producto = :id_producto;");

$sql_variante->bindParam(':id_producto', $id_producto);
$sql_variante->execute();
$variantes = $sql_variante->fetchAll(PDO::FETCH_ASSOC);

$imagen = $con->prepare("SELECT p.id_producto, i.nombre AS imagen_principal
            FROM productos p
            LEFT JOIN imagenes i ON p.id_producto = i.id_producto AND i.principal = 1
            WHERE p.id_producto = :id_producto;");
$imagen->bindParam(':id_producto', $id_producto);
$imagen->execute();
$imagen = $imagen->fetch(PDO::FETCH_ASSOC);

$sql_categorias = $con->prepare("SELECT * FROM categorias;");
$sql_categorias->execute();
$categorias = $sql_categorias->fetchAll(PDO::FETCH_ASSOC);

$sql_marca = $con->prepare("SELECT * FROM marcas;");
$sql_marca->execute();
$marcas = $sql_marca->fetchAll(PDO::FETCH_ASSOC);


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
      <!-- Content Header (Page header) -->
      <section class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1>Resumen del Producto</h1>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="#">Inicio</a></li>
                <li class="breadcrumb-item"><a href="#">Productos</a></li>
                <li class="breadcrumb-item active">Resumen del Producto</li>
              </ol>
            </div>
          </div>
        </div><!-- /.container-fluid -->
      </section>

      <!-- Main content -->
      <section class="content">
        <div class="container-fluid">
          <div class="row">
            <div class="col-md-6">
              <div class="card card-primary">
                <div class="card-header">
                  <h3 class="card-title">Información del Producto</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                  <!-- Campos Editables -->
                  <div id="editable-fields">
                    <input type="hidden" id="id_producto-input" value="<?php echo $id_producto ?>">
                    <strong><i class="fas fa-box mr-1"></i> Nombre del Producto</strong>
                    <p class="text-muted mb-0" id="nombre-text"><?php echo $resumen["nombre"] ?></p>
                    <input type="text" id="nombre-input" class="form-control d-none" value="<?php echo $resumen["nombre"] ?>">
                    <hr>

                    <strong><i class="fas fa-tag mr-1"></i> Marca</strong>
                    <p class="text-muted mb-0" id="marca-text"><?php echo $resumen["marca"] ?></p>
                    <select name="marca" id="marca-input" class="form-control d-none">
                      <?php foreach ($marcas as $marca) {  ?>
                        <option value="<?php echo $marca['id_marca'] ?>" <?php echo $marca['id_marca'] == $resumen['id_marca'] ? 'selected' : '' ?>><?php echo $marca['nombre'] ?></option>
                      <?php } ?>
                    </select>

                    <hr>
                    <strong><i class="fas fa-tag mr-1"></i> Submarca</strong>
                    <p class="text-muted mb-0" id="submarca-text"><?php echo !empty($resumen["submarca"]) ? $resumen["submarca"] : 'N/A'; ?></p>
                    <input type="text" id="submarca-input" class="form-control d-none" value="<?php echo $resumen["submarca"] ?>">

                    <hr>
                    <strong><i class="fas fa-tag mr-1"></i> Categoría</strong>
                    <p class="text-muted mb-0" id="categoria-text"><?php echo $resumen["categoria"] ?></p>
                    <select name="categoria" id="categoria-input" class="form-control d-none">
                      <?php foreach ($categorias as $categoria) {  ?>
                        <option value="<?php echo $categoria['id_categoria'] ?>" <?php echo $categoria['id_categoria'] == $resumen['id_categoria'] ? 'selected' : '' ?>><?php echo $categoria['nombre'] ?></option>
                      <?php } ?>
                    </select>

                    <hr>
                    <strong><i class="fas fa-dollar-sign mr-1"></i> Precio Minorista</strong>
                    <p class="text-muted mb-0" id="precio_minorista-text">$<?php echo $resumen["precio_minorista"] ?></p>
                    <input type="number" id="precio_minorista-input" class="form-control d-none" value="<?php echo $resumen["precio_minorista"] ?>">

                    <hr>
                    <?php if (strtolower($resumen["marca"]) == "saphirus") { ?>
                      <strong><i class="fas fa-warehouse mr-1"></i> Precio Mayorista 6</strong>
                      <p class="text-muted mb-0" id="precio_mayorista_6-text">$<?php echo $resumen["precio_mayorista_6"] ?></p>
                      <input type="number" id="precio_mayorista_6-input" class="form-control d-none" value="<?php echo $resumen["precio_mayorista_6"] ?>">
                      <hr>
                      <strong><i class="fas fa-warehouse mr-1"></i> Precio Mayorista 48</strong>
                      <p class="text-muted mb-0" id="precio_mayorista_48-text">$<?php echo $resumen["precio_mayorista_48"] ?></p>
                      <input type="number" id="precio_mayorista_48-input" class="form-control d-none" value="<?php echo $resumen["precio_mayorista_48"] ?>">
                      <hr>
                      <strong><i class="fas fa-warehouse mr-1"></i> Precio Mayorista 120</strong>
                      <p class="text-muted mb-0" id="precio_mayorista_120-text">$<?php echo $resumen["precio_mayorista_120"] ?></p>
                      <input type="number" id="precio_mayorista_120-input" class="form-control d-none" value="<?php echo $resumen["precio_mayorista_120"] ?>">
                    <?php } else { ?>
                      <strong><i class="fas fa-warehouse mr-1"></i> Precio Mayorista</strong>
                      <p class="text-muted mb-0" id="precio_mayorista-text">$<?php echo $resumen["precio_mayorista"] ?></p>
                      <input type="number" id="precio_mayorista-input" class="form-control d-none" value="<?php echo $resumen["precio_mayorista"] ?>">
                    <?php } ?>

                    <hr>
                    <strong><i class="fas fa-cubes mr-1"></i> Destacado</strong>
                    <p class="text-muted mb-0" id="destacado-text"><?php echo $resumen["destacado"] == 1 ? 'Si' : 'No'; ?></p>
                    <select id="destacado-input" class="form-control d-none">
                      <option value="1" <?php echo $resumen["destacado"] == 1 ? 'selected' : ''; ?>>Si</option>
                      <option value="0" <?php echo $resumen["destacado"] == 0 ? 'selected' : ''; ?>>No</option>
                    </select>

                    <hr>
                    <strong><i class="fas fa-align-left mr-1"></i> Descripción</strong>
                    <p class="text-muted mb-0" id="descripcion-text"><?php echo $resumen["descripcion"] ?></p>
                    <textarea id="descripcion-input" class="form-control d-none"><?php echo $resumen["descripcion"] ?></textarea>

                    <hr>
                    <strong><i class="fas fa-cubes mr-1"></i> Estado</strong>
                    <p class="text-muted mb-0" id="estado-text"><?php echo $resumen["estado"] == 1 ? 'Habilitado' : 'Deshabilitado'; ?></p>
                    <select id="estado-input" class="form-control d-none">
                      <option value="1" <?php echo $resumen["estado"] == 1 ? 'selected' : ''; ?>>Habilitar</option>
                      <option value="0" <?php echo $resumen["estado"] == 0 ? 'selected' : ''; ?>>Deshabilitar</option>
                    </select>
                  </div>
                </div>

                <!-- Botones -->
                <div class="card-footer">
                  <div class="row mt-4">
                    <div class="col-12">
                      <a href="./productos.php" id="volver-listado" class="btn btn-secondary">Volver al listado</a>
                      <button type="button" id="boton-editar" class="btn btn-success float-right" onclick="iniciarEdicion()">
                        <i class="fas fa-edit"></i> Editar
                      </button>
                      <button type="button" id="boton-guardar" class="btn btn-success float-right d-none" onclick="editarProducto()">
                        <i class="fas fa-save"></i> Guardar
                      </button>
                      <button type="button" id="boton-cancelar" class="btn btn-danger float-right d-none" onclick="cancelarEdicion()">
                        <i class="fas fa-times"></i> Cancelar Edición
                      </button>
                    </div>
                  </div>
                </div>

                <!-- /.card-body -->
              </div>
              <!-- /.card -->
            </div>
            <!-- /.col -->
            <div class="col-md-6">
              <div class="card card-secondary">
                <div class="card-header">
                  <h3 class="card-title">variantes</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                  <table class="table table-bordered">
                    <thead>
                      <tr>
                        <th>SKU</th>
                        <th>aroma</th>
                        <th>color</th>
                        <th>titulo</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($variantes as $variante) { ?>
                        <tr>
                          <td><?php echo $variante["sku"] ?></td>
                          <td><?php echo $variante["aroma"] ?></td>
                          <td><?php echo $variante["color"] ?></td>
                          <td><?php echo $variante["titulo"] ?></td>
                          <td><?php echo $variante["estado"] ?></td>
                          <td>
                            <?php if (strtolower($variante["estado"]) == "disponible") { // Disponible 
                            ?>
                              <button type="button" class="btn btn-sm btn-danger" onclick="eliminarVariante(this)" data-sku="<?php echo $variante['sku']; ?>">
                                <i class="fas fa-trash"></i> Eliminar
                              </button>
                            <?php } elseif (strtolower($variante["estado"]) == "agotado") { // Agotado 
                            ?>
                              <button type="button" class="btn btn-sm bg-orange" onclick="editarVariante(this)"
                                data-sku="<?php echo $variante['sku']; ?>"
                                data-aroma="<?php echo $variante['aroma']; ?>"
                                data-titulo="<?php echo $variante['titulo']; ?>"
                                data-color="<?php echo $variante['color']; ?>"><i class="fas fa-edit"></i> Editar</button>

                              </button>
                              <button type="button" class="btn btn-sm btn-success" onclick="activarVariante(this)" data-sku="<?php echo $variante['sku']; ?>">
                                <i class="fas fa-play"></i> Activar
                              </button>
                            <?php } ?>
                          </td>
                        </tr>
                      <?php } ?>

                    </tbody>
                  </table>
                </div>
                <!-- /.card-body -->
                <div class="card-footer">
                  <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-add-fragrance">
                    <i class="fas fa-plus"></i> Agregar Fragancia
                  </button>
                </div>
              </div>
              <!-- /.card -->
            </div>

            <div class="col-md-6">
              <div class="card card-success">
                <div class="card-header">
                  <h3 class="card-title">imagen</h3>
                </div>
                <div class="card-body d-flex align-items-center justify-content-between">
                  <!-- Imagen izquierda -->
                  <div class="text-center mb-4">
                    <img src="../assets/productos/imagen/<?php echo $id_producto; ?>/<?php echo $imagen["imagen_principal"]; ?>"
                      alt="<?php echo $resumen["nombre"]; ?>"
                      class="img-fluid"
                      style="max-height: 200px;">
                  </div>

                  <!-- Flecha en el medio -->
                  <div class="d-flex align-items-center justify-content-center">
                    <i class="fas fa-arrow-right"></i>
                  </div>

                  <!-- Imagen derecha con hover -->
                  <div class="text-center mb-4 position-relative">
                    <img id="rightImage"
                      src="../assets/subirimagen.webp"
                      alt="<?php echo $resumen["nombre"]; ?>"
                      class="img-fluid hover-image"
                      style="max-height: 200px;">
                    <span class="hover-text" style="display: none;">Subir imagen</span>
                    <input type="file" id="imageInput" style="display: none;" accept="image/*">
                  </div>

                  <!-- Botón de guardar imagen -->
                  <div id="saveButtonContainer" style="display: none;" class="mt-3">
                    <button id="saveImage" class="btn btn-primary">Guardar imagen</button>
                  </div>
                </div>

              </div>
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



  <!-- Modal para agregar fragancia -->
  <div class="modal fade" id="modal-add-fragrance">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Agregar Nueva Fragancia</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form>
            <input type="hidden" name="id_producto" id="id_producto" value="<?php echo $id_producto ?>">
            <div class="form-group">
              <label for="variante-sku">SKU</label>
              <input type="text" class="form-control" id="variante-sku">
            </div>
            <div class="form-group">
              <label for="variante-atributo">Atributo</label>
              <select class="form-control" id="variante-atributo">
                <option value="color">Color</option>
                <option value="aroma">Aroma</option>
                <option value="titulo">Título</option>
              </select>
              <div class="form-group">
                <label for="variante-name">valor del atributo</label>
                <input type="text" class="form-control" id="variante-name">
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
          <button type="button" class="btn btn-primary" onclick="agregarVariante()">Guardar Variante</button>
        </div>
      </div>
    </div>
  </div>
  <!-- /.modal -->

  <!-- modal para  la edicion del producto -->
  <div class="modal fade" id="modal-edit-fragrance">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Editar Variante</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form id="edit-variante-form">
            <div class="form-group">
              <label for="edit-variante-sku">SKU</label>
              <input type="text" class="form-control" id="edit-variante-sku" readonly>
            </div>
            <div class="form-group">
              <label for="edit-variante-atributo">Atributo</label>
              <select class="form-control" id="edit-variante-atributo">
                <option value="color">Color</option>
                <option value="aroma">Aroma</option>
                <option value="titulo">Título</option>
              </select>
              <div class="form-group">
                <label for="edit-variante-name">Nombre de la variante</label>
                <input type="text" class="form-control" id="edit-variante-name">
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
          <button type="button" class="btn btn-primary" onclick="guardarVarianteEditada()">Guardar Cambios</button>
        </div>
      </div>
    </div>
  </div>


  <script>
    async function agregarVariante() {
      // Obtener valores de los campos
      const id_producto = document.getElementById("id_producto").value;
      const sku = document.getElementById("variante-sku").value;
      const atributo = document.getElementById("variante-atributo").value;
      const valor = document.getElementById("variante-name").value;


      // Validar campos obligatorios
      if (!sku || !valor) {
        alert('Por favor, completa todos los campos obligatorios.');
        return;
      }


      // Crear el FormData
      const formData = new FormData();
      formData.append('id_producto', id_producto);
      formData.append('sku', sku);
      formData.append('atributo', atributo);
      formData.append('valor', valor);


      try {
        // Enviar solicitud
        const response = await fetch('anadir_fragancia.php', {
          method: 'POST',
          body: formData,
        });
        const data = await response.json();

        if (data.status === 'success') {
          alert('Fragancia agregada correctamente');
          location.reload(); // Recargar página
        } else {
          alert('Ocurrió un error al agregar la fragancia: ' + (data.message || 'Error desconocido.'));
        }
      } catch (error) {
        console.error('Error al enviar la solicitud:', error);
        alert('Ocurrió un problema al procesar la solicitud. Inténtalo nuevamente.');
      }
    }

    function eliminarVariante(button) {
      const confirmar = confirm('¿Estás seguro de que deseas eliminar esta variante?');

      if (confirmar) {
        // Obtener el SKU del atributo data-sku del botón
        const sku = button.getAttribute('data-sku');

        if (!sku) {
          alert('El SKU de la variante es requerido.');
          return;
        }

        console.log('Eliminando variante con SKU:', sku);
        const formData = new FormData();
        formData.append('sku', sku);

        fetch('eliminar_variante.php', {
            method: 'POST',
            body: formData,
          })
          .then(response => {
            if (!response.ok) {
              throw new Error('Error en la solicitud al servidor.');
            }
            return response.json();
          })
          .then(data => {
            if (data.status === 'success') {
              alert('Variante eliminada correctamente');
              location.reload(); // Recargar página
            } else {
              alert('Ocurrió un error al eliminar la variante: ' + (data.message || 'Error desconocido.'));
            }
          })
          .catch(error => {
            console.error('Error:', error);
            alert('Ocurrió un error inesperado.');
          });
      }
    }

    function editarVariante(button) {
      // Obtener datos del botón usando atributos data-*
      const sku = button.getAttribute('data-sku');
      const aroma = button.getAttribute('data-aroma') || null;
      const color = button.getAttribute('data-color') || null;
      const titulo = button.getAttribute('data-titulo') || null;

      let atributo = '';
      let valor = '';

      if (aroma) {
        atributo = 'aroma';
        valor = aroma;
      } else if (color) {
        atributo = 'color';
        valor = color;
      } else if (titulo) {
        atributo = 'titulo';
        valor = titulo;
      }

      document.getElementById('edit-variante-atributo').value = atributo;
      document.getElementById('edit-variante-name').value = valor;
      document.getElementById('edit-variante-sku').value = sku;

      // Mostrar el modal de edición
      $('#modal-edit-fragrance').modal('show');
    }

    // Función para guardar la variante editada
    function guardarVarianteEditada() {
      // Recoger datos del formulario
      const sku = document.getElementById('edit-variante-sku').value;
      const atributo = document.getElementById('edit-variante-atributo').value;
      const valor = document.getElementById('edit-variante-name').value;

      // Aquí debes hacer una llamada AJAX para actualizar los datos en el servidor
      const formData = new FormData();
      formData.append('sku', sku);
      formData.append("atributo", atributo);
      formData.append("valor", valor);

      fetch('editar_variante.php', {
          method: 'POST',
          body: formData,
        })
        .then(response => response.json())
        .then(data => {
          if (data.status === 'success') {
            alert('Variante actualizada correctamente');
            location.reload(); // Recargar página
          } else {
            alert('Ocurrió un error al actualizar la variante: ' + (data.message || 'Error desconocido.'));
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('Ocurrió un error inesperado.');
        });

      // Cerrar el modal después de la acción
      $('#modal-edit-fragrance').modal('hide');
    }

    function activarVariante(button) {
      const confirmar = confirm('¿Estás seguro de que deseas activar esta variante?');

      if (confirmar) {
        // Obtener el SKU del atributo data-sku del botón
        const sku = button.getAttribute('data-sku');

        console.log('activando variante con SKU:', sku);
        const formData = new FormData();
        formData.append('sku', sku);

        fetch('activar_variante.php', {
            method: 'POST',
            body: formData,
          })
          .then(response => {
            if (!response.ok) {
              throw new Error('Error en la solicitud al servidor.');
            }
            return response.json();
          })
          .then(data => {
            if (data.status === 'success') {
              alert('Variante activada correctamente');
              location.reload(); // Recargar página
            } else {
              alert('Ocurrió un error al activar la variante: ' + (data.message || 'Error desconocido.'));
            }
          })
          .catch(error => {
            console.error('Error:', error);
            alert('Ocurrió un error inesperado.');
          });
      }
    }

    function iniciarEdicion() {
      document.querySelectorAll('#editable-fields p').forEach(p => p.classList.add('d-none'));
      document.querySelectorAll('#editable-fields input, #editable-fields select, #editable-fields textarea').forEach(input => input.classList.remove('d-none'));
      document.getElementById('boton-editar').classList.add('d-none');
      document.getElementById('boton-guardar').classList.remove('d-none');
      document.getElementById('boton-cancelar').classList.remove('d-none');
    }

    function cancelarEdicion() {
      document.querySelectorAll('#editable-fields p').forEach(p => p.classList.remove('d-none'));
      document.querySelectorAll('#editable-fields input, #editable-fields select, #editable-fields textarea').forEach(input => input.classList.add('d-none'));
      document.getElementById('boton-editar').classList.remove('d-none');
      document.getElementById('boton-guardar').classList.add('d-none');
      document.getElementById('boton-cancelar').classList.add('d-none');
    }

    function editarProducto() {
      const id_producto = document.getElementById('id_producto-input').value;
      const nombre = document.getElementById('nombre-input').value;
      const categoria = document.getElementById('categoria-input').value;
      const marca = document.getElementById('marca-input').value;

      const precio_minorista = document.getElementById('precio_minorista-input').value;

      // Verifica si los elementos existen antes de acceder a sus valores
      const precio_mayorista_input = document.getElementById('precio_mayorista-input'); 
      const precio_mayorista = precio_mayorista_input ? precio_mayorista_input.value : null;

      const precio_mayorista_6_input = document.getElementById('precio_mayorista_6-input');
      const precio_mayorista_6 = precio_mayorista_6_input ? precio_mayorista_6_input.value : null;

      const precio_mayorista_48_input = document.getElementById('precio_mayorista_48-input');
      const precio_mayorista_48 = precio_mayorista_48_input ? precio_mayorista_48_input.value : null;

      const precio_mayorista_120_input = document.getElementById('precio_mayorista_120-input');
      const precio_mayorista_120 = precio_mayorista_120_input ? precio_mayorista_120_input.value : null;

      const destacado = document.getElementById('destacado-input').value;
      const descripcion = document.getElementById('descripcion-input').value;
      const estado = document.getElementById('estado-input').value;

      const formData = new FormData();
      formData.append('id_producto', id_producto);
      formData.append('nombre', nombre);
      formData.append('categoria', categoria);
      formData.append('marca', marca);
      formData.append('descripcion', descripcion);

      if (precio_minorista !== null) formData.append('precio_minorista', precio_minorista);
      if (precio_mayorista !== null) formData.append('precio_mayorista', precio_mayorista);
      if (precio_mayorista_6 !== null) formData.append('precio_mayorista_6', precio_mayorista_6);
      if (precio_mayorista_48 !== null) formData.append('precio_mayorista_48', precio_mayorista_48);
      if (precio_mayorista_120 !== null) formData.append('precio_mayorista_120', precio_mayorista_120);

      formData.append('destacado', destacado);
      formData.append('estado', estado);

      console.log('Enviando datos:', Object.fromEntries(formData.entries()));

      fetch('guardar_producto.php', {
          method: 'POST',
          body: formData
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            alert('Producto actualizado correctamente');
            location.reload();
          } else {
            alert('Ocurrió un error al actualizar el producto: ' + (data.message || 'Error desconocido'));
          }
        })
    }

    const rightImage = document.getElementById('rightImage');
    const imageInput = document.getElementById('imageInput');
    const saveButtonContainer = document.getElementById('saveButtonContainer');

    // Mostrar input de imagen al hacer clic en la imagen derecha
    rightImage.addEventListener('click', () => {
      imageInput.click();
    });

    // Mostrar la imagen seleccionada
    imageInput.addEventListener('change', (event) => {
      const file = event.target.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = (e) => {
          rightImage.src = e.target.result;
          saveButtonContainer.style.display = 'block'; // Mostrar el botón de guardar
        };
        reader.readAsDataURL(file);
      }
    });

    // Manejar el clic en el botón de guardar
    document.getElementById('saveImage').addEventListener('click', async () => {
      const formData = new FormData();
      formData.append('image', imageInput.files[0]);
      formData.append('id_producto', document.getElementById('id_producto-input').value);

      // Enviar la imagen al servidor
      const response = await fetch('guardar_imagen.php', {
        method: 'POST',
        body: formData,
      });

      const result = await response.json();
      if (result.success) {
        alert('Imagen guardada como principal.');
        location.reload(); // Recargar la página para reflejar los cambios
      } else {
        alert('Error al guardar la imagen.');
      }
    });
  </script>
</body>


</html>
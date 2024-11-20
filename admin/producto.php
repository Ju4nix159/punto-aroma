<?php
include './footer.php';
include './header.php';
include './aside.php';
include './config/sbd.php';

$id_producto = $_GET['id_producto'];


$sql_resume_producto = $con->prepare(" SELECT 
            p.id_producto, 
            p.nombre, 
            p.descripcion, 
            c.nombre AS categoria, 
            p.destacado, 
            i.ruta AS imagen_principal,
            MIN(CASE WHEN vtp.id_tipo_precio = 1 THEN vtp.precio END) AS precio_minorista,
            MIN(CASE WHEN vtp.id_tipo_precio = 2 THEN vtp.precio END) AS precio_mayorista
        FROM 
            productos p
            JOIN categorias c ON p.id_categoria = c.id_categoria
            LEFT JOIN imagenes i ON p.id_producto = i.id_producto AND i.principal = 1
            LEFT JOIN variantes_tipo_precio vtp ON p.id_producto = vtp.id_producto
        WHERE 
            p.id_producto = :id_producto
        GROUP BY 
            p.id_producto, 
            p.nombre, 
            p.descripcion, 
            c.nombre, 
            p.destacado, 
            i.ruta;");
$sql_resume_producto->bindParam(':id_producto', $id_producto);
$sql_resume_producto->execute();
$resumen = $sql_resume_producto->fetch(PDO::FETCH_ASSOC);


$sql_fragancias = $con->prepare("SELECT ep.nombre AS estado_nombre, v.aroma, v.sku
            FROM variantes v
            JOIN estados_productos ep ON v.id_estado_producto = ep.id_estado_producto
            WHERE v.id_producto = :id_producto;");

$sql_fragancias->bindParam(':id_producto', $id_producto);
$sql_fragancias->execute();
$fragancias = $sql_fragancias->fetchAll(PDO::FETCH_ASSOC);

$imagen = $con->prepare("SELECT p.id_producto, i.ruta AS imagen_principal
            FROM productos p
            LEFT JOIN imagenes i ON p.id_producto = i.id_producto AND i.principal = 1
            WHERE p.id_producto = :id_producto;");
$imagen->bindParam(':id_producto', $id_producto);
$imagen->execute();
$imagen = $imagen->fetch(PDO::FETCH_ASSOC);

$sql_aromas = $con->prepare("SELECT * FROM aromas;");
$sql_aromas->execute();
$aromas = $sql_aromas->fetchAll(PDO::FETCH_ASSOC);


?>



<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
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
                  <div class="text-center mb-4">
                    <img src="../assets/productos<?php echo $imagen["imagen_principal"] ?>" alt="<?php echo $resumen["nombre"] ?>" class="img-fluid" style="max-height: 200px;">
                  </div>

                  <strong><i class="fas fa-box mr-1"></i> Nombre del Producto</strong>
                  <p class="text-muted"><?php echo $resumen["nombre"] ?></p>

                  <hr>

                  <strong><i class="fas fa-tag mr-1"></i> Categoría</strong>
                  <p class="text-muted"><?php echo $resumen["categoria"] ?></p>

                  <hr>

                  <strong><i class="fas fa-dollar-sign mr-1"></i> Precio Minorista</strong>
                  <p class="text-muted">$<?php echo $resumen["precio_minorista"] ?></p>

                  <hr>

                  <strong><i class="fas fa-warehouse mr-1"></i> Precio Mayorista</strong>
                  <p class="text-muted">$<?php echo $resumen["precio_mayorista"] ?></p>

                  <hr>

                  <strong><i class="fas fa-cubes mr-1"></i> Destacado</strong>
                  <?php if ($resumen["destacado"] == 1) { ?>

                    <p class="text-muted">Si</p>
                  <?php } else { ?>
                    <p class="text-muted">No</p>
                  <?php } ?>

                  <hr>

                  <strong><i class="fas fa-align-left mr-1"></i> Descripción</strong>
                  <p class="text-muted"><?php echo $resumen["descripcion"] ?></p>
                </div>
                <!-- /.card-body -->
              </div>
              <!-- /.card -->
            </div>
            <!-- /.col -->
            <div class="col-md-6">
              <div class="card card-secondary">
                <div class="card-header">
                  <h3 class="card-title">Fragancias</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                  <table class="table table-bordered">
                    <thead>
                      <tr>
                        <th>SKU</th>
                        <th>Fragancia</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($fragancias as $fragancia) { ?>
                        <tr>
                          <td><?php echo $fragancia["sku"] ?></td>
                          <td><?php echo $fragancia["aroma"] ?></td>
                          <td><?php echo $fragancia["estado_nombre"] ?></td>
                          <td>
                            <button type="button" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
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
            <!-- /.col -->
          </div>
          <!-- /.row -->
          <div class="row mt-4">
            <div class="col-12">
              <a href="#" class="btn btn-secondary">Volver al listado</a>
              <button type="button" class="btn btn-success float-right" style="margin-right: 5px;">
                <i class="fas fa-edit"></i> Editar Producto
              </button>
              <button type="button" class="btn btn-danger float-right" style="margin-right: 5px;">
                <i class="fas fa-trash"></i> Eliminar Producto
              </button>
            </div>
          </div>
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
            <div class="form-group">
              <input type="hidden" name="id_producto" id="id_producto" value="<?php echo $id_producto ?>">
              <label for="fragrance-name">Nombre de la Fragancia</label>
              <input type="text" class="form-control" id="fragrance-name" placeholder="Ingrese el nombre de la fragancia">
            </div>
            <div class="form-group">
              <label for="fragrance-sku">SKU</label>
              <input type="text" class="form-control" id="fragrance-sku" placeholder="Ingrese SKU de la fragancia">
            </div>
          </form>
        </div>
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
          <button type="button" class="btn btn-primary" onclick="agregarFragancia()">Guardar Fragancia</button>
        </div>
      </div>
    </div>
  </div>
  <!-- /.modal -->
  <script>
    async function agregarFragancia() {
      // Obtener valores de los campos
      const id_producto = document.getElementById("id_producto").value;
      const aroma = document.getElementById('fragrance-name').value;
      const sku = document.getElementById('fragrance-sku').value;

      // Validar campos obligatorios
      if (!aroma || !sku) {
        alert('Por favor, completa todos los campos obligatorios.');
        return;
      }


      // Crear el FormData
      const formData = new FormData();
      formData.append('id_producto', id_producto);
      formData.append('sku', sku);
      formData.append('aroma', aroma);

      try {
        // Enviar solicitud
        const response = await fetch('añadir_fragancia.php', {
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
  </script>
</body>


</html>
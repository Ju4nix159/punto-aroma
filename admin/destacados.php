<?php
include './header.php';
include './config/sbd.php';
include './aside.php';
include './footer.php';

$sql_destacados = $con->prepare('SELECT p.id_producto, p.nombre, p.destacado, c.nombre as categoria, m.nombre as marca
FROM productos p
JOIN marcas m ON p.id_marca = m.id_marca
JOIN categorias c ON p.id_categoria = c.id_categoria
WHERE p.destacado = 1');
$sql_destacados->execute();
$destacados = $sql_destacados->fetchAll();


?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Destacados</title>
</head>

<body>
  <div class="wrapper">
    <div class="content-wrapper">
      <section class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1 class="m-0">Gestion productos destacados</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="./admin.php">Home</a></li>
                <li class="breadcrumb-item active">Productos</>
                </li>
              </ol>
            </div><!-- /.col -->
          </div><!-- /.row -->
        </div><!-- /.container-fluid -->
      </section><!-- /.content-header -->
      <section class="content">
        <div class="container-fluid">
          <div class="row">
            <div class="col-12">
              <div class="card">
                <div class="card-header">
                  <h3 class="card-title">Productos destacado</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                  <table id="destacado" class="table table-bordered table-striped">
                    <thead>
                      <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Marca</th>
                        <th>categoria</th>
                        <th>Accion</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($destacados as $destacado) { ?>
                        <tr>
                          <td><?php echo $destacado["id_producto"] ?></td>
                          <td><?php echo $destacado["nombre"] ?></td>
                          <td><?php echo $destacado["marca"] ?></td>
                          <td><?php echo $destacado["categoria"] ?></td>
                          <td>
                            <a href="#" type="button" class="btn bg-blue btn-flat margin"><i class="fas fa-eye"></i></a>
                          </td>
                        </tr>
                      <?php } ?>
                    </tbody>
                    <tfoot>
                      <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Marca</th>
                        <th>categoria</th>
                        <th>Accion</th>
                      </tr>
                    </tfoot>
                  </table>
                </div>
                <!-- /.card-body -->
              </div>
              <!-- /.card -->
            </div>
          </div>
        </div>
      </section>
    </div>
  </div>
  <script>
    $(function() {
      $("#destacado").DataTable({
        "responsive": true,
        "lengthChange": false,
        "autoWidth": false,
        "buttons": ["copy", "excel", "pdf", "print", "colvis"]
      }).buttons().container().appendTo('#destacado_wrapper .col-md-6:eq(0)');
      $('#example2').DataTable({
        "paging": true,
        "lengthChange": false,
        "searching": false,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "responsive": true,
      });
    });
  </script>
</body>

</html>
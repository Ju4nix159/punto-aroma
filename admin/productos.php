<?php
include './header.php';
include './config/sbd.php';
include './aside.php';
include './footer.php';

$sql_productos = $con->prepare("SELECT p.*, c.nombre AS categoria, COUNT(DISTINCT v.aroma) AS cantidad_fragancias, MIN(CASE WHEN vtp.id_tipo_precio = 1 THEN vtp.precio END) AS precio_minorista, MIN(CASE WHEN vtp.id_tipo_precio = 2 THEN vtp.precio END) AS precio_mayorista , m.nombre AS marca
FROM productos p 
JOIN categorias c ON p.id_categoria = c.id_categoria 
JOIN marcas m ON p.id_marca = m.id_marca
JOIN variantes v ON p.id_producto = v.id_producto 
JOIN variantes_tipo_precio vtp ON p.id_producto = vtp.id_producto 
GROUP BY p.id_producto, p.nombre, p.descripcion, c.nombre;
");
$sql_productos->execute();
$productos = $sql_productos->fetchAll(PDO::FETCH_ASSOC);


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
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Gestion productos</h1>
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
                                    <h3 class="card-title">Gestion de los pedidos</h3>
                                </div>
                                <!-- /.card-header -->
                                <div class="card-body">
                                    <table id="pedidos" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Nombre</th>
                                                <th>Marca</th>
                                                <th>categoria</th>
                                                <th>precio min </th>
                                                <th>precio may </th>
                                                <th>fragancias</th>
                                                <th>Accion</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($productos as $producto) { ?>
                                                <tr>
                                                    <td><?php echo $producto["id_producto"] ?></td>
                                                    <td><?php echo $producto["nombre"] ?></td>
                                                    <td><?php echo $producto["marca"] ?></td>
                                                    <td><?php echo $producto["categoria"] ?></td>
                                                    <td><?php echo $producto["precio_minorista"] ?></td>
                                                    <td><?php echo $producto["precio_mayorista"] ?></td>
                                                    <td><?php echo $producto["cantidad_fragancias"] ?></td>
                                                    <td>
                                                        <a href="producto.php?id_producto=<?php echo $producto["id_producto"] ?>" type="button" class="btn bg-blue btn-flat margin"><i class="fas fa-eye"></i></a>
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
                                                <th>precio min </th>
                                                <th>precio may </th>
                                                <th>fragancias</th>
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
            $("#pedidos").DataTable({
                "responsive": true,
                "lengthChange": false,
                "autoWidth": false,
                "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
            }).buttons().container().appendTo('#pedidos_wrapper .col-md-6:eq(0)');
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
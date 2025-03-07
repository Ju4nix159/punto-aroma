<?php
include './header.php';
include './config/sbd.php';
include './aside.php';
include './footer.php';

$sql_productos = $con->prepare("SELECT 
    p.id_producto, 
    p.nombre, 
    c.nombre AS categoria, 
    COUNT(DISTINCT v.sku) AS cantidad_variantes, 
    MIN(CASE WHEN vtp.id_tipo_precio = 1 THEN vtp.precio END) AS precio_minorista, 
    MIN(CASE WHEN vtp.id_tipo_precio = 2 THEN vtp.precio END) AS precio_mayorista, 
    m.nombre AS marca
FROM 
    productos p 
JOIN 
    categorias c ON p.id_categoria = c.id_categoria 
JOIN 
    marcas m ON p.id_marca = m.id_marca
JOIN 
    variantes v ON p.id_producto = v.id_producto 
JOIN 
    variantes_tipo_precio vtp ON p.id_producto = vtp.id_producto 
WHERE 
    m.nombre != 'saphirus'
GROUP BY 
    p.id_producto, p.nombre, p.descripcion, c.nombre;
");
$sql_productos->execute();
$productos_no_saphirus = $sql_productos->fetchAll(PDO::FETCH_ASSOC);

$sql_productos_saphirus =   $con->prepare("SELECT 
    p.id_producto, 
    p.nombre, 
    c.nombre AS categoria, 
    COUNT(DISTINCT v.sku) AS cantidad_variantes, 
    MIN(CASE WHEN vtp.id_tipo_precio = 2 THEN vtp.precio END) AS precio_minorista, 
    MIN(CASE WHEN vtp.id_tipo_precio = 3 THEN vtp.precio END) AS precio_mayorista_6, 
    MIN(CASE WHEN vtp.id_tipo_precio = 4 THEN vtp.precio END) AS precio_mayorista_48, 
    MIN(CASE WHEN vtp.id_tipo_precio = 5 THEN vtp.precio END) AS precio_mayorista_120, 
    m.nombre AS marca,
    sm.nombre AS submarca
FROM 
    productos p 
JOIN 
    categorias c ON p.id_categoria = c.id_categoria 
JOIN 
    marcas m ON p.id_marca = m.id_marca
LEFT JOIN 
    marcas sm ON p.id_submarca = sm.id_marca
JOIN 
    variantes v ON p.id_producto = v.id_producto 
JOIN 
    variantes_tipo_precio vtp ON p.id_producto = vtp.id_producto 
WHERE 
    m.nombre = 'saphirus'
GROUP BY 
    p.id_producto, p.nombre, p.descripcion, c.nombre, sm.nombre;
");
$sql_productos_saphirus->execute();
$productos_saphirus = $sql_productos_saphirus->fetchAll(PDO::FETCH_ASSOC);



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
                                    <h3 class="card-title">VISHNU - SAGRADA MADRE - ILUMINARTE</h3>
                                </div>
                                <!-- /.card-header -->
                                <div class="card-body">
                                    <table id="producto_no_saphirus" class="table table-bordered table-striped">
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
                                            <?php foreach ($productos_no_saphirus as $producto) { ?>
                                                <tr>
                                                    <td><?php echo $producto["id_producto"] ?></td>
                                                    <td><?php echo $producto["nombre"] ?></td>
                                                    <td><?php echo $producto["marca"] ?></td>
                                                    <td><?php echo $producto["categoria"] ?></td>
                                                    <td><?php echo $producto["precio_minorista"] ?></td>
                                                    <td><?php echo $producto["precio_mayorista"] ?></td>
                                                    <td><?php echo $producto["cantidad_variantes"] ?></td>
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
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">SAPHIRUS - AMBAR - SHINY - MILANO - </h3>
                                </div>
                                <!-- /.card-header -->
                                <div class="card-body">
                                    <table id="producto_saphirus" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Nombre</th>
                                                <th>Marca</th>
                                                <th>Submarca</th>
                                                <th>Categoria</th>
                                                <th>Variantes</th>
                                                <th>Precio min </th>
                                                <th>Precio may 6 </th>
                                                <th>Precio may 48 </th>
                                                <th>Precio may 120</th>
                                                <th>Accion</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($productos_saphirus as $producto) { ?>
                                                <tr>
                                                    <td><?php echo $producto["id_producto"] ?></td>
                                                    <td><?php echo $producto["nombre"] ?></td>
                                                    <td><?php echo $producto["marca"] ?></td>
                                                    <td><?php echo $producto["submarca"] ?></td>
                                                    <td><?php echo $producto["categoria"] ?></td>
                                                    <td><?php echo $producto["cantidad_variantes"] ?></td>
                                                    <td><?php echo $producto["precio_minorista"] ?></td>
                                                    <td><?php echo $producto["precio_mayorista_6"] ?></td>
                                                    <td><?php echo $producto["precio_mayorista_48"] ?></td>
                                                    <td><?php echo $producto["precio_mayorista_120"] ?></td>
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
                                                <th>Submarca</th>
                                                <th>Categoria</th>
                                                <th>Variantes</th>
                                                <th>Precio min </th>
                                                <th>Precio may 6 </th>
                                                <th>Precio may 48 </th>
                                                <th>Precio may 120</th>
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
            $("#producto_no_saphirus").DataTable({
                "responsive": true,
                "lengthChange": false,
                "autoWidth": false,
                "buttons": ["copy", "excel", "pdf", "print", "colvis"]
            }).buttons().container().appendTo('#producto_no_saphirus_wrapper .col-md-6:eq(0)');
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
        $(function() {
            $("#producto_saphirus").DataTable({
                "responsive": true,
                "lengthChange": false,
                "autoWidth": false,
                "buttons": ["copy", "excel", "pdf", "print", "colvis"]
            }).buttons().container().appendTo('#producto_saphirus_wrapper .col-md-6:eq(0)');
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
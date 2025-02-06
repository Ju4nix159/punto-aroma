<?php
include './header.php';
include './config/sbd.php';
include './aside.php';
include './footer.php';

$sql_pedidos = $con->prepare("SELECT p.id_pedido, p.total, p.fecha, u.email AS nombre_usuario, ep.nombre AS estado_pedido
FROM pedidos p
JOIN usuarios u ON p.id_usuario = u.id_usuario
JOIN estados_pedidos ep ON p.id_estado_pedido = ep.id_estado_pedido;");
$sql_pedidos->execute();
$pedidos = $sql_pedidos->fetchAll(PDO::FETCH_ASSOC);

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
                            <h1 class="m-0">Gestion Pedidos</h1>
                        </div><!-- /.col -->
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="./admin.php">Home</a></li>
                                <li class="breadcrumb-item active">Pedidos</>
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
                                                <th>ID Pedido</th>
                                                <th>Cliente</th>
                                                <th>Fecha</th>
                                                <th>Total</th>
                                                <th>Estado</th>
                                                <th>Accion</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($pedidos as $pedido) { ?>
                                                <tr>
                                                    <td><?php echo $pedido["id_pedido"] ?></td>
                                                    <td><?php echo $pedido["nombre_usuario"] ?></td>
                                                    <td><?php echo $pedido["fecha"] ?></td>
                                                    <td><?php echo $pedido["total"] ?></td>
                                                    <td>
                                                        <span class="order-status status-<?php echo $pedido["estado_pedido"] ?>">
                                                            <?php echo $pedido["estado_pedido"] ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                    <td>
                                                        <a href="resumen_pedido.php?id_pedido=<?php echo $pedido["id_pedido"] ?>" type="button" class="btn bg-blue btn-flat margin"><i class="fas fa-eye"></i></a>
                                                        <button onclick="printOrder(<?php echo $pedido['id_pedido'] ?>)" type="button" class="btn bg-green btn-flat margin"><i class="fas fa-print"></i></button>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th>ID Pedido</th>
                                                <th>Cliente</th>
                                                <th>Fecha</th>
                                                <th>Total</th>
                                                <th>Estado</th>
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
            <!-- Main content -->
        </div> <!-- /.content-wrapper -->
    </div><!-- ./wrapper -->
    <script>
        function printOrder(orderId) {
            // Open the print template in a new window
            let printWindow = window.open('print_order.php?id_pedido=' + orderId, '_blank');
            // Automatically trigger print when the page loads
            printWindow.onload = function() {
                printWindow.print();
            };
        }
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
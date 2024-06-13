<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}

$ordenCompraID = $_GET['orden_id'];

// Obtener detalles de la orden de compra
$stmtDetallesCompra = $conn->prepare("EXEC ObtenerDetallesCompraPorOrden :OrdenCompraID");
$stmtDetallesCompra->bindParam(':OrdenCompraID', $ordenCompraID);
$stmtDetallesCompra->execute();
$detallesCompra = $stmtDetallesCompra->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle de Compra</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1 class="text-center">Detalle de Compra #<?php echo $ordenCompraID; ?></h1>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Producto ID</th>
                    <th>Nombre</th>
                    <th>Cantidad</th>
                    <th>Precio Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($detallesCompra as $detalle): ?>
                    <tr>
                        <td><?php echo $detalle['ProductoID']; ?></td>
                        <td><?php echo $detalle['Nombre']; ?></td>
                        <td><?php echo $detalle['Cantidad']; ?></td>
                        <td><?php echo $detalle['PrecioTotal']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div class="mt-4">
            <a href="detalles_compra.php" class="btn btn-info">Volver a Detalles de compras</a>
        </div>
    </div>
</body>
</html>

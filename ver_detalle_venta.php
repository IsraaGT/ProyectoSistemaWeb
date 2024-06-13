<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}

$ordenVentaID = $_GET['orden_id'];

// Obtener detalles de la orden de venta
$stmtDetallesVenta = $conn->prepare("EXEC ObtenerDetallesVentaPorOrden :OrdenVentaID");
$stmtDetallesVenta->bindParam(':OrdenVentaID', $ordenVentaID, PDO::PARAM_INT);
$stmtDetallesVenta->execute();
$detallesVenta = $stmtDetallesVenta->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle de Venta</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1 class="text-center">Detalle de Venta #<?php echo htmlspecialchars($ordenVentaID); ?></h1>
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
                <?php foreach ($detallesVenta as $detalle): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($detalle['ProductoID']); ?></td>
                        <td><?php echo htmlspecialchars($detalle['Nombre']); ?></td>
                        <td><?php echo htmlspecialchars($detalle['Cantidad']); ?></td>
                        <td><?php echo htmlspecialchars($detalle['PrecioTotal']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div class="mt-4">
            <a href="detalles_venta.php" class="btn btn-info">Volver a Detalles de Ventas</a>
        </div>
    </div>
</body>
</html>

<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}

$itemsPerPage = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $itemsPerPage;

// Obtener órdenes de venta con paginación
$stmtOrdenesVenta = $conn->prepare("EXEC ObtenerVentasPaginadas :Offset, :FetchNext");
$stmtOrdenesVenta->bindParam(':Offset', $offset, PDO::PARAM_INT);
$stmtOrdenesVenta->bindParam(':FetchNext', $itemsPerPage, PDO::PARAM_INT);
$stmtOrdenesVenta->execute();
$ordenesVenta = $stmtOrdenesVenta->fetchAll(PDO::FETCH_ASSOC);

// Contar el total de órdenes de venta
$stmtCount = $conn->prepare("SELECT COUNT(*) AS Total FROM OrdenVentas");
$stmtCount->execute();
$totalItems = $stmtCount->fetch(PDO::FETCH_ASSOC)['Total'];
$totalPages = ceil($totalItems / $itemsPerPage);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles de Ventas</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1 class="text-center">Detalles de Ventas</h1>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Orden Venta ID</th>
                    <th>Empresa ID</th>
                    <th>Cliente ID</th>
                    <th>Fecha</th>
                    <th>Status</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($ordenesVenta as $orden): ?>
                    <tr>
                        <td><?php echo $orden['OrdenVentaID']; ?></td>
                        <td><?php echo $orden['EmpresaID']; ?></td>
                        <td><?php echo $orden['ClienteID']; ?></td>
                        <td><?php echo $orden['Fecha']; ?></td>
                        <td><?php echo $orden['Status']; ?></td>
                        <td>
                            <a href="ver_detalle_venta.php?orden_id=<?php echo $orden['OrdenVentaID']; ?>" class="btn btn-info">Ver Detalle</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <nav>
            <ul class="pagination justify-content-center">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?php if ($i == $page) echo 'active'; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
            <a href="ventas.php" class="btn btn-info">Volver.</a>
        </nav>
    </div>
</body>
</html>

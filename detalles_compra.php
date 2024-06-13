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

// Obtener órdenes de compra con paginación
$stmtOrdenesCompra = $conn->prepare("EXEC ObtenerComprasPaginadas :Offset, :FetchNext");
$stmtOrdenesCompra->bindParam(':Offset', $offset, PDO::PARAM_INT);
$stmtOrdenesCompra->bindParam(':FetchNext', $itemsPerPage, PDO::PARAM_INT);
$stmtOrdenesCompra->execute();
$ordenesCompra = $stmtOrdenesCompra->fetchAll(PDO::FETCH_ASSOC);

// Contar el total de órdenes de compra
$stmtCount = $conn->prepare("SELECT COUNT(*) AS Total FROM OrdenCompra");
$stmtCount->execute();
$totalItems = $stmtCount->fetch(PDO::FETCH_ASSOC)['Total'];
$totalPages = ceil($totalItems / $itemsPerPage);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles de Compras</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1 class="text-center">Detalles de Compras</h1>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Orden Compra ID</th>
                    <th>Empresa ID</th>
                    <th>Proveedor ID</th>
                    <th>Fecha</th>
                    <th>Status</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($ordenesCompra as $orden): ?>
                    <tr>
                        <td><?php echo $orden['OrdenCompraID']; ?></td>
                        <td><?php echo $orden['EmpresaID']; ?></td>
                        <td><?php echo $orden['ProveedorID']; ?></td>
                        <td><?php echo $orden['Fecha']; ?></td>
                        <td><?php echo $orden['Status']; ?></td>
                        <td>
                            <a href="ver_detalle_compra.php?orden_id=<?php echo $orden['OrdenCompraID']; ?>" class="btn btn-info">Ver Detalle</a>
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
            <a href="compras.php" class="btn btn-info">Volver.</a>

        </nav>
    </div>
</body>
</html>
